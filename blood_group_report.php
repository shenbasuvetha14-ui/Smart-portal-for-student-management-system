
<?php
session_start();
include 'config.php';

if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch POST values from selection page
$class_raw = isset($_POST['class']) ? $_POST['class'] : '';
$bg_raw = isset($_POST['blood_group']) ? $_POST['blood_group'] : '';

$class = mysqli_real_escape_string($conn, trim($class_raw));
$bg = mysqli_real_escape_string($conn, trim($bg_raw));

// முக்கிய திருத்தம்: LIKE-க்கு பதிலாக '=' பயன்படுத்தப்பட்டுள்ளது. இது துல்லியமான முடிவைத் தரும்.
$sql = "SELECT s_name, self_no, blood_group FROM student_details_table 
        WHERE UPPER(class_name) = UPPER('$class') 
        AND UPPER(blood_group) = UPPER('$bg') 
        ORDER BY s_name ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("<div style='color:red; padding:20px;'>Query Failed: " . mysqli_error($conn) . "</div>");
}

// Count total rows returned
$total_students = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bg) ?> Blood Group Report | <?= htmlspecialchars($class) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; margin: 40px; color: #1e293b; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        .report-title h2 { margin: 0; color: #ef4444; }
        
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; }
        .btn-print { background: #0f172a; color: white; }
        .btn-back { background: #94a3b8; color: white; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th { background: #ef4444; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background: #f1f5f9; }

        @media print {
            .btn-group { display: none !important; }
            body { margin: 0; background: white; }
            table { box-shadow: none; border: 1px solid #cbd5e1; }
        }
    </style>
</head>
<body>

<div class="header-box">
    <div class="report-title">
        <h2><i class="fas fa-droplet"></i> <?= htmlspecialchars($bg) ?> Blood Group Report</h2>
        <p>Class: <b><?= htmlspecialchars($class) ?></b> | Date: <?= date('d-m-Y') ?></p>
    </div>
    
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Print</button>
        <a href="blood_group_selection.php" class="btn btn-back">← Back</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Phone Number</th>
            <th>Blood Group</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($total_students > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars(strtoupper($row['s_name'])) . "</td>
                        <td>" . htmlspecialchars($row['self_no'] ?? 'N/A') . "</td>
                        <td style='color:#ef4444; font-weight:bold;'>" . htmlspecialchars($row['blood_group']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3' style='text-align:center; padding:40px;'>No students found with " . htmlspecialchars($bg) . " group in " . htmlspecialchars($class) . ".</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
