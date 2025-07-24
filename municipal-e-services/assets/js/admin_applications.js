async function fetchApplications(keyword = '', statusFilter = 'Pending Approval') {
    const formData = new FormData();
    formData.append('action', 'fetch_admin_applications');
    formData.append('search', keyword);
    formData.append('status_filter', statusFilter);

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
                    <button onclick="showApplicationDetails(${app.app_id}, '${app.current_status}')" class="button-link">
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

function handleSearch(event) {
    fetchApplications(event.target.value, document.getElementById('statusFilter').value);
}

function handleStatusFilter() {
    const keyword = document.getElementById('searchInput').value;
    const statusFilter = document.getElementById('statusFilter').value;
    fetchApplications(keyword, statusFilter);
}

window.onload = () => fetchApplications();

function showApplicationDetails(appId, currentStatus) {
    fetch(`/municipal-e-services/views/application_details.php?app_id=${appId}&as_json=1`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to load application details.');
                return;
            }

            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');

            let html = `
                <p><strong>Resident:</strong> ${data.full_name}</p>
                <p><strong>Type:</strong> ${data.type}</p>
                <p><strong>Purpose:</strong> ${data.purpose}</p>
                <p><strong>Status:</strong> ${
                    data.current_status === 'Approved'
                        ? '<span style="color:green;">✔ Approved</span>'
                        : data.current_status
                }</p>
                <p><strong>Submitted:</strong> ${data.submitted_at}</p>
                <h4>Details:</h4>
                <ul>`;

            for (const [key, val] of Object.entries(data.extra_data || {})) {
                const label = key.charAt(0).toUpperCase() + key.slice(1);
                html += `<li><strong>${label}:</strong> ${val}</li>`;
            }

            html += '</ul>';

            html += '<h4>Documents:</h4><ul>';
            data.documents.forEach(doc => {
                html += `<li><a href="${doc.file_path}" download>${doc.file_name}</a></li>`;
            });
            html += '</ul>';

            if (currentStatus === 'Pending Approval') {
                html += `
                    <label for="remarksInput"><strong>Remarks (Admin Editable):</strong></label>
                    <textarea id="remarksInput" rows="4" style="width: 100%;">${data.remarks || ''}</textarea>
                    <button id="approveBtn">Approve</button>
                    <button id="rejectBtn" style="background:#dc3545; color:white;">Reject</button>
                `;
            }

            // Show QR Code status with check or X
            html += '<p><strong>QR Code Generated:</strong> ';

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
            modal.scrollTop = 0;
            content.scrollTop = 0;

            if (currentStatus === 'Pending Approval') {
                document.getElementById('approveBtn').onclick = () => confirmAndSave(appId, 'Approved');
                document.getElementById('rejectBtn').onclick = () => confirmAndSave(appId, 'Rejected');
            }
        });
}

// function showApplicationDetails(appId, currentStatus) {
//     fetch(`/municipal-e-services/views/application_details.php?app_id=${appId}&as_json=1`)
//         .then(res => res.json())
//         .then(data => {
//             if (!data.success) {
//                 alert('Failed to load application details.');
//                 return;
//             }

//             const modal = document.getElementById('detailsModal');
//             const content = document.getElementById('modalContent');

//             let html = `
//                 <p><strong>Resident:</strong> ${data.full_name}</p>
//                 <p><strong>Type:</strong> ${data.type}</p>
//                 <p><strong>Purpose:</strong> ${data.purpose}</p>
//                 <p><strong>Status:</strong> ${data.current_status}</p>
//                 <p><strong>Submitted:</strong> ${data.submitted_at}</p>
//                 <h4>Details:</h4>
//                 <ul>`;

//             for (const [key, val] of Object.entries(data.extra_data || {})) {
//                 const label = key.charAt(0).toUpperCase() + key.slice(1);
//                 html += `<li><strong>${label}:</strong> ${val}</li>`;
//             }

//             html += '</ul>';

//             html += '<h4>Documents:</h4><ul>';
//             data.documents.forEach(doc => {
//                 html += `<li><a href="${doc.file_path}" download>${doc.file_name}</a></li>`;
//             });
//             html += '</ul>';

//             if (currentStatus === 'Pending Approval') {
//                 html += `
//                     <label for="remarksInput"><strong>Remarks (Admin Editable):</strong></label>
//                     <textarea id="remarksInput" rows="4" style="width: 100%;">${data.remarks || ''}</textarea>
//                     <button id="approveBtn">Approve</button>
//                     <button id="rejectBtn" style="background:#dc3545; color:white;">Reject</button>
//                 `;
//             }
//             if (data.current_status === 'Approved') {
//                 // Show green checkmark instead of default status text
//                 html += '<p><strong>Status:</strong> <span style="color:green;">✔ Approved</span></p>';
//             }

//             // Show QR Code status with check or X
//             html += '<p><strong>QR Code Generated:</strong> ';

//             if (data.qr_value) {
//                 // If QR code exists, show checkmark and the QR code image
//                 html += '<span style="color:green;">✔</span></p>';
//                 html += `<h4>QR Code:</h4><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(data.qr_value)}" alt="QR Code" />`;
//             } else {
//                 // If no QR code, show red X
//                 html += '<span style="color:red;">✘</span></p>';
//             }

//             content.innerHTML = html;
//             modal.style.display = 'block';
//             modal.scrollTop = 0;
//             content.scrollTop = 0;

//             if (currentStatus === 'Pending Approval') {
//                 document.getElementById('approveBtn').onclick = () => confirmAndSave(appId, 'Approved');
//                 document.getElementById('rejectBtn').onclick = () => confirmAndSave(appId, 'Rejected');
//             }
//         });
// }

function confirmAndSave(appId, decision) {
    if (confirm(`Are you sure you want to ${decision} this application?`)) {
        saveApplicationChanges(appId, decision);
    }
}

function saveApplicationChanges(appId, decision) {
    const remarks = document.getElementById('remarksInput').value.trim();

    const formData = new FormData();
    formData.append('action', 'update_application_status');
    formData.append('app_id', appId);
    formData.append('current_status', decision);
    formData.append('remarks', remarks);

    fetch('../controllers/ApplicationController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Application marked as ${decision}.`);
                generateQRCode(appId);
                closeModal();
                fetchApplications();
            } else {
                alert('Failed to update application.');
            }
        });
}

function downloadQR(url) {
    const link = document.createElement('a');
    link.href = url;
    link.download = 'application_qr_code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    }
});