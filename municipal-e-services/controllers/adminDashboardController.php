<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';  // Ensure $mysqli is connected

header('Content-Type: application/json');

$stmt = $mysqli->prepare("
    SELECT 
        COUNT(*) AS total_applications,
        SUM(CASE WHEN current_status = 'Filed' THEN 1 ELSE 0 END) AS filed,
        SUM(CASE WHEN current_status = 'Under Review' THEN 1 ELSE 0 END) AS under_review,
        SUM(CASE WHEN current_status = 'Approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN current_status = 'Rejected' THEN 1 ELSE 0 END) AS rejected
    FROM applications
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(['success' => true, 'stats' => $result]);
exit();
