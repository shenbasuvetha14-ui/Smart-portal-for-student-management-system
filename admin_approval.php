<?php
include 'config.php'; 

// Fix: Pending aaga irukura requests-ai mattum fetch pandrom
$sql = "SELECT * FROM faculty_leaves WHERE (status IS NULL OR status = '' OR status = 'Pending') ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Leave Approvals</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 1300px; margin: auto; }
        h2 { color: #7d4f4d; text-align: center; margin-bottom: 30px; font-size: 28px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border-bottom: 1px solid #eee; padding: 15px 12px; text-align: left; }
        th { background-color: #7d4f4d; color: white; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        tr:hover { background-color: #fcfcfc; }
        .doc-container { display: flex; flex-direction: column; gap: 8px; }
        .doc-link { text-decoration: none; font-size: 12px; font-weight: 600; padding: 5px 8px; border-radius: 4px; border: 1px solid #ddd; display: inline-flex; align-items: center; gap: 5px; width: fit-content; }
        .initial-doc { color: #007bff; border-color: #007bff; }
        .fitness-doc { color: #28a745; border-color: #28a745; }
        .btn-group { display: flex; gap: 8px; }
        .action-btn { padding: 8px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; text-align: center; transition: 0.3s; }
        .approve-btn { background-color: #28a745; color: white; }
        .reject-btn { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Pending Faculty Leave Requests</h2>
    <table>
        <thead>
            <tr>
                <th>Faculty Details</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Verification Docs</th> 
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { 
                    $medicalFile = $row['initial_document'] ?? null; 
                    $fitnessFile = $row['fitness_certificate'] ?? null;
            ?>
            <tr>
                <td><strong><?php echo $row['faculty_id']; ?></strong></td>
                <td><?php echo $row['leave_type']; ?></td>
                <td style="font-size: 13px;">
                    <div style="color: #28a745;"><b>From:</b> <?php echo $row['from_date']; ?></div>
                    <div style="color: #dc3545;"><b>To:</b> <?php echo $row['to_date']; ?></div>
                </td>
                <td><b><?php echo $row['total_days']; ?></b></td>
                <td style="font-size: 13px; max-width: 150px;"><?php echo $row['reason']; ?></td>
                <td>
                    <div class="doc-container">
                        <?php 
                        $any_file = false;
                        if(!empty($medicalFile)) {
                            $path = "uploads/" . $medicalFile; 
                            $label = ($row['leave_type'] == 'OD') ? "📄 OD Invitation" : "📄 Medical Cert";
                            if(file_exists($path)) {
                                echo "<a href='$path' class='doc-link initial-doc' download>📥 Download $label</a>";
                            } else {
                                echo "<span style='color:orange; font-size:11px;'>File Missing</span>";
                            }
                            $any_file = true;
                        }
                        if(!$any_file) { echo "<span style='color:#999; font-size:12px;'>No documents</span>"; }
                        ?>
                    </div>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="leave_action.php?id=<?php echo $row['id']; ?>&status=Granted" class="action-btn approve-btn" onclick="return confirm('Approve?')">Approve</a>
                        <a href="leave_action.php?id=<?php echo $row['id']; ?>&status=Rejected" class="action-btn reject-btn" onclick="return confirm('Reject?')">Reject</a>
                    </div>
                </td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding: 50px; color: #999;'>No pending requests.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>