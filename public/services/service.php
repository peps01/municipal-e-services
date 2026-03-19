<?php
include '../../config/db.php'; // Adjust path since we're in public/services/

if(!isset($_GET['slug'])){
    echo "No service selected!";
    exit;
}

$slug = $_GET['slug'];

// Fetch the service from database
$query = "SELECT * FROM services WHERE slug='$slug'";
$result = mysqli_query($conn, $query);
$service = mysqli_fetch_assoc($result);

if(!$service){
    echo "Service not found!";
    exit;
}
?>

<h1><?php echo $service['name']; ?></h1>
<p><?php echo $service['description']; ?></p>

<!-- Optional: custom forms per service -->
<?php if($service['slug'] == 'pay-bills'){ ?>
<form method="POST" action="pay_bills_submit.php">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="number" name="amount" placeholder="Amount" required>
    <button type="submit">Submit Payment</button>
</form>
<?php } ?>

<!-- You can add different forms/content here based on the service -->