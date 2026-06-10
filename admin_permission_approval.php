<?php 
// 1. Database Connection
$conn = mysqli_connect("localhost", "root", "", "college_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Final Approval Logic
if (isset($_GET['final_id'])) {
    $id = intval($_GET['final_id']);
    
    // Faculty status 1-ah (Verified) iruntha mattum thaan Admin approve panna mudiyum
    $sql = "UPDATE permission_request 
            SET admin_status = 1, overall_status = 'Approved' 
            WHERE id = $id AND faculty_status = 1";
            
    if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
        header("Location: admin_permission_approval.php?msg=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Final Permission Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #c0392b; /* Admin Red */
            --dark: #0f172a;
            --success: #10b981;
            --bg: #f8fafc;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            padding: 30px; 
            color: #334155;
        }

        .container { max-width: 1200px; margin: auto; }

        .header {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid var(--primary);
        }

        .header h2 { margin: 0; font-size: 22px; color: var(--dark); }
        .header p { margin: 5px 0 0 0; font-size: 14px; color: #64748b; }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        
        th { 
            background: #f1f5f9; 
            padding: 18px; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        td { padding: 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        .student-name { font-weight: 700; color: var(--dark); display: block; }
        
        .class-badge {
            font-size: 11px;
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            margin-top: 4px;
        }

        .event-title { font-weight: 600; color: #1e293b; display: block; margin-bottom: 2px; }
        
        .event-type-badge {
            font-size: 11px;
            background: #f3e8ff;
            color: #7e22ce;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 5px;
        }

        .date-text { 
            display: inline-block;
            font-size: 12px; 
            color: #d97706; 
            background: #fffbeb;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 5px;
        }

        .btn-file {
            text-decoration: none;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #dbeafe;
            padding: 6px 12px;
            border-radius: 8px;
            background: #eff6ff;
            transition: 0.3s;
        }

        .status-verified {
            color: #059669;
            font-size: 13px;
            font-weight: 700;
        }

        .btn-approve {
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            transition: 0.3s;
            display: inline-block;
        }

        .approved-tag {
            background: #dcfce7;
            color: #15803d;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
        }

        .alert {
            background: #dcfce7;
            color: #15803d;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h2>Admin Final Approval Dashboard</h2>
            <p>Processing requests verified by Faculty Members</p>
        </div>
        <a href="admin_dashboard.php" style="font-size: 14px; text-decoration: none; color: #64748b; font-weight: 600;">← Back to Home</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert">✓ Permission Approved Successfully!</div>
    <?php endif; ?>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Event & Date Details</th>
                    <th>Invitation</th>
                    <th>Faculty Status</th>
                    <th>Action</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Standardised to pull from 'class_name'
                $query = "SELECT * FROM permission_request WHERE faculty_status = 1 ORDER BY admin_status ASC, id DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $f_raw = $row['from_date'] ?? ''; 
                        $t_raw = $row['to_date'] ?? '';

                        $from = (!empty($f_raw)) ? date('d M Y', strtotime($f_raw)) : "N/A";
                        $to   = (!empty($t_raw)) ? date('d M Y', strtotime($t_raw)) : "N/A";

                        $display_date = ($from == $to || $to == "N/A") ? $from : "$from - $to";
                ?>
                    <tr>
                        <td>
                            <span class="student-name"><?php echo htmlspecialchars($row['student_name']); ?></span>
                            <span class="class-badge"><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></span>
                        </td>
                        <td>
                            <div class="event-title"><?php echo htmlspecialchars($row['event_name']); ?></div>
                            <div class="event-type-badge"><?php echo htmlspecialchars($row['event_type'] ?? 'General'); ?></div><br>

                            <div style="font-size:12px;color:#2563eb;font-weight:600;">
                                🏫 <?php echo htmlspecialchars($row['college_name'] ?? 'N/A'); ?>
                            </div>

                            <div class="date-text">📅 <?php echo $display_date; ?></div>
                        </td>
                        <td>
                            <a href="uploads/events/<?php echo $row['invitation_file']; ?>" target="_blank" class="btn-file">📄 View File</a>
                        </td>
                        <td>
                            <span class="status-verified">✅ Verified</span>
                        </td>
                        <td>
                            <?php if($row['admin_status'] == 0): ?>
                                <a href="?final_id=<?php echo $row['id']; ?>" class="btn-approve" onclick="return confirm('Grant final approval?')">Approve Final</a>
                            <?php else: ?>
                                <span class="approved-tag">✓ Approved</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            if(!empty($row['certificate_file'])) {
                                echo "<a href='uploads/certificates/".$row['certificate_file']."' target='_blank' class='btn-file'>📄 View Certificate</a>";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 50px; color: #94a3b8;'>No faculty-verified requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>