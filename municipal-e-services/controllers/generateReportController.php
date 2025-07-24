<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {  // Allow both Admin and Staff
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}


require_once '../config/database.php';
require_once '../models/Report.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'fetch_report_data') {
    $report = new Report($mysqli);
    $stats = $report->getStaffDashboardStats();

    echo json_encode(['success' => true, 'stats' => $stats]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit();
