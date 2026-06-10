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
            --primary: #4f46e5; /* Premium Indigo Accent */
            --primary-hover: #4338ca;
            --sidebar-bg: rgba(15, 23, 42, 0.85);
            --nav-bg: rgba(255, 255, 255, 0.15); /* Glass effect */
            --card-bg: rgba(255, 255, 255, 0.2);  /* High contrast glass */
            --border: rgba(255, 255, 255, 0.25);
            --text-dark: #1e293b;
            --danger: #ef4444;
        }

        body { 
            margin: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: url('adminbg.jpg') no-repeat center center fixed; 
            background-size: cover; 
            color: var(--text-dark);
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.15);
            z-index: -1;
        }

        /* --- Navbar --- */
        .admin-nav { 
            display: flex; 
            justify-content: flex-end; 
            padding: 15px 40px; 
            gap: 12px; 
            background: var(--nav-bg); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .admin-nav a, .dropbtn { 
            text-decoration: none; 
            background: rgba(255, 255, 255, 0.4); 
            color: var(--text-dark); 
            padding: 10px 20px; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 13px;
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .admin-nav a:hover, .dropbtn:hover { 
            background: var(--primary); 
            color: white !important;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* --- Dropdowns --- */
        .dropdown { position: relative; }
        
        .dropdown-content { 
            display: none; 
            position: absolute; 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-width: 240px; 
            border-radius: 14px; 
            top: 120%; 
            right: 0; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border: 1px solid var(--border);
            overflow: visible; /* Fixed to allow sub-menus to display */
            padding: 6px 0;
        }
        
        .dropdown-content a { 
            padding: 12px 20px; 
            display: block; 
            color: var(--text-dark) !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            background: transparent;
            transition: 0.2s;
            border: none !important;
        }
        
        .dropdown-content a:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary) !important;
            padding-left: 24px;
        }
        
        .dropdown:hover .dropdown-content { display: block; }

        /* --- Nested Sub Dropdowns FIXED --- */
        .sub-dropdown { position: relative; }
        
        .sub-dropdown-header {
            padding: 12px 20px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-dark);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .sub-dropdown:hover .sub-dropdown-header {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .sub-dropdown-content { 
            display: none; 
            position: absolute; 
            background: rgba(255, 255, 255, 0.98); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-width: 220px; 
            border-radius: 14px; 
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            left: -225px; /* Clean positioning to the left side */
            top: 0;
            overflow: hidden;
            padding: 6px 0;
            z-index: 1000;
        }

        .sub-dropdown:hover .sub-dropdown-content { display: block; }

        .menu-icon { 
            font-size: 20px; 
            cursor: pointer; 
            margin-right: auto; 
            background: rgba(255, 255, 255, 0.4); 
            color: var(--text-dark);
            padding: 10px 14px; 
            border-radius: 12px; 
            border: 1px solid var(--border);
            transition: 0.3s; 
        }
        
        .menu-icon:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* --- Sidebar --- */
        .sidebar { height: 100%; width: 0; position: fixed; z-index: 1001; top: 0; left: 0; background: var(--sidebar-bg); backdrop-filter: blur(20px); overflow-x: hidden; transition: 0.5s; padding-top: 60px; border-right: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { padding: 16px 30px; text-decoration: none; font-size: 15px; color: #cbd5e1; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a:hover { color: white; background: rgba(255, 255, 255, 0.1); border-left: 4px solid var(--primary); }
        .sidebar .closebtn { position: absolute; top: 15px; right: 25px; font-size: 30px; color: #cbd5e1; }

        /* Welcome & Form Card */
        .main-container { padding: 60px 40px; display: flex; justify-content: center; }
        .incharge-box { 
            background: var(--card-bg); 
            backdrop-filter: blur(25px); 
            -webkit-backdrop-filter: blur(25px);
            padding: 40px; 
            border-radius: 24px; 
            border: 1px solid var(--border); 
            width: 380px; 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }
        .incharge-box h3 { margin-top: 0; font-size: 22px; color: var(--text-dark); margin-bottom: 25px; text-align: center; }
        
        select { 
            width: 100%; 
            padding: 14px; 
            margin-top: 6px;
            margin-bottom: 20px; 
            border-radius: 12px; 
            background: rgba(255, 255, 255, 0.6); 
            border: 1px solid var(--border); 
            color: var(--text-dark); 
            font-weight: 500;
            font-family: inherit;
            outline: none;
        }
        select:focus { border-color: var(--primary); }
        
        .btn-submit { 
            width: 100%; 
            padding: 14px; 
            background: var(--primary); 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: 700; 
            border-radius: 12px; 
            transition: 0.3s; 
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); }
        
        .welcome-screen { text-align: center; margin-top: 12%; text-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .welcome-screen h1 { font-size: 52px; margin-bottom: 12px; color: var(--text-dark); font-weight: 700; }
        .welcome-screen h1 span { color: var(--primary); }
        .welcome-screen p { color: #475569; font-size: 18px; font-weight: 500; }
    </style>
</head>
<body>


    <div class="admin-nav">
       
        <a href="manage_events.php">events</a>

                <div class="dropdown">
            <button class="dropbtn">Students &#9662;</button>
            <div class="dropdown-content">
                <a href="admin_permission_approval.php">📅 Event Request</a>
                <a href="admin_class_selection.php">👥 Manage Records</a>
                <a href="blood_group_selection.php?type=blood">🩸 Blood Group Report</a>
                <a href="view_student_report.php?type=transport">🚌 Transport Report</a>
  <a href="admin_syllabus.php">📝 Post Syllabus</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Faculty &#9662;</button>
            <div class="dropdown-content">
                <a href="add_faculty.php">Add Faculty</a>
                <a href="faculties.php">Faculty Details</a>
                <a href="admin_dashboard.php?view=assign_ci" style="font-weight: bold;">Assign Incharge</a>
            </div>
        </div>

        <a href="index.php" style="background: var(--danger); color: white; border: none;">Logout</a>
    </div>

    <?php if(isset($_GET['view']) && $_GET['view'] == 'assign_ci'): ?>
        <div class="main-container">
            <div class="incharge-box">
                <h3>Assign Class Incharge</h3>
                <form method="POST">
                    <label style="font-size: 13px; font-weight: 600; color: #475569;">Select Faculty</label>
                    <select name="fac_id" required>
                        <option value="">-- Faculty Name --</option>
                        <?php 
                        mysqli_data_seek($all_faculties_res, 0); 
                        while($f = mysqli_fetch_assoc($all_faculties_res)) { 
                            echo "<option value='{$f['faculty_id']}'>{$f['name']}</option>"; 
                        }
                        ?>
                    </select>
                    
                    <label style="font-size: 13px; font-weight: 600; color: #475569;">Select Class</label>
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
            <h1>Welcome, <span>Admin</span></h1>
            <p>Accessing VCW Secure Management Portal</p>
        </div>
    <?php endif; ?>

    <script>
        function openNav() { document.getElementById("mySidebar").style.width = "280px"; }
        function closeNav() { document.getElementById("mySidebar").style.width = "0"; }
    </script>
</body>
</html>