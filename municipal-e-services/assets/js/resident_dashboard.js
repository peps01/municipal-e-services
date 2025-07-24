let modalOpen = false;

async function fetchNotifications(showModal = false) {
    const res = await fetch('/municipal-e-services/controllers/notificationController.php?action=fetch_notifications');
    const data = await res.json();
    const badge = document.getElementById('notifBadge');

    if (data.success) {
        const unreadCount = data.notifications.length;

        if (unreadCount > 0) {
            badge.innerText = unreadCount;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }

        if (showModal) {
            displayModal(data.notifications);

            if (unreadCount > 0) {
                await fetch('/municipal-e-services/controllers/notificationController.php?action=mark_seen');
                badge.style.display = 'none';
            }
        }
    } else {
        badge.style.display = 'none';
    }
}

function displayModal(notifications) {
    const modal = document.getElementById('notifModal');
    const content = document.getElementById('notifContent');
    const backdrop = document.getElementById('notifBackdrop');

    content.innerHTML = '<h3>Notifications <span class="notifClose" onclick="closeModal()">×</span></h3>';

    if (notifications.length === 0) {
        const empty = document.createElement('p');
        empty.innerText = 'No notifications.';
        empty.style.textAlign = 'center';
        empty.style.padding = '20px 0';
        content.appendChild(empty);
    } else {
        notifications.forEach(notif => {
            const item = document.createElement('div');
            item.className = 'notifItem';
            item.innerText = notif.message + ' (' + notif.created_at + ')';
            content.appendChild(item);
        });
    }

    modal.style.display = 'block';
    backdrop.style.display = 'block';
    modalOpen = true;
}

function closeModal() {
    document.getElementById('notifModal').style.display = 'none';
    document.getElementById('notifBackdrop').style.display = 'none';
    modalOpen = false;
}

window.onload = () => {
    fetchNotifications();
    setInterval(fetchNotifications, 10000);
};