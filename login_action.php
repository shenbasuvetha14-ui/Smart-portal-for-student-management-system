<?php
session_start();

// Database connection details
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "college_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_user = $_POST['username']; 
    $admin_pass = $_POST['password'];

    // Inga unga database check logic (Secure-ah pannunga)
    if ($admin_user == "admin" && $admin_pass == "admin123") {
        
        // --- INGA THAAN CHANGE PANNIYACHU ---
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $admin_user;
        $_SESSION['role'] = 'admin'; // Indha line thaan syllabus page-ku romba mukkiyam!

        // Dashboard-ku redirect
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Username or Password'); window.location='admin_login.php';</script>";
    }
}
?>