<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'submit_application') {
        $user_id = intval($_POST['user_id']);
        $type = trim($_POST['type']);
        $purpose = trim($_POST['purpose']);
        $extra_data = isset($_POST['extra_data']) ? $_POST['extra_data'] : json_encode([]);

        $stmt = $mysqli->prepare("INSERT INTO applications (user_id, type, purpose, current_status, remarks, extra_data) VALUES (?, ?, ?, 'Filed', '', ?)");
        if ($stmt) {
            $stmt->bind_param("isss", $user_id, $type, $purpose, $extra_data);
            if ($stmt->execute()) {
                $app_id = $stmt->insert_id;

                if (!empty($_FILES['documents']['name'][0])) {
                    $upload_dir = '../uploads/';
                    foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                        $file_name = basename($_FILES['documents']['name'][$key]);
                        $file_path = $upload_dir . time() . '_' . $file_name;
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $stmt_doc = $mysqli->prepare("INSERT INTO documents (app_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                            $file_type = $_FILES['documents']['type'][$key];
                            $stmt_doc->bind_param("isss", $app_id, $file_name, $file_path, $file_type);
                            $stmt_doc->execute();
                            $stmt_doc->close();
                        }
                    }
                }

                $response = ['success' => true, 'message' => 'Application submitted successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to submit application.'];
            }
            $stmt->close();
        }
    }
    
    if ($_POST['action'] === 'resubmit_application') {
        $app_id = intval($_POST['app_id']);
        $purpose = trim($_POST['purpose']);
        $extra_data = $_POST['extra_data'] ?? '{}';

        $stmt = $mysqli->prepare("UPDATE applications SET purpose = ?, extra_data = ?, current_status = 'Filed', updated_at = NOW() WHERE app_id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $purpose, $extra_data, $app_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
                exit();
            }
            $stmt->close();
        }
        echo json_encode(['success' => false, 'message' => 'Failed to resubmit application.']);
        exit();
    }

    if ($_POST['action'] === 'fetch_applications') {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            $response = ['success' => false, 'message' => 'Unauthorized access.'];
        } else {
            $role_id = intval($_SESSION['role_id']);
            $search = '%' . $mysqli->real_escape_string($_POST['search'] ?? '') . '%';

            if ($role_id === 2) {
                // Staff: Fetch all applications
                $stmt = $mysqli->prepare("
                    SELECT a.app_id, a.type, a.purpose, a.current_status, a.remarks, a.submitted_at, u.full_name
                    FROM applications a
                    JOIN users u ON a.user_id = u.user_id
                    WHERE a.type LIKE ? OR a.current_status LIKE ?
                    ORDER BY a.submitted_at DESC
                ");
                if ($stmt) {
                    $stmt->bind_param("ss", $search, $search);
                }
            } elseif ($role_id === 1) {
                // Admin: Fetch all applications (no filter on user_id)
                $stmt = $mysqli->prepare("
                    SELECT a.app_id, a.type, a.purpose, a.current_status, a.remarks, a.submitted_at, u.full_name
                    FROM applications a
                    JOIN users u ON a.user_id = u.user_id
                    WHERE a.type LIKE ? OR a.current_status LIKE ?
                    ORDER BY a.submitted_at DESC
                ");
                if ($stmt) {
                    $stmt->bind_param("ss", $search, $search);
                }
            } else {
                // Resident: Fetch only their applications
                $user_id = intval($_SESSION['user_id']);
                $stmt = $mysqli->prepare("SELECT app_id, type, purpose, current_status, remarks, submitted_at FROM applications WHERE user_id = ? AND (type LIKE ? OR current_status LIKE ?) ORDER BY submitted_at DESC");
                if ($stmt) {
                    $stmt->bind_param("iss", $user_id, $search, $search);
                }
            }

            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();

                $applications = [];
                while ($row = $result->fetch_assoc()) {
                    $applications[] = $row;
                }
                $response = ['success' => true, 'applications' => $applications];
                $stmt->close();
            }
        }
    }

    if ($_POST['action'] === 'update_application_status') {
        if (!isset($_SESSION['user_id'], $_SESSION['role_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $role_id = $_SESSION['role_id'];
        $app_id = intval($_POST['app_id'] ?? 0);
        $current_status = trim($_POST['current_status'] ?? '');
        $remarks = trim($_POST['remarks'] ?? '');

        $allowed_statuses = [];
        if ($role_id == 2) {
            $allowed_statuses = ['Filed', 'Under Review', 'Returned'];

            $stmt_type = $mysqli->prepare("SELECT type FROM applications WHERE app_id = ?");
            $stmt_type->bind_param('i', $app_id);
            $stmt_type->execute();
            $result_type = $stmt_type->get_result();
            $app_type = $result_type->fetch_assoc()['type'] ?? '';
            $stmt_type->close();

            if (in_array($app_type, ['Residency Certificate', 'Indigency Certificate'])) {
                $allowed_statuses = array_merge($allowed_statuses, ['Approved', 'Rejected']);
            }
        } elseif ($role_id == 1) {
            $allowed_statuses = ['Approved', 'Rejected'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (!in_array($current_status, $allowed_statuses, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status update']);
            exit();
        }

        if ($current_status === 'Approved') {
            $stmt_qr = $mysqli->prepare("SELECT qr_value FROM qr_codes WHERE app_id = ?");
            $stmt_qr->bind_param('i', $app_id);
            $stmt_qr->execute();
            $result_qr = $stmt_qr->get_result();

            if ($result_qr->num_rows === 0) {
                $token = bin2hex(random_bytes(16));

                $host = $_SERVER['HTTP_HOST'];
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $qr_url = "$protocol://$host/municipal-e-services/views/verify_application.php?token=$token";

                $stmt_insert_qr = $mysqli->prepare("INSERT INTO qr_codes (app_id, qr_value, token) VALUES (?, ?, ?)");
                $stmt_insert_qr->bind_param('iss', $app_id, $qr_url, $token);
                $stmt_insert_qr->execute();
                $stmt_insert_qr->close();
            }
            $stmt_qr->close();
        }

        $stmt = $mysqli->prepare("UPDATE applications SET current_status = ?, remarks = ?, updated_at = NOW() WHERE app_id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $current_status, $remarks, $app_id);
            if ($stmt->execute()) {
                $stmt_log = $mysqli->prepare("INSERT INTO statuses (app_id, status, changed_by) VALUES (?, ?, ?)");
                $stmt_log->bind_param('isi', $app_id, $current_status, $_SESSION['user_id']);
                $stmt_log->execute();
                $stmt_log->close();

                $stmt_user = $mysqli->prepare("SELECT user_id FROM applications WHERE app_id = ?");
                $stmt_user->bind_param('i', $app_id);
                $stmt_user->execute();
                $res_user = $stmt_user->get_result();
                $user = $res_user->fetch_assoc();
                $stmt_user->close();

                if ($user) {
                    $notif_msg = "Your application has been approved.";
                    if ($remarks !== '') {
                        $notif_msg .= " Remarks: {$remarks}";
                    }
                    $stmt_notif = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt_notif->bind_param('is', $user['user_id'], $notif_msg);
                    $stmt_notif->execute();
                    $stmt_notif->close();
                }

                echo json_encode(['success' => true]);
                exit();
            }
            $stmt->close();
        }

        echo json_encode(['success' => false, 'message' => 'Failed to update application']);
        exit();
    }

    if ($_POST['action'] === 'forward_to_admin') {
        if (!isset($_SESSION['user_id'], $_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $app_id = intval($_POST['app_id'] ?? 0);

        // Set status to "Pending Approval"
        $new_status = 'Pending Approval';
        $remarks = 'Forwarded by staff for admin approval.';

        // Update applications table
        $stmt = $mysqli->prepare("UPDATE applications SET current_status = ?, remarks = ?, updated_at = NOW() WHERE app_id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $new_status, $remarks, $app_id);
            if ($stmt->execute()) {
                // Log status change to statuses table
                $stmt_log = $mysqli->prepare("INSERT INTO statuses (app_id, status, changed_by) VALUES (?, ?, ?)");
                $stmt_log->bind_param('isi', $app_id, $new_status, $_SESSION['user_id']);
                $stmt_log->execute();
                $stmt_log->close();

                // Notify all admins (role_id = 1)
                $result_admins = $mysqli->query("SELECT user_id FROM users WHERE role_id = 1");
                while ($admin = $result_admins->fetch_assoc()) {
                    $notif_msg = "Application #{$app_id} has been forwarded to you for approval.";
                    $stmt_notif = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt_notif->bind_param('is', $admin['user_id'], $notif_msg);
                    $stmt_notif->execute();
                    $stmt_notif->close();
                }

                // Optionally, send notification to resident (user_id) about forwarding for admin approval
                $stmt_user = $mysqli->prepare("SELECT user_id FROM applications WHERE app_id = ?");
                $stmt_user->bind_param('i', $app_id);
                $stmt_user->execute();
                $res_user = $stmt_user->get_result();
                $user = $res_user->fetch_assoc();
                $stmt_user->close();

                if ($user) {
                    $notif_msg_resident = "Your application #{$app_id} has been forwarded for admin approval.";
                    $stmt_notif_resident = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt_notif_resident->bind_param('is', $user['user_id'], $notif_msg_resident);
                    $stmt_notif_resident->execute();
                    $stmt_notif_resident->close();
                }

                echo json_encode(['success' => true]);
                exit();
            }
            $stmt->close();
        }

        echo json_encode(['success' => false, 'message' => 'Failed to forward application.']);
        exit();
    }

        if ($_POST['action'] === 'upload_additional_documents') {
        if (!isset($_SESSION['user_id'], $_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $app_id = intval($_POST['app_id'] ?? 0);
        $reupload_message = trim($_POST['reupload_message'] ?? '');

        // Check if app_id exists and belongs to some user
        $stmt_check = $mysqli->prepare("SELECT user_id FROM applications WHERE app_id = ?");
        $stmt_check->bind_param('i', $app_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
            exit();
        }
        $app_user = $result_check->fetch_assoc();
        $stmt_check->close();

        // Handle file uploads
        $upload_dir = '../uploads/'; // adjust if needed
        $uploaded_files_count = 0;

        if (!empty($_FILES['documents'])) {
            $files = $_FILES['documents'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_name = basename($files['name'][$i]);
                    $file_tmp = $files['tmp_name'][$i];
                    $file_type = $files['type'][$i];
                    $target_file = $upload_dir . time() . '_' . $file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $stmt_doc = $mysqli->prepare("INSERT INTO documents (app_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                        $stmt_doc->bind_param('isss', $app_id, $file_name, $target_file, $file_type);
                        $stmt_doc->execute();
                        $stmt_doc->close();
                        $uploaded_files_count++;
                    }
                }
            }
        }

        // If there is a re-upload message, save it as a remark + send notification
        if ($reupload_message !== '') {
            // Append or overwrite remarks? Here we append
            $stmt_get_remarks = $mysqli->prepare("SELECT remarks FROM applications WHERE app_id = ?");
            $stmt_get_remarks->bind_param('i', $app_id);
            $stmt_get_remarks->execute();
            $res_remarks = $stmt_get_remarks->get_result();
            $current_remarks = '';
            if ($row = $res_remarks->fetch_assoc()) {
                $current_remarks = $row['remarks'] ?? '';
            }
            $stmt_get_remarks->close();

            $new_remarks = trim($current_remarks . "\n[Re-upload Request] " . $reupload_message);

            $stmt_update = $mysqli->prepare("UPDATE applications SET remarks = ?, updated_at = NOW() WHERE app_id = ?");
            $stmt_update->bind_param('si', $new_remarks, $app_id);
            $stmt_update->execute();
            $stmt_update->close();

            // Insert notification for the user
            $notif_msg = "Staff requested document re-upload for application #{$app_id}: {$reupload_message}";
            $stmt_notif = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt_notif->bind_param('is', $app_user['user_id'], $notif_msg);
            $stmt_notif->execute();
            $stmt_notif->close();
        }

        echo json_encode([
            'success' => true,
            'message' => "Uploaded {$uploaded_files_count} files. Re-upload request sent."
        ]);
        exit();
    }
    if ($_POST['action'] === 'fetch_admin_applications') {
        if (!isset($_SESSION['user_id'], $_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = '%' . $mysqli->real_escape_string($_POST['search'] ?? '') . '%';
        $status_filter = $_POST['status_filter'] ?? 'Pending Approval';

        $query = "
            SELECT a.app_id, a.type, a.purpose, a.current_status, a.remarks, a.submitted_at, u.full_name
            FROM applications a
            JOIN users u ON a.user_id = u.user_id
            WHERE (a.type LIKE ? OR u.full_name LIKE ?)
        ";

        // If not "All", apply status filter:
        if ($status_filter !== 'All') {
            $query .= " AND a.current_status = ?";
        }

        $query .= " ORDER BY a.submitted_at DESC";

        $stmt = $status_filter !== 'All'
            ? $mysqli->prepare($query)
            : $mysqli->prepare(str_replace(' AND a.current_status = ?', '', $query));

        if ($status_filter !== 'All') {
            $stmt->bind_param("sss", $search, $search, $status_filter);
        } else {
            $stmt->bind_param("ss", $search, $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }

        echo json_encode(['success' => true, 'applications' => $applications]);
        exit();
    }

    // if ($_GET['action'] === 'generate_qr' && isset($_GET['app_id'])) {
    //     $app_id = intval($_GET['app_id']);

    //     // You may use app_id as part of the QR's link:
    //     $qr_link = "http://localhost/municipal-e-services/verify.php?app_id={$app_id}";

    // // 🔒 Later, when deploying to your domain, replace http://localhost/municipal-e-services with your domain URL. Example:
    // // $qr_link = "https://yourdomain.com/verify.php?app_id={$app_id}";

    //     // Insert or update qr_codes table:
    //     $stmt_check = $mysqli->prepare("SELECT qr_id FROM qr_codes WHERE app_id = ?");
    //     $stmt_check->bind_param('i', $app_id);
    //     $stmt_check->execute();
    //     $stmt_check->store_result();

    //     if ($stmt_check->num_rows > 0) {
    //         // Update existing QR code
    //         $stmt_update = $mysqli->prepare("UPDATE qr_codes SET qr_value = ? WHERE app_id = ?");
    //         $stmt_update->bind_param('si', $qr_link, $app_id);
    //         $stmt_update->execute();
    //         $stmt_update->close();
    //     } else {
    //         // Insert new QR code
    //         $stmt_insert = $mysqli->prepare("INSERT INTO qr_codes (app_id, qr_value) VALUES (?, ?)");
    //         $stmt_insert->bind_param('is', $app_id, $qr_link);
    //         $stmt_insert->execute();
    //         $stmt_insert->close();
    //     }

    //     $stmt_check->close();

    //     echo json_encode(['success' => true, 'qr_link' => $qr_link]);
    //     exit();
    // }


}

echo json_encode($response);
?>
