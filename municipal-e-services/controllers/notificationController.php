<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unauthorized'];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$action = $_GET['action'] ?? '';

if ($action === 'fetch_notifications') {
    $stmt = $mysqli->prepare("SELECT notif_id, message, created_at FROM notifications WHERE user_id = ? AND seen = 0 ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    $response = ['success' => true, 'notifications' => $notifications];
    $stmt->close();
}

if ($action === 'mark_seen') {
    $stmt = $mysqli->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ? AND seen = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $response = ['success' => true];
}

echo json_encode($response);
?>
