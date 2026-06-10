<?php
session_start();
include 'config.php';

// Faculty லாகின் செக் 
if (!isset($_SESSION['faculty_logged_in'])) {
    header("Location: faculty_login.php");
    exit();
}

$uploadDir = "uploads/events/";

// ==========================================
// 🔥 AUTOMATIC EXPIRED EVENTS CLEANUP SYSTEM
// ==========================================
// ஃபேக்கல்ட்டி இந்த பேஜை ஓபன் பண்ணினாலே தானாகவே டேட் முடிந்த ஈவென்ட்களை சர்வரில் தேடி அழிக்கும்
$today = date('Y-m-d');
$expired_res = $conn->query("SELECT id, poster_file FROM events_table WHERE event_date < '$today'");

if ($expired_res && $expired_res->num_rows > 0) {
    while ($expired_row = $expired_res->fetch_assoc()) {
        $expired_id = $expired_row['id'];
        $expired_file = $uploadDir . $expired_row['poster_file'];
        
        // 1. சர்வர் ஃபோல்டரிலிருந்து இமேஜை அழிக்கிறது
        if (!empty($expired_row['poster_file']) && file_exists($expired_file)) {
            unlink($expired_file);
        }
        
        // 2. டேட்டாபேஸிலிருந்து அந்த ரெக்கார்டை நீக்குகிறது
        $conn->query("DELETE FROM events_table WHERE id = '$expired_id'");
    }
}
// ==========================================


// --- MANUAL DELETE LOGIC ---
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $res = $conn->query("SELECT poster_file FROM events_table WHERE id = '$id'");
    if($row = $res->fetch_assoc()) {
        $filePath = $uploadDir . $row['poster_file'];
        if(file_exists($filePath)) { 
            unlink($filePath); 
        }
    }
    $conn->query("DELETE FROM events_table WHERE id = '$id'");
    header("Location: faculty_manage_events.php");
    exit();
}

