<?php 
session_start();
include '../config/db.php';

/* Check if user is logged in */
if(!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.html");
    exit();
}

/* Get user id safely */
$user_id = intval($_SESSION['user_id']);

/* Get user data */
$sql = "SELECT first_name, last_name, profile_pic FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

/* Default profile image fallback */
$profilePic = (!empty($user['profile_pic'])) ? "../".$user['profile_pic'] : "../uploads/default-profile.png";


/* ============================= */
/* DASHBOARD DATA QUERIES */
/* ============================= */

/* Pending Requests */
$query = "SELECT COUNT(*) as total FROM service_requests WHERE user_id = $user_id AND status = 'Pending'";
$result = mysqli_query($conn,$query);
$pending_count = mysqli_fetch_assoc($result)['total'];


/* Approved Requests */
$query = "SELECT COUNT(*) as total FROM service_requests WHERE user_id = $user_id AND status = 'Approved'";
$result = mysqli_query($conn,$query);
$approved_count = mysqli_fetch_assoc($result)['total'];


/* Ready for Pickup */
$query = "SELECT COUNT(*) as total FROM service_requests WHERE user_id = $user_id AND status = 'Ready for Pickup'";
$result = mysqli_query($conn,$query);
$ready_count = mysqli_fetch_assoc($result)['total'];


/* Completed */
$query = "SELECT COUNT(*) as total FROM service_requests WHERE user_id = $user_id AND status = 'Completed'";
$result = mysqli_query($conn,$query);
$completed_count = mysqli_fetch_assoc($result)['total'];


/* Recent Requests */
$recent_requests = [];

$query = "SELECT sr.*, s.name AS service_name
          FROM service_requests sr
          JOIN services s ON sr.service_id = s.id
          WHERE sr.user_id = $user_id
          ORDER BY sr.created_at DESC
          LIMIT 5";

$result = mysqli_query($conn,$query);

while($row = mysqli_fetch_assoc($result)){
    $recent_requests[] = $row;
}


/* Notifications */
$notifications = [];

$query = "SELECT * FROM notifications WHERE user_id = $user_id
          ORDER BY created_at DESC
          LIMIT 5";

$result = mysqli_query($conn,$query);

while($row = mysqli_fetch_assoc($result)){
    $notifications[] = $row;
}


/* Announcements */
$announcements = [];

$query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3";

$result = mysqli_query($conn,$query);

while($row = mysqli_fetch_assoc($result)){
    $announcements[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resident Dashboard</title>

    <link rel="stylesheet" href="../assets/css/resident-dashboard.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

</head>

<body>

        <button id="menu-toggle" class="menu-toggle">☰</button>

        <?php include '../includes/sidebar.php'; ?>



    <?php include '../includes/topbar.php'; ?>

    <div class="main-content" id="main-content">

        <div id="page-content">
            <?php include 'dashboard_content.php'; ?>
        </div>


</div>


    <script src="../assets/js/resident-dashboard.js"></script>
    <script>
        const menuToggle = document.getElementById("menu-toggle");
        const sidebar = document.getElementById("sidebar");

        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    </script>
</body>

</html>