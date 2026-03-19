<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id'])){
    echo "<p>Please login.</p>";
    exit();
}

$user_id = intval($_SESSION['user_id']);

$query = "SELECT first_name,last_name,email,phone,barangay,profile_pic 
          FROM users WHERE id = $user_id";

$result = mysqli_query($conn,$query);
$user = mysqli_fetch_assoc($result);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

$first = mysqli_real_escape_string($conn,$_POST['first_name']);
$last = mysqli_real_escape_string($conn,$_POST['last_name']);
$phone = mysqli_real_escape_string($conn,$_POST['phone']);
$barangay = mysqli_real_escape_string($conn,$_POST['barangay']);

$update = "UPDATE users 
           SET first_name='$first',
               last_name='$last',
               phone='$phone',
               barangay='$barangay'
           WHERE id=$user_id";

if(mysqli_query($conn,$update)){
    echo "<div class='alert success'>Profile updated successfully.</div>";
} else {
    echo "<div class='alert error'>Error updating: ".mysqli_error($conn)."</div>";
}

$query = "SELECT first_name,last_name,email,phone,barangay 
          FROM users WHERE id = $user_id";

$result = mysqli_query($conn,$query);
$user = mysqli_fetch_assoc($result);
}
?>

    <h2>My Profile</h2>

    <form method="POST" action="profile_content.php" class="profile-form">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label>Email</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

        <label>Barangay</label>
        <input type="text" name="barangay" value="<?php echo htmlspecialchars($user['barangay']); ?>">

        <button type="submit" name="update_profile">Update Profile</button>

    </form>
