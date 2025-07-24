<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '../config/database.php';

$user_id = $_SESSION['user_id'];
$result = $mysqli->query("SELECT app_id, type, current_status, updated_at FROM applications WHERE user_id = $user_id ORDER BY updated_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resident Dashboard</title>
    <link rel="stylesheet" href="/municipal-e-services/assets/css/dashboard.css">
    <script src="/municipal-e-services/assets/js/resident_dashboard.js"></script>
</head>
<body>
  <div id="notifBackdrop" onclick="closeModal()"></div>
  <div id="notifModal">
      <div id="notifContent"></div>
  </div>

  <section class="dashboard-container">
    <div class="dashboard-box">
      <h2>Welcome, <span class="username"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></h2>
      <ul class="dashboard-menu">
        <li><a href="/municipal-e-services/views/form_application.php">📄 Apply for Permit</a></li>
        <li><a href="/municipal-e-services/views/status_tracker.php">📋 View My Applications</a></li>
        <li>
            <a href="#" onclick="fetchNotifications(true)">🔔 View Notifications
                <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
            </a>
        </li>
        <li><a href="/municipal-e-services/logout.php">🚪 Logout</a></li>
      </ul>
      <h3 class="apps-heading">Your Latest Applications</h3>
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="apps-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="app-card">
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
                        <p class="status">Status: <?php echo htmlspecialchars($row['current_status']); ?></p>
                        <p><small>Last Updated: <?php echo htmlspecialchars($row['updated_at']); ?></small></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-apps">No applications found. Start by submitting a new one.</p>
        <?php endif; ?>
    </div>
  </section>
</body>
</html>
