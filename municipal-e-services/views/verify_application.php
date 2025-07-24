<?php
require_once __DIR__ . '/../config/database.php'; // Adjust path as needed

// Get app_id or token from GET parameter
$app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : 0;

if ($app_id <= 0) {
    echo "Invalid QR code or application ID.";
    exit();
}

// Prepare statement to get approved application details without personal info
$stmt = $mysqli->prepare("
    SELECT type, purpose, current_status, remarks, submitted_at, extra_data
    FROM applications
    WHERE app_id = ? AND current_status = 'Approved'
");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Application not found or not approved
    echo "Application not found or not approved yet.";
    exit();
}

$app = $result->fetch_assoc();

// Fetch associated documents if needed
$stmt_docs = $mysqli->prepare("SELECT file_name, file_path FROM documents WHERE app_id = ?");
$stmt_docs->bind_param("i", $app_id);
$stmt_docs->execute();
$result_docs = $stmt_docs->get_result();

$documents = [];
while ($doc = $result_docs->fetch_assoc()) {
    $documents[] = $doc;
}
$stmt_docs->close();

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Application Details Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; }
        h1 { text-align: center; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 8px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Application Details</h1>

    <p><span class="label">Type:</span> <?= htmlspecialchars($app['type']) ?></p>
    <p><span class="label">Purpose:</span> <?= nl2br(htmlspecialchars($app['purpose'])) ?></p>
    <p><span class="label">Status:</span> <?= htmlspecialchars($app['current_status']) ?></p>
    <p><span class="label">Remarks:</span> <?= nl2br(htmlspecialchars($app['remarks'] ?: 'None')) ?></p>
    <p><span class="label">Submitted at:</span> <?= htmlspecialchars($app['submitted_at']) ?></p>

    <?php if (!empty($app['extra_data'])): 
        $extra_data = json_decode($app['extra_data'], true);
        if (is_array($extra_data) && count($extra_data) > 0):
    ?>
        <h3>Additional Details:</h3>
        <ul>
            <?php foreach ($extra_data as $key => $val): ?>
                <li><span class="label"><?= htmlspecialchars(ucfirst($key)) ?>:</span> <?= htmlspecialchars($val) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php 
        endif;
    endif; ?>

    <?php if (count($documents) > 0): ?>
        <h3>Documents:</h3>
        <ul>
            <?php foreach ($documents as $doc): ?>
                <li><a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" download><?= htmlspecialchars($doc['file_name']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
