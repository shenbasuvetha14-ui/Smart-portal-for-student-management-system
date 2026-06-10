<?php
session_start();
include 'config.php'; 

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$targetDir = "uploads/syllabus/";

// Dynamically calculate current automated batches
$current_year = (int)date("Y");
$auto_batches = [];
for ($i = 0; $i < 3; $i++) {
    $start = $current_year + $i;
    $end = $start + 3;
    $auto_batches[] = "{$start}-{$end}";
}

// --- DELETE SYLLABUS LOGIC ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // 1. ஃபைல் நேமை டேட்டாபேஸ்ல இருந்து எடுக்குறோம்
    $file_q = mysqli_query($conn, "SELECT file_name FROM syllabus_table WHERE id=$delete_id");
    if ($file_row = mysqli_fetch_assoc($file_q)) {
        $del_file = $targetDir . $file_row['file_name'];
        
        // 2. சர்வர் ஃபோல்டர்ல இருந்து ஃபைலை டெலீட் பண்றோம்
        if (file_exists($del_file)) {
            unlink($del_file);
        }
        
        // 3. டேட்டாபேஸ்ல இருந்து ரெக்கார்டை நீக்குறோம்
        mysqli_query($conn, "DELETE FROM syllabus_table WHERE id=$delete_id");
        echo "<script>alert('🗑️ Syllabus Removed Successfully!'); window.location.href='admin_syllabus.php?view_batch=".$_GET['view_batch']."';</script>";
    }
}

// --- FILE UPLOAD & DATABASE INSERT/UPDATE LOGIC ---
if (isset($_POST['admin_upload'])) {
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);      
    $class = mysqli_real_escape_string($conn, $_POST['class']);      
    $sem   = mysqli_real_escape_string($conn, $_POST['sem']);        
    
    $fileName = "syllabus_" . str_replace(' ', '_', $batch) . "_" . $class . "_" . $sem . ".pdf";
    
    if (!is_dir($targetDir)) { 
        mkdir($targetDir, 0777, true); 
    }
    
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFile)) {
        $check_query = "SELECT id FROM syllabus_table WHERE batch='$batch' AND class='$class' AND sem='$sem'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $save_query = "UPDATE syllabus_table SET file_name='$fileName', uploaded_at=CURRENT_TIMESTAMP WHERE batch='$batch' AND class='$class' AND sem='$sem'";
        } else {
            $save_query = "INSERT INTO syllabus_table (batch, class, sem, file_name) VALUES ('$batch', '$class', '$sem', '$fileName')";
        }
        
        if (mysqli_query($conn, $save_query)) {
            echo "<script>alert('✅ Syllabus Uploaded & Saved!'); window.location.href='admin_syllabus.php?view_batch=$batch';</script>";
        } else {
            echo "<script>alert('⚠️ DB Error: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('❌ Upload Failed to Folder!');</script>";
    }
}

// --- MATRIX STATUS SYSTEM ---
$selected_batch = isset($_GET['view_batch']) ? mysqli_real_escape_string($conn, $_GET['view_batch']) : $auto_batches[0];

// டேட்டாபேஸ்ல இருந்து ஐடி மற்றும் ஃபைல் நேமையும் சேர்த்து மேப் பண்றோம்
$uploaded_list = [];
$db_check = mysqli_query($conn, "SELECT id, class, sem, file_name FROM syllabus_table WHERE batch='$selected_batch'");
if ($db_check) {
    while ($r = mysqli_fetch_assoc($db_check)) {
        $uploaded_list[$r['class'] . '_' . $r['sem']] = [
            'id' => $r['id'],
            'file_name' => $r['file_name']
        ];
    }
}

