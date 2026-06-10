<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Class | Admin</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #121212; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .selection-box { background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 20px; border: 1px solid #333; text-align: center; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { color: #007bff; margin-bottom: 25px; }
        .class-btn { 
            display: block; 
            width: 100%; 
            padding: 15px; 
            margin: 10px 0; 
            background: #222; 
            color: white; 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: bold; 
            border: 1px solid #444;
            transition: 0.3s;
        }
        .class-btn:hover { background: #007bff; border-color: #007bff; transform: scale(1.02); }
        .back-link { display: inline-block; margin-top: 20px; color: #888; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: white; }
    </style>
</head>
<body>

<div class="selection-box">
    <h2>🎓 Select Class</h2>
    <p>Choose a class to view student records</p>
    
    <a href="admin_view_students.php?class=I-BCA" class="class-btn">I - BCA</a>
    <a href="admin_view_students.php?class=II-BCA" class="class-btn">II - BCA</a>
    <a href="admin_view_students.php?class=III-BCA" class="class-btn">III - BCA</a>
    <a href="admin_view_students.php?class=I-MCA" class="class-btn">I - MCA</a>
    <a href="admin_view_students.php?class=II-MCA" class="class-btn">II - MCA</a>

    <a href="admin_dashboard.php" class="back-link">← Return to Dashboard</a>
</div>

</body>
</html>