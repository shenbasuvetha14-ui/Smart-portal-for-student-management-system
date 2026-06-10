<?php
session_start();
include 'config.php';

// --- ADMIN CHECK ---
// Unga admin login logic-la $_SESSION['role'] == 'admin' nu vachirundha adhai inge use pannunga
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// --- FILE UPLOAD LOGIC (Only for Admin) ---
if ($is_admin && isset($_POST['upload_syllabus'])) {
    $year = $_POST['upload_year'];
    $sem = $_POST['upload_sem'];
    
    // File format: syllabus_IYear_Odd.pdf
    $formattedYear = str_replace(' ', '', $year);
    $formattedSem = explode(' ', $sem)[0];
    $file_name = "syllabus_" . $formattedYear . "_" . $formattedSem . ".pdf";
    
    if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $file_name)) {
        echo "<script>alert('Syllabus Updated Successfully: $file_name');</script>";
    } else {
        echo "<script>alert('Upload Failed! Check folder permissions.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VCW - Student Portal</title>
    <style>
        body {
            font-family: 'Cambria', serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background: #222; 
            color: white;
            overflow: hidden;
        }

        /* Sidebar Style */
        .sidebar {
            width: 300px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #E67E73;
        }

        .sidebar h2 { text-align: center; color: #E67E73; margin-bottom: 30px; }
        .menu-item { margin-bottom: 15px; }
        .main-cat {
            width: 100%; padding: 12px; background: #333; color: white; border: none;
            text-align: left; cursor: pointer; border-radius: 5px; display: flex; justify-content: space-between;
        }

        .year-list { display: none; flex-direction: column; padding-left: 20px; margin-top: 10px; gap: 8px; }
        .year-link { padding: 8px; background: rgba(255, 255, 255, 0.1); color: #ddd; cursor: pointer; border-radius: 4px; }
        .year-link:hover { background: #E67E73; color: white; }

        .main-content { flex: 1; padding: 40px; display: flex; flex-direction: column; align-items: center; overflow-y: auto; }

        /* Admin Section */
        .admin-section {
            width: 100%;
            max-width: 800px;
            background: rgba(230, 126, 115, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px dashed #E67E73;
            text-align: center;
        }
        .admin-section select, .admin-section input {
            padding: 8px; margin: 5px; border-radius: 4px; border: 1px solid #444; background: #333; color: white;
        }

        /* Boxes & UI */
        .nav-controls { position: fixed; top: 20px; right: 30px; display: flex; gap: 20px; }
        .home-btn { padding: 10px 20px; background: #E67E73; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .download-icon { display: none; font-size: 28px; color: #E67E73; text-decoration: none; padding: 5px 10px; }

        .box-container { display: none; gap: 20px; margin-top: 30px; }
        .year-box {
            width: 160px; height: 100px; background: #E67E73; color: white;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px; cursor: pointer; font-weight: bold; transition: 0.3s;
        }
        .year-box:hover { transform: translateY(-5px); background: #c85c52; }

        .sem-container { display: none; gap: 30px; margin-top: 40px; border-top: 1px solid rgba(230, 126, 115, 0.3); padding-top: 30px; }
        .sem-box {
            width: 220px; height: 120px; background: linear-gradient(145deg, #2c2c2c, #1a1a1a);
            color: #E67E73; display: flex; flex-direction: column; align-items: center; justify-content: center;
            border-radius: 15px; cursor: pointer; border: 1px solid rgba(230, 126, 115, 0.2);
        }
        .sem-box:hover { background: #E67E73; color: white; transform: translateY(-10px); }

        .syllabus-panel { width: 100%; margin-top: 40px; border-left: 5px solid #E67E73; display: none; padding-left: 20px;}
        .view-btn { padding: 12px 35px; background: #E67E73; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="nav-controls">
    <a id="dlIcon" href="#" download class="download-icon" title="Download Syllabus">📥</a>
    <a href="index1.php" class="home-btn">Home</a>
</div>

<div class="sidebar">
    <h2>STUDENT PORTAL</h2>
    <div class="menu-item">
        <button class="main-cat" onclick="toggleMenu('ug-years')">Undergraduate (UG) <span>▼</span></button>
        <div id="ug-years" class="year-list">
             <div class="year-link" onclick="openYear('UG', '2023-24')">Batch 2023-2024</div>
             <div class="year-link" onclick="openYear('UG', '2024-25')">Batch 2024-2025</div>
             <div class="year-link" onclick="openYear('UG', '2025-26')">Batch 2025-2026</div>
        </div>
    </div>
    <div class="menu-item">
        <button class="main-cat" onclick="toggleMenu('pg-years')">Postgraduate (PG) <span>▼</span></button>
        <div id="pg-years" class="year-list">
            <div class="year-link" onclick="openYear('PG', '2024-25')">Batch 2024-2025</div>
        </div>
    </div>
</div>

<div class="main-content">
    
    <?php if ($is_admin): ?>
    <div class="admin-section">
        <h3 style="color: #E67E73; margin-top: 0;">Admin Control: Upload Syllabus (PDF Only)</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <select name="upload_year" required>
                <option value="I Year">First Year</option>
                <option value="II Year">Second Year</option>
                <option value="III Year">Third Year</option>
            </select>
            <select name="upload_sem" required>
                <option value="Odd Semester">Odd Semester</option>
                <option value="Even Semester">Even Semester</option>
            </select>
            <input type="file" name="pdf_file" accept=".pdf" required>
            <button type="submit" name="upload_syllabus" class="home-btn" style="border:none; cursor:pointer;">Upload Now</button>
        </form>
    </div>
    <?php endif; ?>

    <h1 id="header-text">Select a Batch to Begin</h1>
    
    <div id="boxWrapper" class="box-container">
        <div class="year-box" onclick="selectYear('I Year')">First Year</div>
        <div class="year-box" onclick="selectYear('II Year')">Second Year</div>
        <div id="thirdYearBox" class="year-box" onclick="selectYear('III Year')">Third Year</div>
    </div>

    <div id="semWrapper" class="sem-container">
        <div class="sem-box" onclick="selectSem('Odd Semester')">
            Odd Semester
            <span style="font-size:10px;">Term 1 / 3 / 5</span>
        </div>
        <div class="sem-box" onclick="selectSem('Even Semester')">
            Even Semester
            <span style="font-size:10px;">Term 2 / 4 / 6</span>
        </div>
    </div>

    <div id="syllabusView" class="syllabus-panel">
        <h2 id="syllabusTitle" style="color:#E67E73;"></h2>
        <button class="view-btn" onclick="triggerView()">View Syllabus</button>
        <p style="font-size: 11px; color: #666; margin-top: 10px;">Note: Syllabus files are maintained by the Admin.</p>
    </div>
</div>

<script>
    let currentPath = { cat: '', batch: '', year: '', sem: '' };

    function toggleMenu(id) {
        let menu = document.getElementById(id);
        menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
    }

    function openYear(cat, batch) {
        currentPath.cat = cat; 
        currentPath.batch = batch;
        document.getElementById('header-text').innerText = cat + " - " + batch;
        
        document.getElementById('boxWrapper').style.display = 'flex';
        document.getElementById('semWrapper').style.display = 'none';
        document.getElementById('syllabusView').style.display = 'none';
        document.getElementById('dlIcon').style.display = 'none';

        document.getElementById('thirdYearBox').style.display = (cat === 'PG') ? 'none' : 'flex';
    }

    function selectYear(year) {
        currentPath.year = year;
        document.getElementById('semWrapper').style.display = 'flex';
        document.getElementById('syllabusView').style.display = 'none';
        document.getElementById('header-text').innerText = currentPath.cat + " - " + currentPath.batch + " (" + year + ")";
    }

    function selectSem(sem) {
        currentPath.sem = sem;
        document.getElementById('syllabusView').style.display = 'block';
        document.getElementById('syllabusTitle').innerText = currentPath.year + " - " + sem + " Syllabus";
    }

    function triggerView() {
        // Filename formatting (I Year -> IYear, Odd Semester -> Odd)
        let formattedYear = currentPath.year.replace(/\s+/g, ''); 
        let formattedSem = currentPath.sem.split(' ')[0];
        
        let pdfUrl = "syllabus_" + formattedYear + "_" + formattedSem + ".pdf"; 
        
        // Show Download Icon
        let dlIcon = document.getElementById('dlIcon');
        dlIcon.style.display = 'block';
        dlIcon.href = pdfUrl; 

        // Open in New Tab
        window.open(pdfUrl, '_blank');
    }
</script>

</body>
</html>