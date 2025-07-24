<?php
$host = 'localhost';
$username = 'root';     
$password = '';         
$database = 'municipal_portal';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    error_log("DB connection failed: " . $mysqli->connect_error); // Logged instead of shown
    exit('Database connection error.');
}

?>
