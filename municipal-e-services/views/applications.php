<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // Role ID 1 for Admin
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
  <title>Admin - Manage Applications</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/css/admin_applications.css">
  <script src="/municipal-e-services/assets/js/admin_application.js"></script>
</head>
<body>
  <div class="tracker-container">
    <h2>Manage Applications</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</p>
    <a class="home-link" href="admin_dashboard.php">← Back to Dashboard</a>

    <!-- Search box for filtering applications -->
    <input type="text" id="searchInput" placeholder="Search applications..." oninput="handleSearch(event)" />

    <!-- Container for displaying application cards -->
    <div id="applicationList">
      <p>Loading applications...</p>
    </div>
  </div>

  <!-- Modal for application details -->
  <div id="detailsModal" class="modal">
    <div id="modalContent" class="modal-content"></div>
    <button class="close-btn" onclick="closeModal()">Close</button>
  </div>
</body>
</html>
