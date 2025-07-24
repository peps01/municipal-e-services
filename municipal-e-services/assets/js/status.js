async function fetchApplications(keyword = '') {
    const formData = new FormData();
    formData.append('action', 'fetch_applications');
    formData.append('search', keyword);

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
            item.innerHTML =
                `<p><strong>Type:</strong> ${app.type}</p>
                <p><strong>Status:</strong> ${app.current_status}</p>
                <p><strong>Remarks:</strong> ${app.remarks || 'None'}</p>
                <p><strong>Date Submitted:</strong> ${app.submitted_at}</p>
                <button onclick="showApplicationDetails(${app.app_id})" style="
                    background: none;
                    border: none;
                    color: #004aad;
                    font-style: italic;
                    font-size: 1rem;
                    font-family: inherit;
                    cursor: pointer;
                    padding: 0;
                    text-decoration: none;
                ">View Details</button>`;

            list.appendChild(item);
        });
    } else {
        list.innerHTML = '<p>No applications found.</p>';
    }
}

function handleSearch(e) {
    fetchApplications(e.target.value);
}

window.onload = () => fetchApplications();

async function showApplicationDetails(appId) {
    const res = await fetch(`/municipal-e-services/views/application_details.php?app_id=${appId}&as_json=1`);
    const data = await res.json();

    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('modalContent');

    let html = `<h3>Application Details</h3>
        <p><strong>Type:</strong> ${data.type}</p>
        <p><strong>Status:</strong> ${
            data.current_status === 'Approved'
                ? '<span style="color:green;">✔ Approved</span>'
                : data.current_status
        }</p>
        <p><strong>Remarks:</strong> ${data.remarks}</p>
        <p><strong>Submitted At:</strong> ${data.submitted_at}</p>
        <h4>Details:</h4>
        <div id="editableFields">`;

    if (data.current_status === 'Returned') {
        // Editable fields for Returned applications
        html += `<label><strong>Purpose:</strong></label>
            <input type="text" id="editPurpose" value="${data.purpose}" style="width:100%;margin-bottom:10px;">`;

        html += `<h4>Update Additional Details:</h4><ul>`;
        for (const [key, value] of Object.entries(data.extra_data)) {
            html += `<li>
                <label>${key}:</label>
                <input type="text" id="field_${key}" value="${value}" style="width:100%;margin-bottom:10px;">
            </li>`;
        }
        html += `</ul>`;

        html += `<button id="resubmitBtn" style="
            background: #004aad;
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 1rem;
            cursor: pointer;
        ">Resubmit Application</button>`;
    } else {
        // Read-only display for non-returned applications
        html += `<p><strong>Purpose:</strong> ${data.purpose}</p>
            <h4>Type-Specific Details:</h4><ul>`;
        for (const [key, value] of Object.entries(data.extra_data)) {
            html += `<li><strong>${key}:</strong> ${value}</li>`;
        }
        html += `</ul>`;
    }

    html += `</div>
        <h4>Uploaded Documents:</h4><ul>`;

    data.documents.forEach(doc => {
        html += `<li><a href="${doc.file_path}" download>${doc.file_name}</a></li>`;
    });
    html += `</ul>`;

    if (data.qr_value) {
        const qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(data.qr_value)}`;

        html += `
        <h4>QR Code:</h4>
        <img id="qrImage" src="${qrImgUrl}" alt="QR Code" />
        <br/>
        <button onclick="downloadQR('${qrImgUrl}')">Download QR Code</button>
    `;
    }

    content.innerHTML = html;
    modal.style.display = 'block';

    if (data.current_status === 'Returned') {
        document.getElementById('resubmitBtn').onclick = () => resubmitApplication(appId, data.extra_data);
    }
}

function downloadQR(url) {
    const link = document.createElement('a');
    link.href = url;
    link.download = 'application_qr_code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function resubmitApplication(appId, extraDataFields) {
    const updatedPurpose = document.getElementById('editPurpose').value.trim();

    const updatedExtraData = {};
    for (const key of Object.keys(extraDataFields)) {
        const value = document.getElementById(`field_${key}`).value.trim();
        updatedExtraData[key] = value;
    }

    const formData = new FormData();
    formData.append('action', 'resubmit_application');
    formData.append('app_id', appId);
    formData.append('purpose', updatedPurpose);
    formData.append('extra_data', JSON.stringify(updatedExtraData));

    const res = await fetch('../controllers/ApplicationController.php', {
        method: 'POST',
        body: formData
    });

    const result = await res.json();
    if (result.success) {
        showPopupNotification('Application resubmitted successfully.', true);
        closeModal();
        fetchApplications();
    } else {
        showPopupNotification('Failed to resubmit.', false);
    }
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}
// close using esc
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    }
});

function showPopupNotification(message, isSuccess = true) {
    const notif = document.createElement('div');
    notif.className = `popup-notification ${isSuccess ? 'success' : 'error'}`;
    notif.textContent = message;

    notif.style.position = 'fixed';
    notif.style.top = '20px';
    notif.style.left = '50%';
    notif.style.transform = 'translateX(-50%)';
    notif.style.background = isSuccess ? '#28a745' : '#dc3545';
    notif.style.color = '#fff';
    notif.style.padding = '10px 20px';
    notif.style.borderRadius = '5px';
    notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
    notif.style.zIndex = '9999';
    notif.style.fontWeight = 'bold';

    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.opacity = '0';
        notif.style.transition = 'opacity 0.5s ease';
        setTimeout(() => notif.remove(), 500);
    }, 3000);
}