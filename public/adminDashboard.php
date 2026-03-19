<?php
session_start();
include '../config/db.php';

/* Check login */
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.html");
    exit();
}

/* Get admin data */
$email = $_SESSION['email'];

$sql = "SELECT name, role FROM admins WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Staff Dashboard</title>

<link rel="stylesheet" href="../assets/css/admin-dashboard.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

</head>

<body>

<div class="admin-nav">

    <div class="user-info">

        <img src="../uploads/default-profile.png" id="profile-pic">

        <h3><?php echo htmlspecialchars($admin['name']); ?></h3>

        <p class="role">
            <?php echo ucfirst($admin['role']); ?>
        </p>

    </div>

    <hr class="nav-divider">

    <div class="menu">
        <nav>
        <ul>

            <li><a href="#">Dashboard</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Resident Management ▾</a>

                <ul class="dropdown-menu">
                    <li><a href="#">View Residents</a></li>
                    <li><a href="#">Manage Request</a></li>
                    <li><a href="#">Manage Bills</a></li>
                </ul>

            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Service Management ▾</a>

                <ul class="dropdown-menu">
                    <li><a href="#">Barangay Clearance</a></li>
                    <li><a href="#">Business Permit</a></li>
                    <li><a href="#">Residency Certificate</a></li>
                </ul>

            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Account ▾</a>

                <ul class="dropdown-menu">
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Change Password</a></li>
                </ul>

            </li>

        </ul>
        </nav>
    </div>

    <div class="logout-section">
        <hr>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

</div>

<div class="main-content">

<h1>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>

<p>You are logged in as <b><?php echo ucfirst($admin['role']); ?></b></p>

</div>
<script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>