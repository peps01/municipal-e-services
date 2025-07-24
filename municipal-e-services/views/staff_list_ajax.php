<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id']!=1) {
  http_response_code(403);
  exit;
}
require_once __DIR__ . '/../config/database.php';

$type = $_GET['type'] ?? '';

if ($type === 'staff') {
    $res = $mysqli->query("SELECT user_id, full_name, email, phone, created_at FROM users WHERE role_id=2 ORDER BY created_at DESC");
    $out = [];
    while ($r = $res->fetch_assoc()) $out[] = $r;
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

if ($type === 'logs') {
    $staff = intval($_GET['staff_id']);
    $sql = "
      SELECT s.status, s.changed_at, a.type application_type,
             u_app.full_name application_owner, u_staff.full_name staff_name
      FROM statuses s
      JOIN applications a ON s.app_id=a.app_id
      JOIN users u_app ON a.user_id=u_app.user_id
      JOIN users u_staff ON s.changed_by=u_staff.user_id
      WHERE u_staff.role_id=2 " .
      ($staff ? " AND s.changed_by=$staff " : "") .
      " ORDER BY s.changed_at DESC";
    $res = $mysqli->query($sql);
    $out = [];
    while ($r = $res->fetch_assoc()) $out[] = $r;
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = intval($_POST['user_id']);
    $fn = $mysqli->real_escape_string($_POST['full_name']);
    $em = $mysqli->real_escape_string($_POST['email']);
    $ph = $mysqli->real_escape_string($_POST['phone']);
    $mysqli->query("UPDATE users SET full_name='$fn', email='$em', phone='$ph' WHERE user_id=$uid AND role_id=2");
    echo json_encode(['success' => $mysqli->affected_rows >= 0]);
    exit;
}
