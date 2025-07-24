<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

header('Content-Type: application/json');

// Add error logging function
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0775, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}

$userModel = new User($mysqli);
$response = ['success' => false, 'message' => 'Invalid Email or Password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'register') {
            $full_name = trim($_POST['full_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = $_POST['password'];

            // Input validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Invalid email format.'];
                echo json_encode($response);
                exit();
            }

            if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
                $response = ['success' => false, 'message' => 'Invalid phone number.'];
                echo json_encode($response);
                exit();
            }

            $result = $userModel->register($full_name, $email, $phone, $password);

            if ($result === true) {
                $response = ['success' => true, 'message' => 'Registration successful. Redirecting to login...', 'redirect' => 'login.php'];
            } elseif ($result === 'exists') {
                $response = ['success' => false, 'message' => 'Email or phone already exists.'];
            } else {
                $response = ['success' => false, 'message' => 'Registration failed'];
            }

        } elseif ($action === 'login') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = $userModel->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role_id'] = $user['role_id'];

                if ($user['role_id'] == 1) {
                    $redirect = 'views/admin_dashboard.php';
                } elseif ($user['role_id'] == 3) {
                    $redirect = 'dashboard.php';
                } else {
                    $redirect = 'views/staff_home.php'; // default
                }

                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $redirect
                ];
            }
        }

    } catch (Exception $e) {
        // Log the error and return a helpful message
        logError("Error in action '$action': " . $e->getMessage());
        $response = ['success' => false, 'message' => 'An internal error occurred. Please try again later.'];
    }
}

echo json_encode($response);
?>
