<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff Dashboard Report</title>
  <link rel="stylesheet" href="../assets/css/report.css">
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
       <script src="../assets/js/report.js"></script>
</head>
<body>
  <a href="staff_home.php" class="back-link">← Back to Dashboard</a>

  <div id="staff-dashboard-report" class="dashboard-section">
    <h3>📊 Application Summary</h3>
    <p><strong>Total Applications:</strong> <span id="totalApplications">0</span></p>
    <p><strong>Filed:</strong> <span id="filedCount">0</span></p>
    <p><strong>Under Review:</strong> <span id="reviewCount">0</span></p>
    <p><strong>Returned:</strong> <span id="returnedCount">0</span></p>
    <p><strong>Approved:</strong> <span id="approvedCount">0</span></p>
    <p><strong>Rejected:</strong> <span id="rejectedCount">0</span></p>
    <p><strong>Waiting for Approval:</strong> <span id="pendingApprovalCount">0</span></p> <!-- Added line for pending approval -->

    <!-- Chart Canvas -->
<canvas id="applicationsChart" style="max-width: 100%; height: 300px; margin-top: 2rem;"></canvas>
  </div>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
