<?php
include '../config/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $barangay = mysqli_real_escape_string($conn, $_POST['barangay']);

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0){
        echo "Email already registered!";
    } else {
        $sql = "INSERT INTO users(first_name, last_name, email, password, phone, barangay)
                VALUES('$first_name', '$last_name', '$email', '$password', '$phone', '$barangay')";

        if(mysqli_query($conn, $sql)){
            echo "Registration Successful!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

}

mysqli_close($conn);
?>