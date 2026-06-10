<?php 
session_start();
include 'config.php'; 

// Faculty login check and assigned class fetch
if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: faculty_login.php");
    exit();
}

// Updated session key here
$my_class = $_SESSION['class_incharge'] ?? ''; 

if(isset($_GET['app_id'])) {
    $id = intval($_GET['app_id']); 

    mysqli_query($conn, "UPDATE permission_request 
        SET faculty_status = 1, 
            overall_status = 'Pending Admin Approval' 
        WHERE id = $id");

    header("Location: faculty_permission_approval.php?msg=done");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty | Event Permissions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card-bg: #1e293b;
            --accent: #06b6d4;
            --success: #10b981;
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --event-type-color: #a855f7; 
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            color: var(--text-main); 
            margin: 0; 
            padding: 40px; 
        }

        .container { max-width: 1100px; margin: auto; }
        .header { margin-bottom: 30px; }
        .header h2 { font-size: 24px; font-weight: 700; color: var(--accent); margin: 0; }
        .header p { color: var(--text-dim); margin-top: 5px; }

        .card { 
            background: var(--card-bg); 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { 
            background: rgba(255,255,255,0.03); 
            padding: 18px; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: var(--text-dim);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }

        .student-name { font-weight: 600; color: #fff; display: block; }
        .student-class { 
            font-size: 11px; 
            color: var(--accent); 
            font-weight: 700; 
            background: rgba(6, 182, 212, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
        }
        
        .event-name { color: #fff; font-size: 14px; font-weight: 600; display: block; margin-bottom: 2px;}
        
        .event-type {
            font-size: 11px;
            color: var(--event-type-color);
            background: rgba(168, 85, 247, 0.1);
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 5px;
        }

        .event-date { 
            display: inline-block;
            font-size: 12px;
            color: #fbbf24; 
            background: rgba(251, 191, 36, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .btn-view {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-view:hover { background: rgba(255,255,255,0.15); }

        .btn-approve {
            background: var(--success);
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-approve:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }

        .status-badge {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .empty { padding: 40px; text-align: center; color: var(--text-dim); }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Faculty Approval Dashboard (Class: <?= htmlspecialchars($my_class ?? '') ?>)</h2>
        <p>Review student requests for your assigned class.</p>
  
        <a href="faculty_dashboard.php" style="font-size: 14px; text-decoration: none; color: #64748b; font-weight: 600;">← Back to Home</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Student & Class</th>
                    <th>Event Details</th>
                    <th>Invitation</th>
                    <th>Action</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query uses the updated $my_class variable
                $res = mysqli_query($conn, "SELECT * FROM permission_request WHERE class_name = '$my_class' ORDER BY faculty_status ASC, id DESC");
                
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $status = $row['faculty_status'];
                        
                        $f_date_raw = $row['from_date'] ?? ''; 
                        $t_date_raw = $row['to_date'] ?? '';

                        $f_date = (!empty($f_date_raw)) ? date('d M Y', strtotime($f_date_raw)) : "N/A";
                        $t_date = (!empty($t_date_raw)) ? date('d M Y', strtotime($t_date_raw)) : "N/A";

                        $date_display = ($f_date == $t_date || $t_date == "N/A") ? $f_date : "$f_date to $t_date";
                ?>
                        <tr>
                            <td>
                                <span class="student-name"><?= htmlspecialchars($row['student_name']) ?></span>
                                <span class="student-class"><?= htmlspecialchars($row['class_name'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <span class="event-name"><?= htmlspecialchars($row['event_name']) ?></span>
                                <span class="event-type"><?= htmlspecialchars($row['event_type'] ?? 'General') ?></span><br>
                                <span style="font-size:12px;color:#38bdf8;">
                                🏫 <?= htmlspecialchars($row['college_name'] ?? 'N/A') ?>
                                </span><br>
                                <span class="event-date">📅 <?= $date_display ?></span>
                            </td>
                            <td>
                                <a href="uploads/events/<?= $row['invitation_file'] ?>" target="_blank" class="btn-view">📄 View File</a>
                            </td>
                            <td>
                                <?php if($status == 0): ?>
                                    <a href="?app_id=<?= $row['id'] ?>" class="btn-approve" onclick="return confirm('Confirm faculty verification?')">Approve</a>
                                <?php else: ?>
                                    <span class="status-badge">✓ Verified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if(!empty($row['certificate_file'])) {
                                    echo "<a href='uploads/certificates/".$row['certificate_file']."' target='_blank' class='btn-view'>📄 View Certificate</a>";
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5' class='empty'>No requests found for your class.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>