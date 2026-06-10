<?php
session_start();
include 'config.php';

// Faculty login check
if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: index1.php");
    exit();
}

// 1. Get Faculty ID from session
$f_id = $_SESSION['f_id'];

// 2. Fetch the specific class this faculty is incharge of
$stmt = $conn->prepare("SELECT class_incharge FROM faculty_table WHERE faculty_id = ?");
$stmt->bind_param("s", $f_id);
$stmt->execute();
$f_res = $stmt->get_result();
$f_row = $f_res->fetch_assoc();

$my_class = isset($f_row['class_incharge']) ? trim((string)$f_row['class_incharge']) : '';

// 3. Fetch ONLY students belonging to that specific class
if (!empty($my_class)) {
    $stmt = $conn->prepare("SELECT * FROM student_details_table WHERE TRIM(class_name) = ? ORDER BY s_reg ASC");
    $stmt->bind_param("s", $my_class);
    $stmt->execute();
    $result = $stmt->get_result();
    $target_header = "Records for Class: " . $my_class;
} else {
    $result = null;
    $target_header = "No Class Assigned to You";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty | Student Records</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
        /* Admin-style Dark Theme Colors */
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #0f172a; --card-bg: #1e293b; --border: #334155; }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: white; margin: 0; padding: 20px; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Table Styling from Admin Code */
        .table-container { 
            background: var(--card-bg); 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            overflow-x: auto; 
            width: 100%;
            border: 1px solid var(--border);
        }
        
        table { border-collapse: collapse; width: 100%; font-size: 12px; min-width: 2800px; table-layout: fixed; }
        th { background: #334155; color: #f8fafc; padding: 15px; text-transform: uppercase; border-bottom: 2px solid #475569; position: sticky; top: 0; }
        td { padding: 12px; border-bottom: 1px solid var(--border); text-align: center; white-space: nowrap; }
        
        tr:hover { background: #1e293b; filter: brightness(1.2); }
        
        .student-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }
        
        /* Tags & Buttons */
        .blood-tag { background: #ef4444; color: white; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .status-tag { padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 11px; }
        .locked { background: #7f1d1d; color: #fecaca; }
        .approved { background: #064e3b; color: #d1fae5; }
        
        .dashboard-btn { text-decoration: none; color: white; background: #334155; padding: 8px 15px; border-radius: 6px; transition: 0.3s; }
        .dashboard-btn:hover { background: #475569; }
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
<button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> Print Report
        </button>

    <h2 style="color: white;"><i class="fas fa-user-graduate"></i> <?= htmlspecialchars((string)$target_header) ?></h2>
    <a href="faculty_dashboard.php" class="dashboard-btn">← Dashboard</a>
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
                <th style="width: 180px;">Mother Name</th>
                <th style="width: 120px;">Father No</th>
                <th style="width: 120px;">Mother No</th>
                <th style="width: 130px;">Income</th>
                <th style="width: 150px;">Aadhar No</th>
                <th style="width: 120px;">PAN No</th>
                <th style="width: 180px;">Bank Name</th>
                <th style="width: 180px;">Account No</th>
                <th style="width: 120px;">IFSC</th>
                <th style="width: 100px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $photo = !empty($row['s_photo']) ? "uploads/".$row['s_photo'] : "https://via.placeholder.com/40";
                ?>
                    <tr>
                        <td><img src="<?= $photo ?>" class="student-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'"></td>
                        <td><b><?= htmlspecialchars((string)($row['s_reg'] ?? '')) ?></b></td>
                        <td><?= htmlspecialchars((string)($row['s_name'] ?? '-')) ?></td>
                        <td><span class="blood-tag"><?= strtoupper($row['blood_group'] ?: '-') ?></span></td>
                        <td><?= htmlspecialchars((string)($row['dob'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['self_no'] ?: $row['self_number'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['s_email'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['religion'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['caste'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['address'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['city'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['pincode'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['father_name'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['mother_name'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['father_number'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['mother_number'] ?? '-')) ?></td>
                        <td>₹<?= number_format((float)($row['annual_income'] ?: $row['f_income'] ?? 0)) ?></td>
                        <td><?= htmlspecialchars((string)($row['aadhar_no'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['pan_no'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['bank_name'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['acc_number'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars((string)($row['ifsc_code'] ?? '-')) ?></td>
                        <td>
                            <span class="status-tag <?= ($row['edit_status'] == 'Approved') ? 'approved' : 'locked' ?>">
                                <?= htmlspecialchars((string)($row['edit_status'] ?: 'Locked')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="23" style="padding:50px; text-align:center; color: #94a3b8;">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>