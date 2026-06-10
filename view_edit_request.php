<?php
session_start();
include('config.php');

// 1. Session Check
if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: faculty_login.php");
    exit();
}

// 2. Define the variable using the session data
// This fixes the "Undefined variable $class_incharge" error
$class_incharge = $_SESSION['class_incharge'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Edit Requests | Faculty View</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1a1a1a; color: white; padding: 40px; }
        .container { max-width: 900px; margin: auto; background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px; border: 1px solid #444; }
        h2 { border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #444; }
        th { background-color: #28a745; color: white; }
        tr:hover { background: rgba(255,255,255,0.05); }
        .btn { padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-approve { background: #28a745; color: white; margin-right: 5px; }
        .btn-reject { background: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Requests for <?php echo htmlspecialchars($class_incharge); ?></h2>
    
    <table>
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Student Name</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
// 3. Corrected SQL Query
// We use COLLATE to resolve the "Illegal mix of collations" error
$sql = "SELECT d.s_reg, s.name, d.edit_reason 
        FROM student_details_table d
        JOIN student_table s ON d.s_reg = s.reg_no COLLATE utf8mb4_unicode_ci
        WHERE TRIM(d.class_name) COLLATE utf8mb4_unicode_ci = '$class_incharge' 
        AND d.edit_status = 'Pending'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['s_reg']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['edit_reason']) . "</td>
                <td>
                    <a href='approve_logic.php?reg=" . urlencode($row['s_reg']) . "&status=Approved' class='btn btn-approve'>Approve</a>
                    <a href='approve_logic.php?reg=" . urlencode($row['s_reg']) . "&status=Rejected' class='btn btn-reject'>Reject</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4' style='text-align:center;'>No pending requests for your class.</td></tr>";
}
?>
        </tbody>        
    </table>
    <br>
    <a href="faculty_dashboard.php" style="color: #28a745; text-decoration: none;">← Back to Dashboard</a>
</div>
</body>
</html>