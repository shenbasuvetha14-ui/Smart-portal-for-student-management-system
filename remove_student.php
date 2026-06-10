<?php
session_start();
include 'config.php';


if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

$f_id = $_SESSION['f_id']; 
$faculty_res = mysqli_query($conn, "SELECT class_incharge FROM faculty_table WHERE faculty_id = '$f_id'");
$faculty_info = mysqli_fetch_assoc($faculty_res);
$class_name = $faculty_info['class_incharge'] ?? 'Not Assigned';

$message = "";
$message_type = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_student'])) {

    $reg_no = mysqli_real_escape_string($conn, strtoupper(trim($_POST['reg_no'])));
    

    $check_student = mysqli_query($conn, "SELECT * FROM student_table WHERE reg_no = '$reg_no' AND class_name = '$class_name'");
    
    if (mysqli_num_rows($check_student) > 0) {
     
        $delete_query = "DELETE FROM student_table WHERE reg_no = '$reg_no' AND class_name = '$class_name'";
        if (mysqli_query($conn, $delete_query)) {
            $message = "Student with Registration Number $reg_no has been successfully removed.";
            $message_type = "success";
        } else {
            $message = "Error: Could not remove the student. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "No student found with Registration Number $reg_no in your assigned class ($class_name).";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Student | Glassmorphism</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%), 
                        url('https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=1920&q=80');
            background-blend-mode: overlay;
            background-size: cover;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .glass-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(239, 68, 68, 0.15); /* Red tint for delete action */
            filter: blur(50px);
            z-index: -1;
        }

        .exit-icon {
            position: absolute;
            top: 25px;
            right: 25px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 20px;
            text-decoration: none;
            transition: 0.3s ease;
        }
        .exit-icon:hover { color: #f87171; transform: rotate(90deg); }

        h2 { 
            font-size: 28px; 
            font-weight: 700; 
            margin-bottom: 8px;
            background: linear-gradient(to right, #fff, #f87171); /* Redish gradient for delete */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .info-badge {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 30px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Alert Status Message Styles */
        .alert-message {
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: left;
            line-height: 1.4;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #4ade80;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
        }

        .input-group { margin-bottom: 24px; text-align: left; }

        label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 13px; 
            font-weight: 500; 
            color: rgba(255, 255, 255, 0.6);
            padding-left: 4px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
            text-transform: uppercase;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(239, 68, 68, 0.5);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        input::placeholder { color: rgba(255, 255, 255, 0.2); }

        button {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            background: #ef4444; /* Danger/Red color for removal */
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s all;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }

        button:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.4);
        }

        button:active { transform: translateY(0); }

        .back-text {
            display: block;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }
        .back-text:hover { color: white; }
    </style>
</head>
<body>

<div class="glass-card">
    <a href="faculty_dashboard.php" class="exit-icon"><i class="fas fa-times"></i></a>
    
    <h2>Remove Student</h2>
    <div class="info-badge">
        <i class="fas fa-user-minus"></i> Class: <?= htmlspecialchars($class_name) ?>
    </div>
    
   
    <?php if (!empty($message)): ?>
        <div class="alert-message alert-<?= $message_type ?>">
            <i class="fas <?= $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i> 
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <form action="remove_student.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently remove this student?');">
        <div class="input-group">
            <label>STUDENT REGISTRATION NUMBER</label>
            <input type="text" name="reg_no" placeholder="e.g. 23UCA180" required autocomplete="off">
        </div>

        <button type="submit" name="remove_student">REMOVE STUDENT</button>
    </form>

    <a href="faculty_dashboard.php" class="back-text">Cancel and return</a>
</div>

</body>
</html>