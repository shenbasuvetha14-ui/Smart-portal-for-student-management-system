
<?php
session_start();
include 'config.php';

if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Group Report Selection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 100%; max-width: 400px; text-align: center; }
        select, button { width: 100%; padding: 12px; margin-top: 15px; border-radius: 8px; border: none; font-size: 16px; }
        select { background: #1e293b; color: white; border: 1px solid #334155; }
        button { background: #ef4444; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #dc2626; transform: translateY(-2px); }
        .back-link { display: block; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="glass-card">
    <h2><i class="fas fa-droplet" style="color: #ef4444;"></i> Blood Group Report</h2>
    
    <form action="blood_group_report.php" method="POST">
        <label style="display:block; text-align:left; color:#94a3b8; font-size:12px;">SELECT CLASS</label>
        <select name="class" required>
            <option value="">-- Select Class --</option>
            <option value="I-BCA">I-BCA</option>
            <option value="II-BCA">II-BCA</option>
            <option value="III-BCA">III-BCA</option>
            <option value="I-MCA">I-MCA</option>
            <option value="II-MCA">II-MCA</option>
        </select>

        <label style="display:block; text-align:left; color:#94a3b8; font-size:12px; margin-top:15px;">SELECT BLOOD GROUP</label>
        <select name="blood_group" required>
            <option value="">-- Select Group --</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
        </select>

        <button type="submit">GENERATE REPORT</button>
    </form>
    <a href="faculty_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>

```