// --- NEW UPLOAD LOGIC ---
if(isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);

    if(!empty($_FILES['poster']['name'])) {
        $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'webp');
        
        if(in_array($ext, $allowed)) {
            $new_name = uniqid('ev_') . '.' . $ext;
            if (!is_dir($uploadDir)) { 
                mkdir($uploadDir, 0777, true); 
            }
            $uploadPath = $uploadDir . $new_name;

            if(move_uploaded_file($_FILES['poster']['tmp_name'], $uploadPath)) {
                $conn->query("INSERT INTO events_table (title, event_date, poster_file) VALUES ('$title', '$event_date', '$new_name')");
                echo "<script>alert('✅ Event Published Successfully!'); window.location.href='faculty_manage_events.php';</script>";
            } else {
                echo "<script>alert('❌ Upload failed to server folder!');</script>";
            }
        } else {
            echo "<script>alert('⚠️ Invalid format! Only JPG, JPEG, PNG & WEBP allowed.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Panel | Manage Campus Events</title>
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --danger-hover: #dc2626;
        }

        body { 
            background: var(--background); 
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0;
            padding: 30px 20px; 
        }

        .container { max-width: 1200px; margin: auto; }
        
        .header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 35px; background: var(--card-bg); padding: 24px 32px; 
            border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            border: 1px solid var(--border);
        }
        
        .header h2 { margin: 0; font-size: 24px; font-weight: 700; display: inline-flex; align-items: center; gap: 12px; }
        .header h2 i { color: var(--primary); }
        
        .btn-back { 
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: var(--text-muted); 
            font-weight: 600; font-size: 14px; padding: 10px 16px;
            border-radius: 8px; border: 1px solid var(--border); transition: 0.2s;
        }
        .btn-back:hover { background: #f1f5f9; color: var(--text-main); }

        .main-grid { display: grid; grid-template-columns: 380px 1fr; gap: 35px; align-items: start; }
        
        .form-card { 
            background: var(--card-bg); padding: 30px; border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid var(--border);
        }
        .form-card h3 { margin-top: 0; margin-bottom: 24px; font-size: 18px; font-weight: 700; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        
        input[type="text"], input[type="date"], input[type="file"] { 
            width: 100%; padding: 12px 14px; border-radius: 8px; 
            border: 1px solid var(--border); box-sizing: border-box; 
            background: #fff; font-size: 14px; font-family: inherit; transition: 0.2s;
        }
        input[type="text"]:focus, input[type="date"]:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        input[type="file"] { padding: 8px; background: #f1f5f9; cursor: pointer; }

        .btn-publish { 
            background: var(--primary); color: white; font-weight: 600; 
            border: none; padding: 14px; width: 100%; border-radius: 8px; 
            cursor: pointer; transition: 0.2s; font-size: 15px; margin-top: 10px;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-publish:hover { background: var(--primary-hover); }

        .event-feed-section {
            background: var(--card-bg); padding: 30px; border-radius: 16px;
            border: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }
        .event-feed-section h3 { margin-top: 0; margin-bottom: 24px; font-size: 18px; font-weight: 700; }

        .event-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px; }
        
        .event-item { 
            background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border); 
            overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; 
        }
        .event-item:hover { transform: translateY(-5px); border-color: #cbd5e1; }
        
        .poster-wrapper { width: 100%; height: 150px; overflow: hidden; background: #e2e8f0; position: relative; cursor: pointer; }
        .poster-wrapper::after {
            content: '\f00e'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); color: #fff; display: flex;
            align-items: center; justify-content: center; font-size: 20px; opacity: 0; transition: 0.3s;
        }
        .poster-wrapper:hover::after { opacity: 1; }
        .event-item img { width: 100%; height: 100%; object-fit: cover; }
        
        .event-details { padding: 16px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .event-details h4 { margin: 0 0 8px 0; font-size: 15px; font-weight: 700; line-height: 1.4; }
        
        .event-date { font-size: 12px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px; margin-bottom: 15px; }
        
        .del-btn { 
            color: var(--danger); text-decoration: none; font-size: 13px; font-weight: 600;
            padding: 8px; border-radius: 6px; background: #fef2f2; border: 1px solid #fee2e2;
            text-align: center; display: block; transition: 0.2s;
        }
        .del-btn:hover { background: var(--danger); color: white; }

        /* VIEW MODAL STYLE */
        .notice-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 9999;
        }
        .notice-modal.active { display: flex; }
        .modal-content {
            background: var(--card-bg); padding: 12px; border-radius: 16px;
            max-width: 90%; max-height: 85vh; position: relative;
        }
        .modal-content img { max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 10px; display: block; }
        .modal-close {
            position: absolute; top: -16px; right: -16px; background: var(--danger); color: white; 
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; cursor: pointer; border: 3px solid white;
        }
        .modal-caption { text-align: center; font-weight: 700; margin-top: 10px; font-size: 16px; }
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-bullhorn"></i> Event Management Hub</h2>
        <a href="faculty_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="main-grid">
        <div class="form-card">
            <h3>Create Live Notice</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" name="title" placeholder="e.g. National Cyber Symposium" required>
                </div>
                <div class="form-group">
                    <label>Scheduled Date</label>
                    <input type="date" name="event_date" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Upload Media Poster</label>
                    <input type="file" name="poster" accept="image/*" required>
                </div>
                <button type="submit" name="submit" class="btn-publish">
                    <i class="fas fa-paper-plane"></i> Publish to Feed
                </button>
            </form>
        </div>

        <div class="event-feed-section">
            <h3>Active Campus Notice Board </h3>
            <div class="event-list">
                <?php
                $res = $conn->query("SELECT * FROM events_table ORDER BY event_date ASC");
                if ($res && $res->num_rows > 0):
                    while($row = $res->fetch_assoc()): 
                ?>
                    <div class="event-item">
                        <div class="poster-wrapper" onclick="openNoticeModal('uploads/events/<?= $row['poster_file'] ?>', '<?= htmlspecialchars($row['title']) ?>')">
                            <img src="uploads/events/<?= $row['poster_file'] ?>" alt="Event Poster">
                        </div>
                        <div class="event-details">
                            <div>
                                <h4><?= htmlspecialchars($row['title']) ?></h4>
                                <span class="event-date">
                                    <i class="far fa-calendar-alt"></i> 
                                    <?= date('d M Y', strtotime($row['event_date'])) ?>
                                </span>
                            </div>
                            <a href="?delete=<?= $row['id'] ?>" class="del-btn" onclick="return confirm('⚠️ Remove this event from student view?')">
                                <i class="fas fa-trash-alt"></i> Remove Notice
                            </a>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else:
                ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-minus"></i>
                        <p>No active campus announcements published yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- POPUP MODAL -->
<div id="noticeModal" class="notice-modal" onclick="closeNoticeModal(event)">
    <div class="modal-content">
        <div class="modal-close" onclick="document.getElementById('noticeModal').classList.remove('active')">
            <i class="fas fa-times"></i>
        </div>
        <img id="modalImg" src="">
        <div id="modalCaption" class="modal-caption"></div>
    </div>
</div>

<script>
function openNoticeModal(imgSrc, title) {
    document.getElementById('modalImg').src = imgSrc;
    document.getElementById('modalCaption').innerText = title;
    document.getElementById('noticeModal').classList.add('active');
}
function closeNoticeModal(e) {
    if(e.target.id === 'noticeModal') {
        document.getElementById('noticeModal').classList.remove('active');
    }
}
</script>
</body>
</html>