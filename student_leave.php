<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: student_login.php");
    exit();
}

$reg_no = $_SESSION['s_reg'];

// --- ATTENDANCE CALCULATION LOGIC ---
$total_working_days = 90;
$total_approved_leave_days = 0;

$leave_count_query = $conn->query("SELECT from_date, to_date FROM leave_requests WHERE s_reg = '$reg_no' AND status = 'Approved'");

while($l_row = $leave_count_query->fetch_assoc()) {
    $start = new DateTime($l_row['from_date']);
    $end = new DateTime($l_row['to_date']);
    $diff = $start->diff($end)->days + 1; 
    $total_approved_leave_days += $diff;
}

$current_attendance = $total_working_days - $total_approved_leave_days;
$attendance_percentage = round(($current_attendance / 90) * 100, 1);

// Inga 'students' nu irunthatha 'student_table' nu maathiten
$student_info = $conn->query("SELECT class_name FROM student_table WHERE reg_no = '$reg_no'")->fetch_assoc();
$default_class = $student_info['class_name'];
// --- LEAVE APPLY LOGIC ---
if (isset($_POST['apply_leave'])) {
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $from = mysqli_real_escape_string($conn, $_POST['from_date']);
    $to = mysqli_real_escape_string($conn, $_POST['to_date']);
    
    if (empty($to)) { $to = $from; }

    // Ippo $default_class-ah inga insert panrom
    $sql = "INSERT INTO leave_requests (s_reg, class_name, leave_reason, from_date, to_date, status, medical_certificate, cert_verified) 
            VALUES ('$reg_no', '$default_class', '$reason', '$from', '$to', 'Pending', NULL, 0)";
    
    if($conn->query($sql)) {
        echo "<script>alert('Leave Applied Successfully!'); window.location.href='student_leave.php';</script>";
    }
}
// --- CERTIFICATE UPLOAD LOGIC (In History Section) ---
if (isset($_POST['upload_cert'])) {
    $l_id = $_POST['leave_id'];
    $ext = pathinfo($_FILES['cert_file']['name'], PATHINFO_EXTENSION);
    $new_cert = $reg_no . "_cert_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES['cert_file']['tmp_name'], "uploads/" . $new_cert)) {
        $conn->query("UPDATE leave_requests SET medical_certificate = '$new_cert', cert_verified = 0 WHERE id = '$l_id'");
        echo "<script>alert('Certificate Uploaded Successfully!'); window.location.href='student_leave.php';</script>";
    }
}

