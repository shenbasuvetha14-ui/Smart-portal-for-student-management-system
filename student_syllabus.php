<?php
session_start();
include 'config.php';
// if (!isset($_SESSION['student_logged_in'])) { header("Location: login.php"); exit(); }

$selected_batch = isset($_POST['batch']) ? mysqli_real_escape_string($conn, $_POST['batch']) : '';
$selected_class = isset($_POST['class']) ? mysqli_real_escape_string($conn, $_POST['class']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Syllabus | Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 30px; }
        
        .filter-card { 
            background: white; padding: 20px; border-radius: 15px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px;
        }
        
        .grid-filters { display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end; }
        
        select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #fff; }
        .btn-search { background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; height: 46px; }
        .btn-search:hover { background: #0056b3; }
        
        .syllabus-item {
            background: white; padding: 20px; border-radius: 12px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 15px; border-left: 5px solid #28a745; /* வெற்றிகரமாக உள்ளதை குறிக்க பச்சை நிறம் */
            transition: 0.3s;
        }
        .syllabus-item:hover { transform: scale(1.02); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .pdf-info { display: flex; align-items: center; gap: 15px; }
        .pdf-icon { font-size: 30px; color: #ff4d4d; }
        .file-name { font-weight: 600; color: #333; }
        
        .download-link { 
            background: #28a745; color: white; text-decoration: none; 
            padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: bold;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .download-link:hover { background: #218838; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📚 Academic Syllabus</h1>
        <p>Select your batch and class to download the latest syllabus</p>
    </div>

    <div class="filter-card">
        <form method="POST" class="grid-filters">
            <div>
                <label style="font-size: 12px; font-weight: bold; color: #666; display:block; margin-bottom:5px;">BATCH</label>
                <select name="batch" required>
                    <option value="">-- Select Batch --</option>
                    <?php
                    // டேட்டாபேஸில் டேட்டா இருந்தால் அங்கிருந்து எடுக்கும், இல்லையெனில் தானாகவே நடப்பு வருடத்திலிருந்து 3 பேட்ச்களை உருவாக்கும்
                    $b_res = mysqli_query($conn, "SELECT DISTINCT batch FROM student_table ORDER BY batch DESC LIMIT 3");
                    
                    if ($b_res && mysqli_num_rows($b_res) > 0) {
                        while($b_row = mysqli_fetch_assoc($b_res)){
                            $s = ($selected_batch == $b_row['batch']) ? 'selected' : '';
                            echo "<option value='{$b_row['batch']}' $s>{$b_row['batch']}</option>";
                        }
                    } else {
                        // AUTOMATIC GENERATOR (டேட்டாபேஸில் ரெக்கார்ட்ஸ் இல்லாத பட்சத்தில்)
                        $current_year = (int)date("Y"); 
                        for ($i = -1; $i < 2; $i++) { // கடந்த வருடம், இந்த வருடம், அடுத்த வருடம்
                            $start = $current_year + $i;
                            $end = $start + 3;
                            $b_opt = "{$start}-{$end}";
                            $s = ($selected_batch == $b_opt) ? 'selected' : '';
                            echo "<option value='$b_opt' $s>$b_opt</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: bold; color: #666; display:block; margin-bottom:5px;">CLASS</label>
                <select name="class" required>
                    <option value="">-- Select Class --</option>
                    <option value="I-BCA" <?= ($selected_class == 'I-BCA') ? 'selected' : '' ?>>I-BCA</option>
                    <option value="II-BCA" <?= ($selected_class == 'II-BCA') ? 'selected' : '' ?>>II-BCA</option>
                    <option value="III-BCA" <?= ($selected_class == 'III-BCA') ? 'selected' : '' ?>>III-BCA</option>
                    <option value="I-MCA" <?= ($selected_class == 'I-MCA') ? 'selected' : '' ?>>I-MCA</option>
                    <option value="II-MCA" <?= ($selected_class == 'II-MCA') ? 'selected' : '' ?>>II-MCA</option>
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> SEARCH</button>
        </form>
    </div>

    <div class="results">
        <?php
        if ($selected_batch && $selected_class) {
            
            // டேட்டாபேஸில் இருந்து நேரடியாக மாணவர் தேர்ந்தெடுத்த விவரங்களை எடுக்கிறோம்
            $query = "SELECT * FROM syllabus_table WHERE batch='$selected_batch' AND class='$selected_class' ORDER BY sem DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $fileName = $row['file_name'];
                    $filePath = "uploads/syllabus/" . $fileName;
                    $semester = $row['sem'];

                    // ஒருவேளை சர்வர் ஃபோல்டரில் ஃபைல் ஏதேனும் காரணத்தால் மிஸ்ஸாகியிருந்தால் செக் செய்ய
                    if (file_exists($filePath)) {
                        ?>
                        <div class="syllabus-item">
                            <div class="pdf-info">
                                <i class="fas fa-file-pdf pdf-icon"></i>
                                <div>
                                    <div class="file-name"><?= htmlspecialchars($selected_class) ?> - Syllabus</div>
                                    <div style="font-size: 12px; color: #888;">
                                        Semester: <strong><?= htmlspecialchars($semester) ?> Semester</strong> | Batch: <?= htmlspecialchars($selected_batch) ?>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= $filePath ?>" class="download-link" download>
                                <i class="fas fa-download"></i> DOWNLOAD
                            </a>
                        </div>
                        <?php
                    }
                }
            } else {
                                echo "<div style='text-align:center; padding: 40px; color: #999; background: white; border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                        <i class='fas fa-folder-open' style='font-size: 40px; color: #ccc; margin-bottom: 10px;'></i>
                        <p style='margin:0; font-weight:600;'>No syllabus published yet!</p>
                        <p style='font-size:13px; color:#aaa; margin-top:5px;'>Syllabus for this batch and class will be available once the admin uploads it.</p>
                      </div>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>