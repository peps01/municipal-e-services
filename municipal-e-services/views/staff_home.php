<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff Home</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/css/staff_home.css">
  <link rel="stylesheet" href="/municipal-e-services/assets/css/admin_dashboard.css"> <!-- Reuse Admin CSS -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="/municipal-e-services/assets/js/staff_home.js"></script>
  <script src="../assets/js/report.js"></script>
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
    <div class="dashboard-box">
      <h2>Staff Dashboard</h2>
      <p class="username">Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>

        <ul class="dashboard-menu">
            <li><a onclick="navigateTo('viewApplications')">📄 View Applications</a></li>
            <li><a href="#" onclick="openReportModal()">📊 Generate Report</a></li> <!-- Updated -->
            <li>
                <a onclick="fetchNotifications()">🔔 View Notifications
                    <?php if ($unread_count > 0): ?>
                        <span id="notifBadge" class="notif-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
  </div>
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
  <div id="reportModalBackdrop" class="modal-backdrop" onclick="closeReportModal()"></div>
</body>
</html>

