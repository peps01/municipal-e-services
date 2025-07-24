// Fetch all applications (for staff)
async function fetchApplications(keyword = '') {
    const formData = new FormData();
    formData.append('action', 'fetch_applications');
    formData.append('search', keyword);

    try {
        const res = await fetch('../controllers/ApplicationController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        const list = document.getElementById('applicationList');
        list.innerHTML = '';

        if (data.success && data.applications.length > 0) {
            data.applications.forEach(app => {
                const item = document.createElement('div');
                item.className = 'application-card';
                item.innerHTML = `
            <p><strong>Resident:</strong> ${app.full_name}</p>
            <p><strong>Type:</strong> ${app.type}</p>
            <p><strong>Status:</strong> ${app.current_status}</p>
            <p><strong>Remarks:</strong> ${app.remarks || 'None'}</p>
            <p><strong>Submitted:</strong> ${app.submitted_at}</p>
            <button onclick="showApplicationDetails(${app.app_id})" class="button-link">
                View Details
            </button>`;
                list.appendChild(item);
            });
        } else {
            list.innerHTML = '<p>No applications found.</p>';
        }
    } catch (error) {
        console.error('Error fetching applications:', error);
        document.getElementById('applicationList').innerHTML = '<p>Error loading applications.</p>';
    }
}

function handleSearch(e) {
    fetchApplications(e.target.value);
}

window.onload = () => fetchApplications();

// Assuming you have a global variable or a function to get the current user role:
const currentUserRole = window.currentUserRole || 'staff'; // or 'admin'

async function showApplicationDetails(appId) {
    try {
        const res = await fetch(`/municipal-e-services/views/application_details.php?app_id=${appId}&as_json=1`);
        const data = await res.json();

        if (!data.success) {
            showNotification(data.message || 'Failed to load details.', true);
            return;
        }

        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('modalContent');

        let html = `
            <p><strong>Resident:</strong> ${data.full_name}</p>
            <p><strong>Type:</strong> ${data.type}</p>
            <p><strong>Purpose:</strong> ${data.purpose}</p>
            <p><strong>Submitted:</strong> ${data.submitted_at}</p>
            <h4>Details:</h4><ul>`;

        for (const [key, val] of Object.entries(data.extra_data || {})) {
            const label = key.charAt(0).toUpperCase() + key.slice(1);
            html += `<li><strong>${label}:</strong> ${val}</li>`;
        }

        html += `</ul><h4>Documents:</h4><ul>`;
        data.documents.forEach(doc => {
            html += `<li><a href="${doc.file_path}" download>${doc.file_name}</a></li>`;
        });

        html += `</ul>`;

        if (data.qr_value) {
            html += `<h4>QR Code:</h4><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(data.qr_value)}" alt="QR Code">`;
        }

        // 🔒 If approved, make it read-only
        if (data.current_status === 'Approved') {
            html += `<p style="margin-top: 1rem; color: green;"><strong>Status:</strong> Approved ✅</p>`;
        } else {
            // Editable only if not approved
            let statusOptions = ['Filed', 'Under Review', 'Returned'];

            // Allow staff direct approve/reject for specific types
            if (currentUserRole === 'admin' ||
                (currentUserRole === 'staff' &&
                    (data.type === 'Residency Certificate' || data.type === 'Indigency Certificate'))
            ) {
                statusOptions.push('Approved', 'Rejected');
            }

            html += `
                <hr>
                <label for="statusSelect"><strong>Status:</strong></label>
                <select id="statusSelect">`;

            statusOptions.forEach(status => {
                const selected = status === data.current_status ? 'selected' : '';
                html += `<option value="${status}" ${selected}>${status}</option>`;
            });

            html += `</select>

                <label for="remarksInput"><strong>Remarks:</strong></label>
                <textarea id="remarksInput" rows="4" style="width: 100%;">${data.remarks || ''}</textarea>
            `;

            if (data.current_status === 'Under Review') {
                html += `
                    <button id="forwardAdminBtn" style="
                        background: #004aad;
                        color: white;
                        border: none;
                        padding: 0.75rem 1rem;
                        border-radius: 5px;
                        font-weight: bold;
                        margin-top: 1rem;
                        cursor: pointer;
                    ">Forward to Admin</button>
                `;
            }

            html += `
                <button id="saveChangesBtn">Save Changes</button>

                <hr>
                <h4>Upload Additional Documents</h4>
                <input type="file" id="additionalDocs" multiple /><br><br>

                <label for="reuploadMessage"><strong>Request Re-upload Message (optional):</strong></label><br>
                <textarea id="reuploadMessage" rows="3" style="width: 100%;" placeholder="Explain what documents to re-upload or other instructions..."></textarea><br>

                <button id="uploadDocsBtn">Upload Documents & Send Request</button>
            `;
        }

        content.innerHTML = html;
        modal.style.display = 'block';
        modal.scrollTop = 0;
        content.scrollTop = 0;

        // Attach handlers only if editable
        if (data.current_status !== 'Approved') {
            document.getElementById('saveChangesBtn').onclick = () => saveApplicationChanges(appId);
            document.getElementById('uploadDocsBtn').onclick = () => uploadAdditionalDocuments(appId);
            if (data.current_status === 'Under Review') {
                document.getElementById('forwardAdminBtn').onclick = () => forwardToAdmin(appId);
            }
        }

    } catch (err) {
        console.error(err);
        showNotification('Failed to load details.', false);
    }
}

async function saveApplicationChanges(appId) {
    const status = document.getElementById('statusSelect').value;
    const remarks = document.getElementById('remarksInput').value.trim();

    const formData = new FormData();
    formData.append('action', 'update_application_status');
    formData.append('app_id', appId);
    formData.append('current_status', status);
    formData.append('remarks', remarks);

    try {
        const res = await fetch('../controllers/ApplicationController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            showNotification('Application updated successfully.', true);
            closeModal();
            fetchApplications(); // Refresh list to reflect changes
        } else {
            showNotification(data.message || 'Failed to update application.', false);
        }
    } catch (error) {
        console.error('Error updating application:', error);
        showNotification('Error updating application.', false);
    }
}

async function uploadAdditionalDocuments(appId) {
    const filesInput = document.getElementById('additionalDocs');
    const reuploadMsg = document.getElementById('reuploadMessage').value.trim();

    if (filesInput.files.length === 0 && reuploadMsg === '') {
        showNotification('Please select files to upload or enter a re-upload request message.', false);
        return;
    }

    const formData = new FormData();
    formData.append('action', 'upload_additional_documents');
    formData.append('app_id', appId);
    formData.append('reupload_message', reuploadMsg);

    for (let i = 0; i < filesInput.files.length; i++) {
        formData.append('documents[]', filesInput.files[i]);
    }

    try {
        const res = await fetch('../controllers/ApplicationController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            showNotification('Documents uploaded and request sent successfully.', true);
            // Clear inputs
            filesInput.value = '';
            document.getElementById('reuploadMessage').value = '';
            closeModal();
            fetchApplications(); // Refresh list if needed
        } else {
            showNotification(data.message || 'Failed to upload documents.', true);
        }
    } catch (error) {
        console.error('Error uploading documents:', error);
        showNotification('Error uploading documents.', true);
    }
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    }
});

function showNotification(message, isSuccess = true) {
    const container = document.getElementById('notificationContainer');
    const notif = document.createElement('div');
    notif.className = `notification-message ${isSuccess ? 'notification-success' : 'notification-error'}`;
    notif.textContent = message;

    container.appendChild(notif);

    // Remove after 3 seconds
    setTimeout(() => {
        notif.style.animation = 'fadeOut 5s ease forwards';
        setTimeout(() => notif.remove(), 5000);
    }, 5000);
}
async function forwardToAdmin(appId) {
    const confirmAction = confirm('Are you sure you want to forward this application to the admin for approval?');
    if (!confirmAction) return;

    const formData = new FormData();
    formData.append('action', 'forward_to_admin');
    formData.append('app_id', appId);

    try {
        const res = await fetch('../controllers/ApplicationController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            showNotification('Application forwarded to admin successfully.', true);
            closeModal();
            fetchApplications();
        } else {
            showNotification(data.message || 'Failed to forward application.', false);
        }
    } catch (error) {
        console.error('Error forwarding application:', error);
        showNotification('Error forwarding application.', false);
    }
}