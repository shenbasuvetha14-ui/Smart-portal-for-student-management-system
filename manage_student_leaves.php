<?php
session_start();
include 'config.php';

// Faculty லாகின் செய்துள்ளாரா எனச் சரிபார்க்கிறது
if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: faculty_login.php");
    exit();
}

$my_class = $_SESSION['class_incharge']; 

// --- 1. STATUS UPDATE LOGIC ---
if (isset($_POST['update_status'])) {
    $l_id = $_POST['leave_id'];
    $new_status = $_POST['status'];
    $rem = mysqli_real_escape_string($conn, $_POST['remarks']);
    $upd = "UPDATE leave_requests SET status = '$new_status', faculty_remarks = '$rem' WHERE id = '$l_id'";
    
    if ($conn->query($upd)) {
        echo "<script>alert('Status updated to $new_status!'); window.location.href=window.location.href;</script>";
    }
}

// --- 2. CERTIFICATE VERIFICATION LOGIC ---
if (isset($_POST['verify_cert'])) {
    $l_id = $_POST['leave_id'];
    $upd_cert = "UPDATE leave_requests SET cert_verified = 1 WHERE id = '$l_id'";
    $conn->query($upd_cert);
}

// --- 3. NOTIFICATION COUNT ---
$notif = $conn->query("SELECT COUNT(*) as total FROM leave_requests WHERE status = 'Pending' AND class_name = '$my_class'")->fetch_assoc();
$pending_count = $notif['total'] ?? 0;

// --- 4. FETCH REQUESTS ---
$requests = $conn->query("
    SELECT l.*, s.name 
    FROM leave_requests l 
    LEFT JOIN student_table s ON l.s_reg = s.reg_no COLLATE utf8mb4_unicode_ci
    WHERE l.class_name COLLATE utf8mb4_unicode_ci = '$my_class' 
    ORDER BY l.applied_on DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management | Modern Clean UI</title>
    <style>
        body { 
            margin: 0;
            padding: 20px;
            font-family: 'Inter', -apple-system, sans-serif; 
            background: #f0f2f5;
            color: #1c1e21;
        }

        .main-card { 
            background: white;
            padding: 0; 
            border-radius: 12px; 
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            max-width: 1200px;
            margin: 20px auto;
            overflow: hidden;
        }

        .header-section {
            padding: 25px 30px;
            border-bottom: 1px solid #ebedf0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h2 { margin: 0; font-size: 1.5rem; color: #1a73e8; }

        .badge { 
            background: #d93025; 
            color: white; 
            padding: 3px 10px; 
            border-radius: 20px; 
            font-size: 13px;
            font-weight: bold;
        }

        .back-link {
            text-decoration: none;
            color: #5f6368;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        table { width: 100%; border-collapse: collapse; }

        th { 
            background: #f8f9fa;
            padding: 15px 20px; 
            text-align: left; 
            font-size: 12px; 
            text-transform: uppercase;
            color: #5f6368;
            font-weight: 600;
        }

        td { 
            padding: 20px; 
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        /* Status Colors */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-Pending { background: #fff4e5; color: #b95000; }
        .status-Approved { background: #e6f4ea; color: #1e8e3e; }
        .status-Denied { background: #fce8e6; color: #d93025; }

        /* Form Elements */
        .remarks-box {
            width: 100%;
            padding: 8px;
            border: 1px solid #dadce0;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 8px;
            box-sizing: border-box;
        }

        .btn-group { display: flex; gap: 8px; }

        .action-btn {
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-approve { background: #1e8e3e; color: white; }
        .btn-approve:hover { background: #187130; }

        .btn-deny { background: #d93025; color: white; }
        .btn-deny:hover { background: #b3261e; }

        .btn-verify { 
            background: #e8f0fe; 
            color: #1a73e8; 
            padding: 6px 12px; 
            border-radius: 4px;
            font-size: 11px;
            margin-top: 10px;
            border: 1px solid #1a73e8;
            cursor: pointer;
        }

        .student-info b { display: block; color: #202124; margin-bottom: 4px; }
        .student-info span { color: #70757a; font-size: 12px; }

        .view-doc { color: #1a73e8; text-decoration: none; font-weight: 500; font-size: 13px; }
        .view-doc:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="main-card">
    <div class="header-section">
        <div>
            <a href="faculty_dashboard.php" class="back-link">← Faculty Dashboard</a>
            <h2>Leave Management: <?= htmlspecialchars($my_class ?? '') ?></h2>
        </div>
        <?php if($pending_count > 0): ?> 
            <div class="badge"><?= $pending_count ?> Pending Requests</div> 
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">Student</th>
                <th width="25%">Reason</th>
                <th width="20%">Documents</th>
                <th width="10%">Status</th>
                <th width="25%">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($requests && $requests->num_rows > 0): ?>
                <?php while($row = $requests->fetch_assoc()): ?>
                <tr>
                    <td class="student-info">
                        <b><?= htmlspecialchars($row['name'] ?? '') ?></b>
                        <span>Reg No: <?= htmlspecialchars($row['s_reg'] ?? '') ?></span>
                    </td>
                    <td>
                        <div style="font-size: 14px; line-height: 1.4;"><?= htmlspecialchars($row['leave_reason'] ?? '') ?></div>
                    </td>
                    <td>
                        <?php if(!empty($row['medical_certificate'])): ?>
                            <a href="uploads/<?= $row['medical_certificate'] ?>" target="_blank" class="view-doc">📄 View Certificate</a>
                            <?php if($row['cert_verified'] == 0): ?>
                                <form method="POST">
                                    <input type="hidden" name="leave_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="verify_cert" class="btn-verify">Mark as Verified</button>
                                </form>
                            <?php else: ?>
                                <div style="color: #1e8e3e; font-size: 12px; margin-top: 5px; font-weight: 600;">✅ Verified</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #9aa0a6; font-size: 13px;">No attachment</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $row['status'] ?>">
                            <?= htmlspecialchars($row['status'] ?? 'Pending') ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="leave_id" value="<?= $row['id'] ?>">
                            <input type="text" name="remarks" class="remarks-box" placeholder="Write remarks here..." value="<?= htmlspecialchars($row['faculty_remarks'] ?? '') ?>">
                            <input type="hidden" name="status" id="s_<?= $row['id'] ?>">
                            
                            <div class="btn-group">
                                <?php if($row['status'] != 'Approved'): ?>
                                    <button type="submit" name="update_status" class="action-btn btn-approve" onclick="document.getElementById('s_<?= $row['id'] ?>').value='Approved'">Approve</button>
                                <?php endif; ?>
                                
                                <?php if($row['status'] != 'Denied'): ?>
                                    <button type="submit" name="update_status" class="action-btn btn-deny" onclick="document.getElementById('s_<?= $row['id'] ?>').value='Denied'">Deny</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 60px; color: #70757a;">
                        No leave requests found for your class.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>