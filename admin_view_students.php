<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$class = $_GET['class'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View | <?= htmlspecialchars($class) ?> Records</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #0f172a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: white; margin: 0; padding: 20px; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Table Styling */
        .table-container { 
            background: #1e293b; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            overflow-x: auto; /* Side scroll enable */
            width: 100%;
            border: 1px solid #334155;
        }
        
        table { border-collapse: collapse; width: 100%; font-size: 12px; min-width: 2800px; table-layout: fixed; }
        th { background: #334155; color: #f8fafc; padding: 15px; text-transform: uppercase; border-bottom: 2px solid #475569; position: sticky; top: 0; }
        td { padding: 12px; border-bottom: 1px solid #334155; text-align: center; white-space: nowrap; }
        
        tr:hover { background: #1e293b; filter: brightness(1.2); }
        
        .student-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #3b82f6; }
        .blood-tag { background: #ef4444; color: white; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .action-btn { background: #3b82f6; color: white; text-decoration: none; padding: 6px 12px; border-radius: 5px; font-weight: bold; }
/* Print Button Styling */
.print-btn { 
    background: #10b981; 
    color: white; 
    text-decoration: none; 
    padding: 8px 15px; 
    border-radius: 6px; 
    border: none; 
    cursor: pointer; 
    font-weight: 600;
    margin-right: 10px;
}
.print-btn:hover { background: #059669; }

/* Print Media Query */
@media print {
    /* Hide navigation and buttons */
    .header-flex div, .action-btn, .print-btn { display: none !important; }
    

    .student-img { border: 1px solid #000; }
}
    </style>
</head>
<body>

<div class="header-flex">
    <h2><i class="fas fa-database"></i> Admin View: <?= htmlspecialchars($class) ?> Records</h2>
    <div>
<button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> Print Report
        </button>
        <a href="admin_class_selection.php" style="text-decoration:none; color:#94a3b8; margin-right: 20px;">Change Class</a>
        <a href="admin_dashboard.php" style="text-decoration:none; color:white; background:#334155; padding:8px 15px; border-radius:6px;">← Dashboard</a>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Photo</th>
                <th style="width: 120px;">Reg No</th>
                <th style="width: 180px;">Student Name</th>
                <th style="width: 80px;">Blood</th>
                <th style="width: 110px;">DOB</th>
                <th style="width: 120px;">Mobile</th>
                <th style="width: 200px;">Email</th>
                <th style="width: 120px;">Religion</th>
                <th style="width: 120px;">Caste</th>
                <th style="width: 250px;">Address</th>
                <th style="width: 120px;">City</th>
                <th style="width: 90px;">Pincode</th>
                <th style="width: 180px;">Father Name</th>
                <th style="width: 120px;">Father No</th>
                <th style="width: 180px;">Mother Name</th>
                <th style="width: 120px;">Mother No</th>
                <th style="width: 130px;">Income</th>
                <th style="width: 150px;">Aadhar No</th>
                <th style="width: 120px;">PAN No</th>
                <th style="width: 180px;">Bank Name</th>
                <th style="width: 180px;">Account No</th>
                <th style="width: 120px;">IFSC</th>
     
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM student_details_table WHERE class_name = '$class' ORDER BY s_reg ASC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $photo = !empty($row['s_photo']) ? "uploads/".$row['s_photo'] : "https://via.placeholder.com/40";
                    echo "<tr>
                            <td><img src='$photo' class='student-img' onerror=\"this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'\"></td>
                            <td><b>{$row['s_reg']}</b></td>
                            <td>" . ($row['s_name'] ?: '-') . "</td>
                            <td><span class='blood-tag'>".strtoupper($row['blood_group'] ?: '-')."</span></td>
                            <td>{$row['dob']}</td>
                            <td>" . ($row['self_no'] ?: '-') . "</td>
                            <td>" . ($row['s_email'] ?: '-') . "</td>
                            <td>" . ($row['religion'] ?: '-') . "</td>
                            <td>" . ($row['caste'] ?: '-') . "</td>
                            <td>" . ($row['address'] ?: '-') . "</td>
                            <td>" . ($row['city'] ?: '-') . "</td>
                            <td>" . ($row['pincode'] ?: '-') . "</td>
                            <td>" . ($row['father_name'] ?: '-') . "</td>
                            <td>" . ($row['father_number'] ?: '-') . "</td>
                            <td>" . ($row['mother_name'] ?: '-') . "</td>
                            <td>" . ($row['mother_number'] ?: '-') . "</td>
                            <td>₹" . number_format((float)($row['annual_income'] ?? 0)) . "</td>
                            <td>" . ($row['aadhar_no'] ?: '-') . "</td>
                            <td>" . ($row['pan_no'] ?: '-') . "</td>
                            <td>" . ($row['bank_name'] ?: '-') . "</td>
                            <td>" . ($row['acc_number'] ?: '-') . "</td>
                            <td>" . ($row['ifsc_code'] ?: '-') . "</td>
                            
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='23' style='padding:50px; text-align:center;'>No records found for $class.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>