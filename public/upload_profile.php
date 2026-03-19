<?php

session_start();
include "../config/db.php";

$user_id = $_SESSION['user_id'];

/* Get current profile picture */
$sql = "SELECT profile_pic FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$oldPic = $user['profile_pic'];

/* Upload new file */
$file = $_FILES['profile_pic'];

$filename = time() . "_" . $file['name'];

$uploadPath = "../uploads/" . $filename;   // actual folder
$dbPath = "uploads/" . $filename;          // path saved in database

/* Move uploaded file */
if(move_uploaded_file($file['tmp_name'], $uploadPath)){

    /* Delete old image if it exists and is not default */
    if(!empty($oldPic) && $oldPic != "uploads/default-profile.png"){

        $oldPath = "../" . $oldPic;

        if(file_exists($oldPath)){
            unlink($oldPath);
        }

    }

    /* Update database */
    $sql = "UPDATE users SET profile_pic='$dbPath' WHERE id='$user_id'";
    mysqli_query($conn, $sql);

}

header("Location: dashboard.php");

?>