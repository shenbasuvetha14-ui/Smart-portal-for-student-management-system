<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_db"; // Unga database name sariyaa irukkanu check panniko

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>