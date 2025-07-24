<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'];
$result = $mysqli->query("SELECT app_id, type, current_status, updated_at FROM applications WHERE user_id = $user_id ORDER BY updated_at DESC LIMIT 3");

// Fetch unread notifications
$notif_result = $mysqli->query("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = $user_id AND seen = 0");
$notif_row = $notif_result->fetch_assoc();
$unread_count = $notif_row['unread_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/municipal-e-services/assets/css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
           <script src="../assets/js/report.js"></script>
           <script src="../assets/js/admin.js"></script>
<style>
/* Backdrop Overlay */
/* Notification Badge Styling */
.notif-badge {
    position: absolute;
    top: -8px;
    right: -5px;
    background: red;
    color: white;
    font-size: 0.65rem;
    font-weight: bold;
    border-radius: 50%;
    padding: 2px 5px;
    min-width: 18px;
    text-align: center;
    display: inline-block;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

#notifBackdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 999;
    cursor: pointer;
}


/* Modal Box */


#notifModal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(1);
    background: #ffffff;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    padding: 1.5rem 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    display: none;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}


/* Modal Content Styling */

#notifContent {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1rem;
    color: #333;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 0.5rem;
}


/* Modal Header */

#notifContent h3 {
    font-size: 1.2rem;
    color: #004aad;
    font-weight: 600;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}


/* Close Button */

.notifClose {
    font-size: 1.4rem;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
    transition: color 0.2s;
    position: absolute;
    top: 10px;
    right: 15px;
}

.notifClose:hover {
    color: #004aad;
}


/* Each Notification Item */

.notifItem {
    background: #f9f9f9;
    border-left: 4px solid #004aad;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.95rem;
    color: #333;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: background 0.2s;
}

.notifItem:hover {
    background: #f1f1f1;
}


/* No Notifications Text */

#notifContent p {
    text-align: center;
    font-style: italic;
    color: #555;
    font-size: 0.95rem;
}


/* Fade Animation */

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -45%) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}
</style>

</head>
<body>
    <!-- Notification Modal Backdrop -->
    <div id="notifBackdrop" onclick="closeNotifModal()"></div>

<!-- Notification Modal -->
<div id="notifModal">
    <span class="notifClose" onclick="closeNotifModal()">×</span>
    <div id="notifContent">
        <h3>Notifications</h3>
        <div id="notifItems"></div> <!-- Notifications will be loaded here -->
        <p id="notifEmptyMessage" style="display: none;">No notifications available.</p>
    </div>
    <span id="notifBadge" class="notif-badge" style="display: <?php echo $unread_count > 0 ? 'inline-block' : 'none'; ?>">
      <?php echo $unread_count > 0 ? $unread_count : ''; ?>
  </span>
</div>
    <div class="dashboard-container">
        <div class="dashboard-box"> <!-- New wrapper like staff -->
            <h2>Department Head</h2>
            <div class="username">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>

            <ul class="dashboard-menu">
                <li><a href="/municipal-e-services/views/admin_application.php">📄 Manage Applications</a></li>
                <li><a href="/municipal-e-services/admin/generate_qr.php">📱 QR Code Generation</a></li>
                <li><a href="/municipal-e-services/views/staff_list.php">👤 Manage Staff</a></li>
                <li style="cursor: pointer;">
                    <a onclick="fetchNotifications()" style="position: relative;">
                        🔔
                        <?php if ($unread_count > 0): ?>
                            <span id="notifBadge" class="notif-badge"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                        View Notifications
                    </a>
                </li>
                <li><a href="#" onclick="openReportModal()">📊 Reports</a></li>
                <li><a href="/municipal-e-services/logout.php">🚪 Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- Backdrop -->
    <div id="reportModalBackdrop" class="modal-backdrop" onclick="closeReportModal()"></div>

    <!-- Modal -->
    <div id="reportModal" class="modal">
    <span class="close" onclick="closeReportModal()">×</span>
        <div class="modal-content">
            <h3>📊 Application Summary</h3>
            <p><strong>Total Applications:</strong> <span id="totalApplications">0</span></p>
            <p><strong>Filed:</strong> <span id="filedCount">0</span></p>
            <p><strong>Under Review:</strong> <span id="reviewCount">0</span></p>
            <p><strong>Returned:</strong> <span id="returnedCount">0</span></p>
            <p><strong>Approved:</strong> <span id="approvedCount">0</span></p>
            <p><strong>Rejected:</strong> <span id="rejectedCount">0</span></p>
            <p><strong>Waiting for Approval:</strong> <span id="pendingApprovalCount">0</span></p>

            <canvas id="applicationsChart"></canvas>
        </div>
    </div>
</body>
</html>
