<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch values from Selection Form or Search Bar
$class_raw    = isset($_POST['class']) ? $_POST['class'] : '';
$bus_name_raw = isset($_POST['bus_name']) ? $_POST['bus_name'] : '';
$bus_stop_raw = isset($_POST['bus_stop']) ? $_POST['bus_stop'] : '';

$class    = mysqli_real_escape_string($conn, trim($class_raw));
$bus_name = mysqli_real_escape_string($conn, trim($bus_name_raw));
$bus_stop = mysqli_real_escape_string($conn, trim($bus_stop_raw));

// முக்கிய திருத்தம்: bus_required = 'Yes' என்ற நிபந்தனை இங்கே சேர்க்கப்பட்டுள்ளது
$sql = "SELECT s_name, self_no, class_name, bus_name, bus_stop 
        FROM student_details_table 
        WHERE bus_required = 'Yes'";

if (!empty($class)) {
    $sql .= " AND UPPER(class_name) = UPPER('$class')";
}
if (!empty($bus_name)) {
    $sql .= " AND UPPER(bus_name) = UPPER('$bus_name')";
}
if (!empty($bus_stop)) {
    $sql .= " AND UPPER(bus_stop) LIKE UPPER('%$bus_stop%')";
}

$sql .= " ORDER BY s_name ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("<div style='color:red; padding:20px;'>Query Failed: " . mysqli_error($conn) . "</div>");
}

$total_students = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Route Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; margin: 40px; color: #1e293b; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        .report-title h2 { margin: 0; color: #3b82f6; }
        
        /* Search and Filter Form Styling */
        .search-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; gap: 15px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .form-group label { font-size: 13px; font-weight: 600; color: #64748b; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-search { background: #3b82f6; color: white; }
        .btn-print { background: #0f172a; color: white; }
        .btn-back { background: #94a3b8; color: white; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th { background: #3b82f6; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background: #f1f5f9; }

        @media print {
            .btn-group, .search-container { display: none !important; }
            body { margin: 0; background: white; }
            table { box-shadow: none; border: 1px solid #cbd5e1; }
        }
    </style>
</head>
<body>

<div class="header-box">
    <div class="report-title">
        <h2><i class="fas fa-bus"></i> College Bus Transport Report</h2>
        <p>
            <b>Filters applied:</b> 
            Class: <u><?= !empty($class) ? htmlspecialchars($class) : 'All Classes' ?></u> | 
            Bus Number: <u><?= !empty($bus_name) ? htmlspecialchars($bus_name) : 'All Buses' ?></u>
        </p>
    </div>
    
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Print Report</button>
        <a href="admin_dashboard.php" class="btn btn-back">← Dashboard</a>
    </div>
</div>

<form method="POST" class="search-container">
    <div class="form-group">
        <label>Class Name</label>
        <select name="class">
            <option value="">-- All Classes --</option>
            <option value="I-BCA" <?= $class == 'I-BCA' ? 'selected' : '' ?>>I-BCA</option>
            <option value="II-BCA" <?= $class == 'II-BCA' ? 'selected' : '' ?>>II-BCA</option>
            <option value="III-BCA" <?= $class == 'III-BCA' ? 'selected' : '' ?>>III-BCA</option>
            <option value="I-MCA" <?= $class == 'I-MCA' ? 'selected' : '' ?>>I-MCA</option>
            <option value="II-MCA" <?= $class == 'II-MCA' ? 'selected' : '' ?>>II-MCA</option>
        </select>
    </div>

    <div class="form-group">
        <label>Bus Number</label>
        <input type="text" name="bus_name" placeholder="e.g. Bus 12" value="<?= htmlspecialchars($bus_name_raw) ?>">
    </div>

    <div class="form-group">
        <label>Boarding Point / Bus Stop</label>
        <input type="text" name="bus_stop" placeholder="e.g. Town Hall" value="<?= htmlspecialchars($bus_stop_raw) ?>">
    </div>

    <button type="submit" class="btn btn-search"><i class="fas fa-search"></i> Search / Filter</button>
</form>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Class</th>
            <th>Phone Number</th>
            <th>Bus Number</th>
            <th>Boarding Point (Bus Stop)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($total_students > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars(strtoupper($row['s_name'])) . "</td>
                        <td>" . htmlspecialchars($row['class_name']) . "</td>
                        <td>" . htmlspecialchars($row['self_no'] ?? 'N/A') . "</td>
                        <td style='font-weight:bold; color:#3b82f6;'> " . htmlspecialchars($row['bus_name'] ?? 'N/A') . "</td>
                        <td style='font-weight:500; color:#0f172a;'><i class='fas fa-map-marker-alt' style='color:#cbd5e1; font-size:13px; margin-right:5px;'></i> " . htmlspecialchars($row['bus_stop'] ?? 'N/A') . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center; padding:40px; color:#64748b;'>No college bus users found matching criteria.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>