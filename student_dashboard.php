<?php
session_start();
include 'db_config.php'; 

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: student_login.php");
    exit();
}

$display_name = "Student";
$display_reg = "N/A";
$my_class = "";

$s_reg = $_SESSION['s_reg'] ?? ''; 

if (!empty($s_reg)) {
    // Fetch Student Info
    $user_query = "SELECT name, reg_no, class_name FROM student_table WHERE reg_no = '$s_reg'";
    $user_res = $conn->query($user_query);

    if ($user_res && $user_res->num_rows > 0) {
        $user_data = $user_res->fetch_assoc();
        $display_name = $user_data['name'] ?? 'Student';
        $display_reg = $user_data['reg_no'] ?? 'N/A';
        $my_class = $user_data['class_name'] ?? '';
        $_SESSION['s_class'] = $my_class; 
    }
}

function safe_html($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Smart Portal</title>
    <!-- Premium Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;       /* Modern Deep Indigo */
            --primary-light: #e0e7ff;
            --danger: #ef4444;        /* Clean Red */
            --bg-sidebar: #f5efe6;    /* NO WHITE - Exact Premium Warm Cream from image_e411f7.jpg */
            --border: rgba(15, 23, 42, 0.05);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            background: url("formbackground.jpg") no-repeat center center fixed; 
            background-size: cover; 
            display: flex; 
            min-height: 100vh;
            color: var(--text-main);
            overflow: hidden;
            position: relative;
        }

        /* Cinematic soft backdrop blur layer over background image */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.08); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 0;
        }

        /* --- Restored Solid Premium Cream Sidebar (image_e411f7.jpg Exact Match) --- */
        .sidebar {
            width: 290px; 
            background: var(--bg-sidebar);
            height: 100vh;
            padding: 45px 24px 35px 24px; 
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            border-right: 1px solid var(--border);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.05);
        }

        .portal-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 10px;
        }

        .portal-title { 
            font-size: 21px; 
            font-weight: 800; 
            letter-spacing: 1.2px;
            color: #1e293b;
            margin: 0;
        }
        
        .portal-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 2px;
            margin-top: 6px;
            display: block;
            text-transform: uppercase;
        }

        /* --- Sidebar Navigation Links --- */
        .sidebar a {
            display: flex;
            align-items: center;
            color: var(--text-muted); 
            text-decoration: none;
            padding: 15px 20px; 
            border-radius: 14px; 
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .sidebar a i {
            margin-right: 14px;
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .sidebar a:hover { 
            color: var(--text-main);
            transform: translateX(4px);
        }

        .sidebar a:hover i {
            color: var(--text-main);
        }

        /* Active State Pill Styling from Screenshot */
        .sidebar a.active { 
            background: #ffffff;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            font-weight: 700;
        }
        
        .sidebar a.active i {
            color: var(--primary);
        }

        /* Logout Pill matching the Soft Red tone in Image */
        .logout-link {
            margin-top: auto;
            background: rgba(239, 68, 68, 0.08) !important;
            color: #dc2626 !important;
            border-radius: 14px;
        }
        
        .logout-link i {
            color: #dc2626 !important;
        }
        
        .logout-link:hover {
            background: var(--danger) !important;
            color: white !important;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.15);
        }

        .logout-link:hover i {
            color: white !important;
        }

        /* --- Main Content Layout --- */
        .main-content { 
            flex: 1; 
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: flex-end; 
            align-items: center;  
            padding-right: 12%;
        }

        .welcome-area { 
            text-align: right; 
            animation: fadeInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .welcome-area h1 { 
            font-size: 56px; 
            margin: 0; 
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            color: #1e293b;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        
        .welcome-area h1 span { 
            color: var(--primary);
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- Info Badges Layout --- */
        .info-pill {
            display: inline-flex;
            gap: 12px;
            margin-top: 28px;
        }

        .reg-no, .class-tag { 
            padding: 10px 22px; 
            border-radius: 30px; 
            font-weight: 700; 
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #1e293b;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .class-tag i {
            color: #10b981; 
        }
        
        .reg-no i {
            color: #4f46e5; 
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(25px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="portal-header">
            <h2 class="portal-title">STUDENT PORTAL</h2>
            <span class="portal-subtitle">Smart Systems</span>
        </div>

        <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Home</a>
        
        <a href="student_syllabus.php"><i class="fa-solid fa-book-open"></i> Syllabus</a>

        <a href="personal_details.php"><i class="fa-solid fa-user-gear"></i> Personal Details</a>

        <a href="student_leave.php">
            <i class="fa-solid fa-envelope-open-text"></i> Apply for Leave
        </a>
        
        <a href="request_form.php">
            <i class="fa-solid fa-file-signature"></i> Permission Request
        </a>
        
        <a href="index.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="welcome-area">
            <h1>Welcome back,<br><span><?= safe_html($display_name) ?>!</span></h1>
            <div class="info-pill">
                <?php if(!empty($my_class)): ?>
                    <span class="class-tag"><i class="fa-solid fa-graduation-cap"></i> <?= safe_html($my_class) ?></span>
                <?php endif; ?>
                <span class="reg-no"><i class="fa-solid fa-id-card"></i> REG: <?= safe_html($display_reg) ?></span>
            </div>
        </div>
    </div>

</body>
</html>