$classes = ['I-BCA', 'II-BCA', 'III-BCA', 'I-MCA', 'II-MCA'];
$semesters = ['Odd', 'Even'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: Syllabus Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; padding: 20px; color: #333; }
        .dashboard { display: flex; gap: 30px; max-width: 1300px; margin: 30px auto; flex-wrap: wrap; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); flex: 1; min-width: 350px; }
        .upload-card { border-top: 5px solid #ff4d4d; max-width: 420px; height: fit-content; }
        .status-card { border-top: 5px solid #2ecc71; flex: 2; }
        
        h2 { margin-top: 0; color: #2c3e50; font-size: 22px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; font-size: 14px; }
        select, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background: #fff; font-size: 14px; }
        
        .btn-red { background: #ff4d4d; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 15px; margin-top: 10px; }
        .btn-red:hover { background: #e04343; }
        
        .status-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .status-table th, .status-table td { padding: 12px 10px; text-align: center; border-bottom: 1px solid #eee; }
        .status-table th { background-color: #f8f9fa; color: #666; font-weight: 600; }
        
        /* Action Box அலைன்மென்ட் */
        .action-box { display: flex; flex-direction: column; align-items: center; gap: 6px; background: #e8f8f0; padding: 8px; border-radius: 8px; border: 1px solid #2ecc71; }
        .action-box.pending { background: #fdf2e2; border: 1px solid #f39c12; color: #d35400; font-weight: bold; font-size: 13px; padding: 12px; }
        
        .badge-txt { color: #2ecc71; font-weight: bold; font-size: 12px; }
        .action-links { display: flex; gap: 10px; margin-top: 2px; }
        
        /* பட்டன் ஸ்டைல்கள் */
        .btn-action { text-decoration: none; font-size: 12px; padding: 4px 8px; border-radius: 4px; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; }
        .btn-view { background: #3498db; color: white; }
        .btn-view:hover { background: #2980b9; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-delete:hover { background: #c0392b; }
        
        .batch-filter { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; background: #eef2f5; padding: 10px 15px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="dashboard">
    
    <div class="card upload-card">
        <h2><i class="fa fa-upload"></i> Upload Syllabus</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>ACADEMIC BATCH</label>
                <select name="batch" required>
                    <?php
                    foreach ($auto_batches as $b_opt) {
                        $selected = ($b_opt == $selected_batch) ? 'selected' : '';
                        echo "<option value='$b_opt' $selected>$b_opt</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Target Class</label>
                <select name="class" required>
                    <?php foreach($classes as $c): ?>
                        <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <select name="sem" required>
                    <option value="Odd">Odd Semester</option>
                    <option value="Even">Even Semester</option>
                </select>
            </div>
            <div class="form-group">
                <label>Syllabus PDF</label>
                <input type="file" name="pdf_file" accept=".pdf" required>
            </div>
            <button type="submit" name="admin_upload" class="btn-red">PUBLISH NOW</button>
        </form>
    </div>

    <div class="card status-card">
        <h2><i class="fa fa-chart-bar"></i> Syllabus Live Tracker Dashboard</h2>
        
        <form method="GET" class="batch-filter">
            <label style="margin:0; min-width:max-content; font-size:14px;">Viewing Status for Batch:</label>
            <select name="view_batch" onchange="this.form.submit()" style="padding: 6px; width: auto; font-size:13px; cursor:pointer;">
                <?php
                foreach ($auto_batches as $b_opt) {
                    $sel = ($b_opt == $selected_batch) ? 'selected' : '';
                    echo "<option value='$b_opt' $sel>$b_opt</option>";
                }
                ?>
            </select>
        </form>

        <table class="status-table">
            <thead>
                <tr>
                    <th style="text-align: left; width: 25%;">Class</th>
                    <th style="width: 37%;">Odd Sem Syllabus</th>
                    <th style="width: 37%;">Even Sem Syllabus</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $cls): ?>
                    <tr>
                        <td style="text-align: left; font-weight: bold; color: #444; font-size: 15px;"><?php echo $cls; ?></td>
                        
                        <?php foreach ($semesters as $sm): ?>
                            <td>
                                <?php
                                $matchKey = $cls . '_' . $sm;
                                
                                if (isset($uploaded_list[$matchKey])) {
                                    $fileData = $uploaded_list[$matchKey];
                                    $filePath = $targetDir . $fileData['file_name'];
                                    ?>
                                    <div class="action-box">
                                        <span class="badge-txt"><i class="fa-solid fa-circle-check"></i> Uploaded</span>
                                        <div class="action-links">
                                            <a href="<?php echo $filePath; ?>" target="_blank" class="btn-action btn-view" title="View or Copy link to share">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <a href="admin_syllabus.php?delete_id=<?php echo $fileData['id']; ?>&view_batch=<?php echo $selected_batch; ?>" 
                                               class="btn-action btn-delete" 
                                               onclick="return confirm('Are you sure you want to delete this syllabus?');">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                                                       echo '<div class="action-box pending"><i class="fa-solid fa-circle-xmark"></i> Pending</div>';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>