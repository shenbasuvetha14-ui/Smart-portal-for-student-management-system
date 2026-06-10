<?php
session_start();
include 'config.php';

// 1. Save Entry Logic
if(isset($_POST['save_allocation'])) {
    $dept = $_POST['dept'];
    $code = mysqli_real_escape_string($conn, $_POST['sub_code']);
    $name = mysqli_real_escape_string($conn, $_POST['sub_name']);
    $fid = $_POST['fac_id'];

    $sql = "INSERT INTO timetable_allocation (department, subject_code, subject_name, faculty_id) 
            VALUES ('$dept', '$code', '$name', '$fid')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Subject Allocated Successfully!'); window.location='allocate_faculty.php';</script>";
    }
}

// 2. Delete Logic (Thappa entry pottutta delete panna)
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM timetable_allocation WHERE id = '$id'");
    header("Location: allocate_faculty.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Allocate Faculty to Subjects</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1a1a1a; color: white; padding: 40px; }
        .form-panel { background: rgba(255,255,255,0.05); padding: 25px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px; }
        input, select { padding: 10px; margin: 10px 0; width: 200px; border-radius: 5px; border: none; }
        .btn { background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; }
        th { background: #333; color: #ffc107; }
        .del-btn { color: #ff4444; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Step 1: Allocate Faculty to Subjects (Master Entry)</h2>
    
    <div class="form-panel">
        <form method="POST">
            <select name="dept" required>
                <option value="">-- Select Class --</option>
                <option value="I-BCA">I-BCA</option>
                <option value="II-BCA">II-BCA</option>
                <option value="III-BCA">III-BCA</option>
            </select>

            <input type="text" name="sub_code" placeholder="Subject Code (e.g. BCA301)" required>
            <input type="text" name="sub_name" placeholder="Subject Name" required>

            <select name="fac_id" required>
                <option value="">-- Select Faculty --</option>
                <?php 
                $facs = mysqli_query($conn, "SELECT faculty_id, name, short_name FROM faculty_table");
                while($f = mysqli_fetch_assoc($facs)) {
                    echo "<option value='{$f['faculty_id']}'>{$f['name']} ({$f['short_name']})</option>";
                }
                ?>
            </select>

            <button type="submit" name="save_allocation" class="btn">Allocate Subject</button>
        </form>
    </div>

    <h3>Existing Allocations</h3>
    <table>
        <tr>
            <th>Class</th>
            <th>Sub Code</th>
            <th>Subject Name</th>
            <th>Faculty (Short Name)</th>
            <th>Action</th>
        </tr>
        <?php 
        $list = mysqli_query($conn, "SELECT t.*, f.name, f.short_name FROM timetable_allocation t JOIN faculty_table f ON t.faculty_id = f.faculty_id");
        while($row = mysqli_fetch_assoc($list)): ?>
        <tr>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['subject_code']; ?></td>
            <td><?php echo $row['subject_name']; ?></td>
            <td><?php echo $row['name']; ?> (<b><?php echo $row['short_name']; ?></b>)</td>
            <td><a href="?delete=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('Delete this?')">Remove</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>