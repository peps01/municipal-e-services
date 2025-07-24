<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';
// Fetch application + QR code (if any)
$stmt = $mysqli->prepare("
    SELECT a.*, u.full_name, q.qr_value
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    LEFT JOIN qr_codes q ON a.app_id = q.app_id
    WHERE a.app_id = ?
");
$stmt->bind_param('i', $app_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/municipal-e-services/assets/css/staff.css">
  <style>
    select {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-bottom: 1rem;
    width: 100%;
    max-width: 300px;
}
  </style>
  <script src="/municipal-e-services/assets/js/admin_applications.js"></script>
</head>
<body>
  <div class="tracker-container">
    <a class="home-link" href="admin_dashboard.php">← Back to Dashboard</a><br>
    <h2>Admin: Manage Applications</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</p>

    <input type="text" id="searchInput" placeholder="Search applications..." oninput="handleSearch(event)" />

    <select id="statusFilter" onchange="handleStatusFilter()" >
      <option value="Pending Approval">Pending Approval</option>
      <option value="All">All Applications</option>
      <option value="Approved">Approved</option>
      <option value="Rejected">Rejected</option>
      <option value="Under Review">Under Review</option>
      <option value="Returned">Returned</option>
      <option value="Filed">Filed</option>
    </select>

    <div id="applicationList">
      <p>Loading applications...</p>
    </div>
  </div>

  <div id="notificationContainer"></div>

  <div id="detailsModal" class="modal">
    <div id="modalContent" class="modal-content"></div>
    <button class="close-btn" onclick="closeModal()">Close</button>
  </div>
</body>
</html>
