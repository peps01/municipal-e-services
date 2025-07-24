<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Ensure that user is either staff (role 2), admin (role 1), or resident (role 3)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2, 3])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

// Check if app_id is provided in the request
if (!isset($_GET['app_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$app_id = intval($_GET['app_id']);

// Handle different roles
if ($_SESSION['role_id'] == 3) {
    // Resident: can only access their own application
    $stmt = $mysqli->prepare("
        SELECT a.*, u.full_name 
        FROM applications a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.app_id = ? AND a.user_id = ?
    ");
    $stmt->bind_param("ii", $app_id, $_SESSION['user_id']);
} else {
    // Staff/Admin: can access any application
    $stmt = $mysqli->prepare("
        SELECT a.*, u.full_name 
        FROM applications a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.app_id = ?
    ");
    $stmt->bind_param("i", $app_id);
}

// Execute the query and fetch the result
$stmt->execute();
$result = $stmt->get_result();

// If no result is found
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Application not found']);
    exit();
}

$app = $result->fetch_assoc();
$stmt->close();

// Fetch uploaded documents
$docs = [];
$stmt_docs = $mysqli->prepare("SELECT file_name, file_path FROM documents WHERE app_id = ?");
$stmt_docs->bind_param("i", $app_id);
$stmt_docs->execute();
$res_docs = $stmt_docs->get_result();
while ($doc = $res_docs->fetch_assoc()) {
    $docs[] = $doc;
}
$stmt_docs->close();

// Get QR code if application is approved
$qr_value = null;
if ($app['current_status'] === 'Approved') {
    $stmt_qr = $mysqli->prepare("SELECT qr_value FROM qr_codes WHERE app_id = ?");
    $stmt_qr->bind_param("i", $app_id);
    $stmt_qr->execute();
    $res_qr = $stmt_qr->get_result();
    if ($qr = $res_qr->fetch_assoc()) {
        $qr_value = $qr['qr_value'];
    }
    $stmt_qr->close();
}

// Decode extra data (if any) stored in JSON format
$extra_data = json_decode($app['extra_data'], true);

// Prepare the response
$response = [
    'success' => true,
    'app_id' => $app['app_id'],
    'full_name' => $app['full_name'],
    'type' => $app['type'],
    'purpose' => $app['purpose'],
    'current_status' => $app['current_status'],
    'remarks' => $app['remarks'],
    'submitted_at' => $app['submitted_at'],
    'extra_data' => $extra_data,
    'documents' => $docs,
    'qr_value' => $qr_value
];

// Output the JSON response
echo json_encode($response);
?>
