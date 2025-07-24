<?php
session_start();
require_once __DIR__ . '/../config/database.php';
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log'; // Adjust the path if needed

    // Make sure the directory exists
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0775, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message" . PHP_EOL;

    // Append the message to the log file
    file_put_contents($logFile, $entry, FILE_APPEND);
}


header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unauthorized'];

// 1. Check authentication and authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode($response);
    exit();
}

// 2. Handle POST request for registering staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_staff') {

    // Trim and sanitize input
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];
    $role_id   = 2; // staff role

    // 3. Check for empty fields
    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // 4. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }

    // 5. Validate phone number format
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number.']);
        exit();
    }

    try {
    // 1. Check if user with same email already exists
    $check = $mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
    if (!$check) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
        exit();
    }
    $check->close();

    // 2. Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insert new user
    $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, phone, password, role_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("ssssi", $full_name, $email, $phone, $hashed_password, $role_id);
    if (!$stmt->execute()) {
        // Check for duplicate email error (MySQL error code 1062)
        if ($stmt->errno === 1062) {
            throw new Exception("Email already exists.");
        }
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Staff account created successfully.']);
    $stmt->close();

} catch (Exception $e) {
    logError($e->getMessage());

    // Custom error message for duplicate email
    if (str_contains($e->getMessage(), 'Email already exists')) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}

    exit();
}

// Fallback response
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
