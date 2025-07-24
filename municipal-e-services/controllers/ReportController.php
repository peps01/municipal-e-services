<?php
session_start();
require_once __DIR__.'/../config/database.php';
header('Content-Type: application/json');

// Only staff or admin
if (!isset($_SESSION['user_id'], $_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Accept optional date filters
$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

// Build query to count statuses
$query = "SELECT current_status, COUNT(*) as count FROM applications";
$params = [];
if ($from && $to) {
    $query .= " WHERE submitted_at BETWEEN ? AND ?";
    $params = [$from, $to];
}
$query .= " GROUP BY current_status";

$stmt = $mysqli->prepare($query);
if ($from && $to) {
    $stmt->bind_param("ss", $from, $to);
}
$stmt->execute();
$res = $stmt->get_result();

$stats = [];
while ($row = $res->fetch_assoc()) {
    $stats[$row['current_status']] = intval($row['count']);
}

echo json_encode([
    'success' => true,
    'stats' => $stats
]);
