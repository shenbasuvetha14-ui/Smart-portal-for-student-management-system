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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | Glassmorphism</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            /* Background image is essential for glassmorphism to show the blur effect */
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

        /* The Glass Container */
        .glass-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.05); /* Semi-transparent white */
            backdrop-filter: blur(12px); /* Frosted effect */
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1); /* Subtle white border */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Glowing background blobs for extra depth */
        .glass-card::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(59, 130, 246, 0.2);
            filter: blur(50px);
            z-index: -1;
        }

        /* Top-right Exit Icon */
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
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .info-badge {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 30px;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .input-group { margin-bottom: 20px; text-align: left; }

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
            text-transform: uppercase; /* Your requirement */
        }

        input[type="password"] { text-transform: none; }

        input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        input::placeholder { color: rgba(255, 255, 255, 0.2); }

        button {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s all;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
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
    
    <h2>Add Student</h2>
    <div class="info-badge">
        <i class="fas fa-graduation-cap"></i> Class: <?= htmlspecialchars($class_name) ?>
    </div>
    
    <form action="save_student.php" method="POST">
        <input type="hidden" name="faculty_id" value="<?= htmlspecialchars($f_id) ?>">
        <input type="hidden" name="class_name" value="<?= htmlspecialchars($class_name) ?>">

        <div class="input-group">
            <label>FULL NAME</label>
            <input type="text" name="s_name" placeholder="Enter full name" required>
        </div>

        <div class="input-group">
            <label>REGISTRATION NUMBER</label>
            <input type="text" name="reg_no" placeholder="e.g. 23UCA180" required>
        </div>
<!-- Registration Number-க்கு கீழே இதைச் சேர்க்கவும் -->
<div class="input-group">
    <label>BATCH</label>
    <input type="text" name="batch" placeholder="e.g. 2023-2026" required>
</div>

        <div class="input-group">
            <label>DEFAULT PASSWORD</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit">REGISTER STUDENT</button>
    </form>

    <a href="faculty_dashboard.php" class="back-text">Cancel and return</a>
</div>

</body>
</html>