$history = $conn->query("SELECT * FROM leave_requests WHERE s_reg = '$reg_no' ORDER BY applied_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Leave Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5c67f2;
            --sidebar-bg: #2c3e50;
            --body-bg: #f0f2f5;
            --white: #ffffff;
            --border-color: #e1e4e8;
            --text-dark: #334155;
            --warning: #ffb547;
            --success: #10b981;
        }

        body { background-color: var(--body-bg); font-family: 'Inter', sans-serif; margin: 0; display: flex; }

        .sidebar {
            width: 260px; background: var(--sidebar-bg); height: calc(100vh - 40px);
            margin: 20px; border-radius: 20px; color: white; padding: 30px 20px; position: fixed;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex; flex-direction: column;
        }

        .sidebar h2 { font-size: 1.2rem; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 25px; }

        .attendance-widget {
            margin: 10px 0 25px 0; padding: 18px; background: rgba(255,255,255,0.08); 
            border-radius: 18px; border: 1px solid rgba(255,255,255,0.1);
        }
        .attendance-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .attendance-value { font-size: 22px; font-weight: bold; margin: 5px 0; }
        
        .mini-progress-bg { background: rgba(255,255,255,0.1); height: 6px; border-radius: 10px; margin-top: 10px; overflow: hidden; }
        .mini-progress-fill { 
            height: 100%; width: <?= $attendance_percentage ?>%; 
            background: <?= ($attendance_percentage < 75) ? 'var(--warning)' : 'var(--success)' ?>;
            transition: width 1s ease-in-out;
        }

        .nav-item {
            padding: 12px 15px; border-radius: 10px; cursor: pointer; margin-bottom: 10px;
            transition: 0.3s; display: flex; align-items: center; gap: 10px; font-size: 14px;
        }

        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: #3498db; color: white; font-weight: 600; }

        .main { flex: 1; margin-left: 320px; padding: 40px; }

        .card {
            background: var(--white); padding: 30px; border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 20px;
            max-width: 800px;
        }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-dark); font-size: 14px; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 10px; box-sizing: border-box; font-family: inherit;
        }

        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { color: #94a3b8; text-transform: uppercase; font-size: 11px; padding: 10px; text-align: left; }
        td { background: #fff; padding: 15px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); font-size: 14px; }
        tr td:first-child { border-left: 1px solid var(--border-color); border-radius: 12px 0 0 12px; }
        tr td:last-child { border-right: 1px solid var(--border-color); border-radius: 0 12px 12px 0; }

        .status-Pending { color: #f59e0b; background: #fffbeb; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .status-Approved { color: #10b981; background: #ecfdf5; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        
        .btn-apply { background: var(--primary); color: white; border: none; padding: 14px; border-radius: 12px; cursor: pointer; width: 100%; font-weight: 600; font-size: 16px; transition: 0.3s; margin-top: 10px; }
        .btn-apply:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(92, 103, 242, 0.3); }

        .upload-btn { background: #eee; border: 1px dashed #ccc; padding: 5px; border-radius: 8px; cursor: pointer; font-size: 11px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Portal</h2>
    
    <div class="attendance-widget">
        <div class="attendance-label">Attendance Status</div>
        <div class="attendance-value" style="color: <?= ($attendance_percentage < 75) ? 'var(--warning)' : 'var(--success)' ?>;">
            <?= $attendance_percentage ?>%
        </div>
        <div style="font-size: 11px; color: #94a3b8;"><?= $current_attendance ?> days present</div>
        <div class="mini-progress-bg">
            <div class="mini-progress-fill"></div>
        </div>
    </div>

    <div class="nav-item active" id="nav-apply" onclick="showSection('apply')">📝 Apply Leave</div>
    <div class="nav-item" id="nav-history" onclick="showSection('history')">📜 Leave History</div>
    
    <div style="flex-grow: 1;"></div> 
    <a href="student_dashboard.php" style="text-decoration:none; padding:15px; text-align:center; color:#94a3b8; font-size:13px; border-top:1px solid rgba(255,255,255,0.1);">← Back to Dashboard</a>
</div>

<div class="main">
    <div id="apply-div">
        <div class="card">
            <h3 style="margin-top:0; color: var(--text-dark);">Apply for Leave</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Reason for Leave</label>
                    <textarea name="reason" rows="3" required placeholder="Ex: Medical emergency, Family function..."></textarea>
                </div>
                <div style="display:flex; gap:15px;">
                    <div class="form-group" style="flex:1;">
                        <label>From Date</label>
                        <input type="date" name="from_date" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>To Date (Optional for 1 Day)</label>
                        <input type="date" name="to_date">
                    </div>
                </div>
                <button type="submit" name="apply_leave" class="btn-apply">Submit Request</button>
            </form>
        </div>
    </div>

    <div id="history-div" style="display:none;">
        <div class="card" style="max-width: 900px;">
            <h3 style="margin-top:0; color: var(--text-dark);">Leave History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Dates</th>
                        <th>Status</th>
                        <th>Medical Certificate</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($history->num_rows > 0): ?>
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <b>
                                <?= date('d M', strtotime($row['from_date'])) ?>
                                <?php if($row['from_date'] != $row['to_date']): ?>
                                     - <?= date('d M', strtotime($row['to_date'])) ?>
                                <?php endif; ?>
                                </b>
                            </td>
                            <td><span class="status-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
      <td>
    <?php if($row['medical_certificate']): ?>
        <a href="uploads/<?= $row['medical_certificate'] ?>" target="_blank" style="text-decoration:none; color:var(--primary); font-weight:600; font-size: 12px; display: flex; align-items: center; gap: 5px;">
            <span style="font-size: 16px;">📄</span> View
        </a>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:5px;">
            <input type="hidden" name="leave_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="upload_cert" value="1"> 
            
            <label style="cursor:pointer; background:#f1f5f9; border:1px solid #cbd5e1; padding:4px 8px; border-radius:6px; font-size:10px; color:#475569; display:inline-block;">
                Choose File
                <input type="file" name="cert_file" required style="display:none;" onchange="this.form.submit()">
            </label>
            <span style="font-size:10px; color:#94a3b8;">(No cert)</span>
        </form>
    <?php endif; ?>
</td>               <td><?= htmlspecialchars($row['faculty_remarks'] ?: '-') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; color:#94a3b8;">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showSection(section) {
        document.getElementById('apply-div').style.display = (section === 'apply') ? 'block' : 'none';
        document.getElementById('history-div').style.display = (section === 'history') ? 'block' : 'none';
        
        document.getElementById('nav-apply').classList.remove('active');
        document.getElementById('nav-history').classList.remove('active');
        
        if(section === 'apply') document.getElementById('nav-apply').classList.add('active');
        else document.getElementById('nav-history').classList.add('active');
    }
</script>

</body>
</html>