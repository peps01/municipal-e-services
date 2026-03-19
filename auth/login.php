<?php
session_start();
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // FIRST: check users table
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $_SESSION['email'] = $email;
            $_SESSION['role'] = "user";
            $_SESSION['user_id'] = $row['id'];

            header("Location: ../public/dashboard.php");
            exit();

        } else {
            header("Location: ../index.html?error=Invalid+password.+Please+try+again.");
            exit();
        }

    } else {

        // SECOND: check admin table
        $admin_sql = "SELECT * FROM admins WHERE email='$email' AND role='staff'";
        $admin_result = mysqli_query($conn, $admin_sql);

        if (mysqli_num_rows($admin_result) == 1) {

            $admin_row = mysqli_fetch_assoc($admin_result);

            if (password_verify($password, $admin_row['password'])) {

                $_SESSION['email'] = $email;
                $_SESSION['role'] = "staff";

                header("Location: ../public/adminDashboard.php");
                exit();

            } else {
                header("Location: ../index.html?error=Invalid+password.+Please+try+again.");
                exit();
            }

        } else {

            header("Location: ../index.html?error=No+account+found+with+that+email.");
            exit();

        }

    }
}
?>