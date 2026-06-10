<?php
session_start();
include 'config.php';

// Faculty/Admin check
if (!isset($_SESSION['faculty_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: index1.php");
    exit();
}

$reg_no = isset($_GET['reg']) ? mysqli_real_escape_string($conn, $_GET['reg']) : '';

if (empty($reg_no)) {
    die("Registration number missing!");
}

// Student details retrieve panrom
$sql = "SELECT * FROM student_details_table WHERE s_reg = '$reg_no'";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Student record not found!");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Profile | <?= $reg_no ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 40px; color: #1e293b; }
        .profile-container { max-width: 900px; margin: auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 30px; }
        .student-info { display: flex; align-items: center; gap: 20px; }
        .profile-pic { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #eef2ff; }
        
        .section-title { background: #5c67f2; color: white; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: bold; margin-top: 30px; margin-bottom: 15px; display: inline-block; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { background: #f8fafc; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; }
        .label { display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .value { font-size: 14px; font-weight: 600; color: #334155; }

        .btn-back { background: #64748b; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; }
        @media print { .btn-back { display: none; } body { padding: 0; } .profile-container { box-shadow: none; } }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="header">
        <div class="student-info">
            <?php $img = !empty($user['s_photo']) ? "uploads/".$user['s_photo'] : "https://via.placeholder.com/100"; ?>
            <img src="<?= $img ?>" class="profile-pic">
            <div>
                <h2 style="margin:0;"><?= $user['s_name'] ?></h2>
                <p style="margin:5px 0; color:#64748b;">Reg No: <b><?= $user['s_reg'] ?></b> | <?= $user['class_name'] ?></p>
            </div>
        </div>
        <a href="javascript:window.history.back()" class="btn-back">← Back</a>
        <button onclick="window.print()" class="btn-back" style="background:#5c67f2; border:none; cursor:pointer; margin-left:10px;">Print PDF</button>
    </div>

    <div class="section-title">Step 1: Basic Information</div>
    <div class="info-grid">
        <div class="info-item"><span class="label">Date of Birth</span><span class="value"><?= $user['dob'] ?></span></div>
        <div class="info-item"><span class="label">Blood Group</span><span class="value"><?= $user['blood_group'] ?></span></div>
        <div class="info-item"><span class="label">Religion</span><span class="value"><?= $user['religion'] ?></span></div>
        <div class="info-item"><span class="label">Caste</span><span class="value"><?= $user['caste'] ?></span></div>
        <div class="info-item" style="grid-column: span 2;"><span class="label">Nationality</span><span class="value"><?= $user['nationality'] ?></span></div>
    </div>

    <div class="section-title">Step 2: Contact Details</div>
    <div class="info-grid">
        <div class="info-item"><span class="label">Student Mobile</span><span class="value"><?= $user['self_no'] ?></span></div>
        <div class="info-item"><span class="label">Email Address</span><span class="value"><?= $user['s_email'] ?></span></div>
        <div class="info-item" style="grid-column: span 2;"><span class="label">Address</span><span class="value"><?= $user['address'] ?></span></div>
        <div class="info-item"><span class="label">City & State</span><span class="value"><?= $user['city'] ?>, <?= $user['state'] ?></span></div>
        <div class="info-item"><span class="label">Pincode</span><span class="value"><?= $user['pincode'] ?></span></div>
    </div>

    <div class="section-title">Step 3: Family & Income</div>
    <div class="info-grid">
        <div class="info-item"><span class="label">Father Name</span><span class="value"><?= $user['father_name'] ?></span></div>
        <div class="info-item"><span class="label">Mother Name</span><span class="value"><?= $user['mother_name'] ?></span></div>
        <div class="info-item"><span class="label">Father Number</span><span class="value"><?= $user['father_number'] ?></span></div>
        <div class="info-item"><span class="label">Mother Number</span><span class="value"><?= $user['mother_number'] ?></span></div>
        <div class="info-item"><span class="label">Annual Income</span><span class="value">₹ <?= number_format($user['annual_income']) ?></span></div>
        <div class="info-item"><span class="label">Guardian Name</span><span class="value"><?= $user['guardian_name'] ?></span></div>
    </div>

    <div class="section-title">Step 4: Identity Documents</div>
    <div class="info-grid">
        <div class="info-item"><span class="label">Aadhar Number</span><span class="value"><?= $user['aadhar_no'] ?></span></div>
        <div class="info-item"><span class="label">PAN Number</span><span class="value"><?= $user['pan_no'] ?></span></div>
    </div>

    <div class="section-title">Step 5: Bank Details</div>
    <div class="info-grid">
        <div class="info-item" style="grid-column: span 2;"><span class="label">Account Number</span><span class="value"><?= $user['acc_number'] ?></span></div>
        <div class="info-item"><span class="label">Bank Name</span><span class="value"><?= $user['bank_name'] ?></span></div>
        <div class="info-item"><span class="label">IFSC Code</span><span class="value"><?= $user['ifsc_code'] ?></span></div>
    </div>

    <div style="margin-top: 40px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
        Student Edit Status: <b><?= $user['edit_status'] ?: 'Locked' ?></b>
    </div>
</div>

</body>
</html>