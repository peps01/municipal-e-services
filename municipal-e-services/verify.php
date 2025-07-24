<?php
require_once __DIR__ . '/config/database.php';

$app_id = intval($_GET['app_id'] ?? 0);

if ($app_id <= 0) {
    echo "<h2>Invalid Verification Link</h2>";
    exit();
}

// Fetch application data
$stmt = $mysqli->prepare("
    SELECT a.app_id, a.type, a.purpose, a.current_status, u.full_name, q.qr_value, q.generated_at
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    LEFT JOIN qr_codes q ON a.app_id = q.app_id
    WHERE a.app_id = ?
");
$stmt->bind_param('i', $app_id);
$stmt->execute();
$result = $stmt->get_result();
$app = $result->fetch_assoc();
$stmt->close();

if (!$app) {
    echo "<h2>No Record Found</h2>";
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 20px; }
        .card { max-width: 500px; background: white; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        h2 { color: #004aad; text-align: center; }
        .status { font-weight: bold; color: green; }
        .invalid { color: red; }
    </style>
</head>
<body>

<div class="card">
    <h2>Document Verification</h2>

    <p><strong>Document Type:</strong> <?php echo htmlspecialchars($app['type']); ?></p>
    <p><strong>Resident Name:</strong> <?php echo htmlspecialchars($app['full_name']); ?></p>
    <p><strong>Purpose:</strong> <?php echo htmlspecialchars($app['purpose']); ?></p>
    <p><strong>Status:</strong>
        <?php
            if ($app['current_status'] === 'Approved') {
                echo '<span class=\"status\">VALID & APPROVED ✅</span>';
            } else {
                echo '<span class=\"invalid\">Not Approved ❌</span>';
            }
        ?>
    </p>
    <p><strong>QR Issued:</strong> <?php echo htmlspecialchars($app['generated_at'] ?: 'Not Generated'); ?></p>

    <p style=\"text-align:center; margin-top:20px; font-size:0.9rem; color:gray;\">For official verification purposes only.</p>
</div>

</body>
</html>
