<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: faculty_login.php");
    exit();
}

$f_id = $_SESSION['f_id']; 

// 1. Fetch Faculty Info
$faculty_res = mysqli_query($conn, "SELECT short_name, name, class_incharge FROM faculty_table WHERE faculty_id = '$f_id'");
$faculty_info = mysqli_fetch_assoc($faculty_res);

$my_short_name = $faculty_info['short_name'] ?? 'N/A';
$display_name = $faculty_info['name'] ?? ($_SESSION['f_name'] ?? 'Faculty');
$incharge_class = $faculty_info['class_incharge'] ?? null;

$notif_count = 0; 

// 2. Fetch Individual Timetable Logic
$my_schedule = [];
$timetable_res = mysqli_query($conn, "SELECT * FROM timetable_grid WHERE faculty_short_name = '$my_short_name'");
if($timetable_res) {
    while($t_row = mysqli_fetch_assoc($timetable_res)) {
        $day = $t_row['day_order']; 
        $hour = $t_row['hour_slot']; 
        $c_name = $t_row['class_name'];
        $s_code = $t_row['subject_code'];
        $my_schedule[$day][$hour] = "<b>$c_name</b><br><small style='color: #a5f3fc;'>$s_code</small>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9; /* Professional Sky Blue */
            --success: #10b981; /* Premium Emerald Green */
            --danger: #ef4444;  /* Soft Red */
            --nav-bg: rgba(15, 23, 42, 0.6); /* Translucent Dark Glass */
            --glass-bg: rgba(15, 23, 42, 0.75); /* Deep frosted card */
            --border: rgba(255, 255, 255, 0.15);
            --text-light: #f8fafc;
        }

        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-image: url('facultybg.jpg'); 
            background-size: cover; 
            background-attachment: fixed; 
            background-position: center; 
            color: var(--text-light); 
            overflow-x: hidden; 
            min-height: 100vh;
        }
        
        /* Soft overlay to blend background image */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: -1;
        }
        
        /* --- Navbar Glassmorphism --- */
        .faculty-nav { 
            display: flex; 
            justify-content: flex-end; 
            align-items: center; 
            padding: 15px 40px; 
            gap: 12px; 
            background: var(--nav-bg); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            position: sticky; 
            top: 0; 
            z-index: 1000; 
            border-bottom: 1px solid var(--border); 
            box-shadow: 0 4px 30px rgba(0,0,0,0.2);
        }

        .faculty-nav a, .dropbtn { 
            text-decoration: none; 
            padding: 10px 20px; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 13px; 
            color: var(--text-light); 
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            cursor: pointer; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
        }
        
        .faculty-nav a:hover, .dropbtn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        }

        /* Specific Nav Button Highlights */
        .nav-home { border-color: var(--success) !important; color: #a7f3d0 !important; }
        .nav-home:hover { background: var(--success) !important; color: white !important; }
        .nav-logout { border-color: var(--danger) !important; color: #fca5a5 !important; }
        .nav-logout:hover { background: var(--danger) !important; color: white !important; }

        /* --- Beautiful Dark Glass Dropdowns --- */
        .dropdown { position: relative; display: inline-block; }
        
        .dropdown-content { 
            display: none; 
            position: absolute; 
            background: rgba(15, 23, 42, 0.95); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-width: 220px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            z-index: 100; 
            border-radius: 14px; 
            top: 120%; 
            right: 0;
            border: 1px solid var(--border);
            overflow: visible; /* Dynamic Viewport Fix */
            padding: 6px 0;
        }

        .dropdown-content a { 
            color: #cbd5e1 !important; 
            background: transparent !important; 
            padding: 12px 20px; 
            display: block; 
            text-align: left; 
            border: none !important;
            font-size: 13.5px; 
            width: 100%; 
            box-sizing: border-box; 
            transition: 0.2s;
        }
        
        .dropdown-content a:hover { 
            background-color: rgba(255, 255, 255, 0.1) !important; 
            color: white !important; 
            padding-left: 24px;
        }
        
        .dropdown:hover > .dropdown-content { display: block; }

        /* --- Fixed Sub Dropdowns View --- */
        .sub-dropdown { position: relative; }
        
        .dropdown-content .sub-header { 
            color: #cbd5e1; 
            padding: 12px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            cursor: pointer; 
            font-size: 13.5px; 
            font-weight: 600; 
        }
        
        .dropdown-content .sub-header:hover { 
            background-color: rgba(255, 255, 255, 0.1); 
            color: white; 
        }

        .sub-dropdown-content { 
            display: none; 
            position: absolute; 
            right: 222px; /* Smooth side view shift */
            top: -6px; 
            background: rgba(15, 23, 42, 0.98); 
            backdrop-filter: blur(20px);
            min-width: 200px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            border-radius: 14px; 
            z-index: 101; 
            border: 1px solid var(--border);
            padding: 6px 0;
        }
        
        .sub-dropdown:hover > .sub-dropdown-content { display: block; }

        /* --- Sidebar Modern look --- */
        .sidebar { height: 100%; width: 0; position: fixed; z-index: 2000; top: 0; left: 0; background-color: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); overflow-x: hidden; transition: 0.5s; padding-top: 60px; border-right: 1px solid var(--border); }
        .sidebar p { text-align: center; color: var(--success); font-weight: 700; font-size: 14px; letter-spacing: 1px; margin-bottom: 30px; }
        .sidebar a, .sidebar-btn { padding: 16px 28px; text-decoration: none; font-size: 14px; color: #94a3b8; display: block; border: none; transition: 0.3s; background: none; width: 100%; text-align: left; cursor: pointer; font-weight: 500; }
        .sidebar a:hover, .sidebar-btn:hover { background: rgba(255,255,255,0.05); color: white; border-left: 4px solid var(--success); }
        
        .openbtn { font-size: 14px; font-weight: 600; cursor: pointer; background: var(--success); color: white; border: none; padding: 10px 18px; border-radius: 12px; position: absolute; left: 40px; top: 16px; z-index: 1001; transition: 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
        .openbtn:hover { background: #059669; transform: translateY(-1px); }
        .closebtn { position: absolute; top: 15px; right: 25px; font-size: 30px; cursor: pointer; color: #64748b; }
        .closebtn:hover { color: white; }

        /* --- Welcome Card --- */
        .welcome-container { 
            text-align: center; 
            margin: 120px auto 40px auto; 
            background: var(--glass-bg); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 40px; 
            border-radius: 24px; 
            width: 100%;
            max-width: 550px; 
            border: 1px solid var(--border); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); 
        }
        .welcome-container h2 { margin: 0 0 10px 0; font-size: 28px; font-weight: 700; color: white; }
        .incharge-badge { 
            background: rgba(16, 185, 129, 0.15); 
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            display: inline-block; 
            padding: 8px 24px; 
            border-radius: 50px; 
            font-weight: 600; 
            font-size: 13px; 
            margin-top: 10px; 
        }
        
        /* --- Timetable Card --- */
        .timetable-container { 
            width: 90%; 
            max-width: 1000px;
            margin: 30px auto; 
            background: var(--glass-bg); 
            backdrop-filter: blur(20px);
            padding: 30px; 
            border-radius: 24px; 
            overflow-x: auto; 
            border: 1px solid var(--border); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .grid-table { width: 100%; border-collapse: collapse; color: #e2e8f0; }
        .grid-table th, .grid-table td { border: 1px solid rgba(255,255,255,0.08); padding: 16px; text-align: center; font-size: 14px; }
        .grid-table th { background: rgba(255,255,255,0.05); font-weight: 600; color: white; }
        .grid-table tbody tr:hover { background: rgba(255,255,255,0.02); }
        .badge { background: var(--danger); color: white; border-radius: 50%; padding: 2px 7px; font-size: 11px; margin-left: 5px; }
    </style>
</head>
<body>




<div class="faculty-nav">
    <a href="manage_events.php" class="nav-home">events</a>
  
    <div class="dropdown">
        <button class="dropbtn">📊 Reports <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i></button>
        <div class="dropdown-content">
            <div class="sub-dropdown">
                <div class="sub-header">
                    <span>Students</span>
                    <i class="fa-solid fa-chevron-left" style="font-size:10px;"></i>
                </div>
                <div class="sub-dropdown-content">
                    <a href="blood_group_selection.php?type=blood">🩸 Blood Group Report</a>
                    <a href="view_student_report.php?type=transport">🚌 Transport Report</a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($incharge_class)): ?>
    <div class="dropdown">
        <button class="dropbtn" style="border-color: var(--primary); color: #7dd3fc;">🎓 Manage <?= htmlspecialchars($incharge_class) ?> <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i></button>
        <div class="dropdown-content">
            <a href="facultyretreive_studentrecords.php" style="font-weight:600; color:#38bdf8 !important;">👥 Manage Students</a>
            <a href="faculty_permission_approval.php">📬 Event Requests</a>
            <a href="manage_student_leaves.php">📄 Student Leaves</a>
            <a href="add_student.php">➕ Add Student</a>
            <a href="view_edit_request.php">📝 Edit Requests</a>
            <a href="remove_student.php" style="color: #f87171 !important;">❌ Remove Student</a>
        </div>
    </div>
    <?php endif; ?>

    <a href="index.php" class="nav-logout">Logout</a>
</div>

<div id="welcome-section" class="welcome-container">
    <h2>Welcome back, <?= htmlspecialchars($display_name) ?></h2>
    <?php if (!empty($incharge_class)): ?>
        <div class="incharge-badge"><i class="fa-solid fa-award"></i> Class Incharge: <?= htmlspecialchars($incharge_class) ?></div>
    <?php endif; ?>
</div>

<script>
function openNav() { document.getElementById("mySidebar").style.width = "280px"; }
function closeNav() { document.getElementById("mySidebar").style.width = "0"; }

function showHome() {
    document.getElementById("welcome-section").style.display = "block";
    document.getElementById("timetable-section").style.display = "none";
    closeNav();
}

function toggleTimetable() {
    var ts = document.getElementById("timetable-section");
    var ws = document.getElementById("welcome-section");
    ts.style.display = "block";
    ws.style.display = "none";
    closeNav();
}
</script>
</body>
</html>