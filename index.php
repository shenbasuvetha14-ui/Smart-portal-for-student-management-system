<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Portal - Department of Computer Applications</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 1. Full Page Background Setup */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; 
            font-family: 'Segoe UI', sans-serif;
        }

        .hero-view {
            height: 100vh;
            width: 100%;
            background: url('your-bg-image.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* 2. Menu Fix - Dropdown logic */
        .menu {
            position: relative;
            display: inline-block; 
            vertical-align: middle;
            padding: 0 15px;
        }

        .menu-icon {
            font-size: 24px;
            cursor: pointer;
            line-height: normal;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 4px;
        }

        .menu:hover .dropdown {
            display: block;
        }

        .dropdown a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .dropdown a:hover {
            background-color: #f1f1f1;
            color: #007bff;
        }

        /* 3. Welcome Content Styling */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #333;
        }

        .main-content h2 { font-size: 3.5rem; margin-bottom: 10px; color: #1a1a1a; }
        .main-content p { font-size: 1.5rem; color: #007bff; font-weight: 500; }

        /* 4. Contact Modal Styling */
        .modal {
            display: none; 
            position: fixed;
            z-index: 2000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.85); 
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #1a1a1a;
            margin: 12% auto;
            padding: 50px 40px;
            border: 2px solid #ffc107;
            width: 50%;
            max-width: 600px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .close-btn {
            position: absolute;
            right: 20px; top: 15px;
            color: #fff;
            font-size: 35px;
            cursor: pointer;
            transition: 0.3s;
        }
        .close-btn:hover { color: #ffc107; }

        .contact-grid {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 30px;
        }

        .contact-item {
            color: #fff;
            text-decoration: none;
            transition: 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .contact-item i { font-size: 45px; color: #ffc107; }
        .contact-item span { font-size: 14px; font-weight: 500; }
        .contact-item:hover { transform: translateY(-10px); }
    </style>
</head>
<body>

<div class="hero-view">
    <header class="header">
        <img src="vcwlogo.png" alt="VCW Logo" class="logo">
        <div class="header-text">
            <h1>Vellalar College for Women</h1>
            <h4>College with Potential for Excellence (Autonomous)</h4>
            <h4>Thindal, Erode-638012</h4>
        </div>
        <img src="logo2.png" alt="VCW Logo" class="logo2">
    </header>

    <nav class="navbar" style="position: relative;">
        <div class="menu">
            <span class="menu-icon">☰</span>
            <div class="dropdown">
                <a href="faculties.php">Faculties</a>
            </div>
        </div>
        <a href="Admin_login.php">Admin</a>
        <a href="Faculty_login.php">Faculty</a>
        <a href="Student_login.php">Student</a>
        <a href="javascript:void(0)" onclick="openContact()">Contact Us</a>
        
        <span style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #64748b; font-style: italic; font-weight: 500; white-space: nowrap;">
            click below to view
        </span>
    </nav>

    <div style="position: absolute; right: 53px; top: 168px; z-index: 999; text-align: center;">
        <a href="student_view_events.php" style="text-decoration: none; display: inline-block;">
            <img src="upcomingevents.png" alt="Upcoming Events" style="width: 100px; display: block; background: transparent; transition: transform 0.2s ease; cursor: pointer;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
        </a>
    </div>

    <div class="main-content">
        <h2>Welcome to our page</h2>
        <p>Department of Computer Applications</p>
    </div>
</div>

<div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeContact()">&times;</span>
        <h2 style="color: #ffc107; margin-bottom: 5px;">Connect With Us</h2>
        <p style="color: #aaa; font-size: 14px; margin-bottom: 20px;">Department of Computer Applications | VCW Troop Tyrants</p>
        
        <div class="contact-grid">
            <a href="https://share.google/1rGu682TjTvgmZj2T" target="_blank" class="contact-item">
                <i class="fas fa-globe"></i><span>Website</span>
            </a>
            <a href="https://www.instagram.com/trooptyrants" target="_blank" class="contact-item">
                <i class="fab fa-instagram"></i><span>Instagram</span>
            </a>
            <a href="https://youtube.com/@trooptyrants" target="_blank" class="contact-item">
                <i class="fab fa-youtube"></i><span>YouTube</span>
            </a>
        </div>
    </div>
</div>

<script>
    function openContact() { document.getElementById("contactModal").style.display = "block"; }
    function closeContact() { document.getElementById("contactModal").style.display = "none"; }
    
    window.onclick = function(event) {
        let modal = document.getElementById("contactModal");
        if (event.target == modal) { modal.style.display = "none"; }
    }
</script>

</body>
</html>