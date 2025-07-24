<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Tracker</title>
    <link rel="stylesheet" href="/municipal-e-services/assets/status.css">
    <script src="/municipal-e-services/assets/js/status.js"></script>
</head>
<body>

<div class="tracker-container">
    <h2>Track My Applications</h2>
    <a href="../dashboard.php" class="back-link">← Back to Dashboard</a>
    <input type="text" placeholder="Search by type or status..." onkeyup="handleSearch(event)">
    <div id="applicationList"></div>
</div>
<div id="detailsModal" class="modal">
    <div id="modalContent" class="modal-content"></div>
    <button class="close-btn" onclick="closeModal()">Close</button>
</div>
<script>
  const latestApplications = <?php echo json_encode($result->fetch_all(MYSQLI_ASSOC)); ?>;
</script>
</body>
</html>

