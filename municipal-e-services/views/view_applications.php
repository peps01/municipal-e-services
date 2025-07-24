<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Application List</title>
    <link rel="stylesheet" href="/municipal-e-services/assets/css/staff.css">
    <script src="/municipal-e-services/assets/js/staff.js"></script>
</head>
<body>
  <div class="tracker-container">
    <h2>Staff Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Staff)</p>
    <a class="home-link" href="staff_home.php">← Back to Dashboard</a>

    <!-- Search box -->
    <input type="text" id="searchInput" placeholder="Search applications..." oninput="handleSearch(event)" />

    <!-- Container for app cards -->
    <div id="applicationList">
      <p>Loading applications...</p>
    </div>
  </div>
  <div id="notificationContainer"></div>
  <!-- Modal for details -->
  <div id="detailsModal" class="modal">
    <div id="modalContent" class="modal-content"></div>
    <button class="close-btn" onclick="closeModal()">Close</button>
  </div>
</body>
</html>
