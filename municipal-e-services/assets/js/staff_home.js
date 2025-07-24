function navigateTo(page) {
    switch (page) {
        case 'viewApplications':
            window.location.href = 'view_applications.php';
            break;
        case 'generateReport':
            window.location.href = 'generate_report.php';
            break;
        case 'notifications':
            openNotifModal();
            break;
        case 'uploadDocuments':
            window.location.href = 'upload_documents.php';
            break;
        default:
            console.log('Invalid page selected');
    }
}

let modalOpen = false;

async function fetchNotifications(showModal = true) {
    const res = await fetch('/municipal-e-services/controllers/notificationController.php?action=fetch_notifications');
    const data = await res.json();
    const badge = document.getElementById('notifBadge');

    if (data.success) {
        const unreadCount = data.notifications.length;

        // Safe badge update
        if (badge) {
            if (unreadCount > 0) {
                badge.innerText = unreadCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        if (showModal) {
            displayModal(data.notifications);
            if (unreadCount > 0) {
                await fetch('/municipal-e-services/controllers/notificationController.php?action=mark_seen');
                if (badge) badge.style.display = 'none'; // Reset safely
            }
        }
    } else {
        if (badge) badge.style.display = 'none';
    }
}

function displayModal(notifications) {
    const modal = document.getElementById('notifModal');
    const backdrop = document.getElementById('notifBackdrop');

    modal.innerHTML = '<span class="notifClose" onclick="closeNotifModal()">×</span><h3>Notifications</h3>';

    if (notifications.length === 0) {
        modal.innerHTML += '<p>No new notifications.</p>';
    } else {
        notifications.forEach(notif => {
            modal.innerHTML += `<div class="notifItem">${notif.message} <br><small>${notif.created_at}</small><hr></div>`;
        });
    }

    modal.style.display = 'block';
    backdrop.style.display = 'block';
    modalOpen = true;
}

function closeNotifModal() {
    document.getElementById('notifModal').style.display = 'none';
    document.getElementById('notifBackdrop').style.display = 'none';
    modalOpen = false;
}

window.onload = () => {
    fetchNotifications(false);
    setInterval(() => fetchNotifications(false), 10000);
};