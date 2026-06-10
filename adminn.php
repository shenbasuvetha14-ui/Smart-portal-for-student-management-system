<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Reports-kaga ella faculty names-yum fetch pandren
$all_faculties_res = mysqli_query($conn, "SELECT faculty_id, name FROM faculty_table ORDER BY name ASC");

// Logic section for setting incharge
if(isset($_POST['set_incharge'])) {
    $fac_id = mysqli_real_escape_string($conn, $_POST['fac_id']);
    $class = mysqli_real_escape_string($conn, $_POST['class_name']);
    $sql = "UPDATE faculty_table SET class_incharge = '$class' WHERE faculty_id = '$fac_id'";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Success: Incharge assigned for $class!'); window.location='admin_dashboard.php?view=assign_ci';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --sidebar-bg: rgba(15, 23, 42, 0.95);
            --nav-bg: rgba(15, 23, 42, 0.8);
            --card-bg: rgba(255, 255, 255, 0.1);
            --border: rgba(255, 255, 255, 0.1);
            --cyan: #0891b2;
        }

        body { 
            margin: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: url('loginimage.jpg') no-repeat center center fixed; 
            background-size: cover; 
            color: white; 
            min-height: 100vh;
        }

        /* --- Navbar --- */
        .admin-nav { 
            display: flex; 
            justify-content: flex-end; 
            padding: 15px 40px; 
            gap: 12px; 
            background: var(--nav-bg); 
            backdrop-filter: blur(12px); 
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border);
        }

        .admin-nav a, .dropbtn { 
            text-decoration: none; 
            background: rgba(255,255,255,0.05); 
            color: white; 
            padding: 10px 18px; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 13px;
            border: 1px solid var(--border);
            transition: 0.3s;
            cursor: pointer;
        }

        .admin-nav a:hover, .dropbtn:hover { 
            background: var(--primary); 
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* --- Dropdowns --- */
        .dropdown { position: relative; }
        .dropdown-content { 
            display: none; 
            position: absolute; 
            background: #1e293b; 
            min-width: 220px; 
            border-radius: 12px; 
            top: 110%; 
            right: 0; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
            border: 1px solid var(--border);
        }
        .dropdown-content a, .dropdown-content span { 
            padding: 12px 20px; 
            display: block; 
            border-bottom: 1px solid var(--border);
            color: white !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .dropdown:hover .dropdown-content { display: block; }

        /* --- Sub & Sub-Sub Dropdowns --- */
        .sub-dropdown { position: relative; }
        
        .sub-dropdown-content { 
            display: none; 
            position: absolute; 
            background: #1e293b; 
            min-width: 200px; 
            border-radius: 12px; 
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            left: -202px; 
            top: 0;
        }

        .sub-dropdown:hover > .sub-dropdown-content { display: block; }

        .menu-icon { font-size: 24px; cursor: pointer; margin-right: auto; background: var(--primary); padding: 8px 12px; border-radius: 10px; transition: 0.3s; }

        /* --- Sidebar --- */
        .sidebar { height: 100%; width: 0; position: fixed; z-index: 1001; top: 0; left: 0; background: var(--sidebar-bg); backdrop-filter: blur(20px); overflow-x: hidden; transition: 0.5s; padding-top: 60px; border-right: 1px solid var(--primary); }
        .sidebar a { padding: 16px 30px; text-decoration: none; font-size: 16px; color: #94a3b8; display: block; transition: 0.3s; }
        .sidebar a:hover { color: white; background: rgba(59, 130, 246, 0.1); border-left: 4px solid var(--primary); }
        .sidebar .closebtn { position: absolute; top: 15px; right: 25px; font-size: 30px; }

        /* Welcome & Form */
        .main-container { padding: 40px; display: flex; justify-content: center; }
        .incharge-box { background: var(--card-bg); backdrop-filter: blur(20px); padding: 35px; border-radius: 24px; border: 1px solid var(--border); width: 350px; }
        select { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 10px; background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border); color: white; }
        .btn-submit { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; cursor: pointer; font-weight: 700; border-radius: 10px; transition: 0.3s; }
        .welcome-screen { text-align: center; margin-top: 150px; }
    </style>
</head>
<body>

    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div style="padding: 0 30px 20px 30px;">
            <h2 style="color: var(--primary); font-size: 22px;">Smart Admin</h2>
            <hr style="border: 0; border-top: 1px solid var(--border);">
        </div>
        <a href="admin_dashboard.php">🏠 Dashboard Home</a>
        <a href="admin_syllabus.php">📝 Post Syllabus</a>
        <a href="faculty_details.php">👥 Faculty Records</a>
    </div>

    <div class="admin-nav">
        <span class="menu-icon" onclick="openNav()">&#9776;</span>

        <a href="admin_dashboard.php">Home</a>

        <div class="dropdown">
            <button class="dropbtn" style="color: #06b6d4;">📊 Reports &#9662;</button>
            <div class="dropdown-content">
                
                <div class="sub-dropdown">
                    <span>Students &raquo;</span>
                    <div class="sub-dropdown-content">
                        <a href="view_student_report.php?type=blood">🩸 Blood Group Report</a>
                        <a href="view_student_report.php?type=transport">🚌 Transport Report</a>
                    </div>
                </div>

                <a href="faculty_report_selection.php">👨‍🏫 Faculties</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn" style="color: #a855f7;">Students &#9662;</button>
            <div class="dropdown-content">
                <a href="admin_permission_approval.php">📅 Event Request</a>
                <a href="admin_class_selection.php">👥 Manage Records</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn" style="color: var(--primary);">Faculty &#9662;</button>
            <div class="dropdown-content">
                <a href="add_faculty.php">Add Faculty</a>
                <a href="faculty_details.php">Faculty Details</a>
                <a href="admin_dashboard.php?view=assign_ci" style="font-weight: bold;">Assign Incharge</a>
            </div>
        </div>

        <a href="index.php" style="background: #ef4444; border: none;">Logout</a>
    </div>

    <?php if(isset($_GET['view']) && $_GET['view'] == 'assign_ci'): ?>
        <div class="main-container">
            <div class="incharge-box">
                <h3>Class Incharge</h3>
                <form method="POST">
                    <label style="font-size: 12px; color: #94a3b8;">Select Faculty</label>
                    <select name="fac_id" required>
                        <option value="">-- Faculty Name --</option>
                        <?php 
                        mysqli_data_seek($all_faculties_res, 0); 
                        while($f = mysqli_fetch_assoc($all_faculties_res)) { 
                            echo "<option value='{$f['faculty_id']}'>{$f['name']}</option>"; 
                        }
                        ?>
                    </select>
                    
                    <label style="font-size: 12px; color: #94a3b8;">Select Class</label>
                    <select name="class_name" required>
                        <option value="I-BCA">I-BCA</option>
                        <option value="II-BCA">II-BCA</option>
                        <option value="III-BCA">III-BCA</option>
                        <option value="I-MCA">I-MCA</option>
                        <option value="II-MCA">II-MCA</option>
                    </select>
                    <button type="submit" name="set_incharge" class="btn-submit">Update Designation</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="welcome-screen">
            <h1 style="font-size: 48px; margin-bottom: 10px;">Welcome, <span>Admin</span></h1>
            <p style="color: #94a3b8; font-size: 18px;">Accessing VCW Secure Management Portal</p>
        </div>
    <?php endif; ?>

    <script>
        function openNav() { document.getElementById("mySidebar").style.width = "280px"; }
        function closeNav() { document.getElementById("mySidebar").style.width = "0"; } 
    </script>
</body>
</html>