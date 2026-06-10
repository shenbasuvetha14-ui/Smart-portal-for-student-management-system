<?php
session_start();
include 'config.php';


// if (!isset($_SESSION['student_logged_in'])) { header("Location: student_login.php"); exit(); }

$uploadDir = "uploads/events/";
$today = date('Y-m-d');
$expired_res = $conn->query("SELECT id, poster_file FROM events_table WHERE event_date < '$today'");
if ($expired_res && $expired_res->num_rows > 0) {
    while ($expired_row = $expired_res->fetch_assoc()) {
        $expired_id = $expired_row['id'];
        $expired_file = $uploadDir . $expired_row['poster_file'];
        if (!empty($expired_row['poster_file']) && file_exists($expired_file)) {
            unlink($expired_file);
        }
        $conn->query("DELETE FROM events_table WHERE id = '$expired_id'");
    }
}
// ===================================================
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Campus Notice Board</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --background: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        body { 
            background: var(--background); 
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0;
            padding: 30px 20px; 
        }

        .container { max-width: 1100px; margin: auto; }
        
        /* Modern Header for Students */
        .header { 
            text-align: center;
            margin-bottom: 40px; 
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            padding: 35px 20px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2);
            color: white;
        }
        .header h1 { margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0 0; opacity: 0.9; font-size: 15px; }

        /* Grid Layout */
        .event-list { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 25px; 
        }
        
        /* Premium Card Design */
        .event-item { 
            background: var(--card-bg); 
            border-radius: 16px; 
            border: 1px solid var(--border); 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01), 0 2px 4px -1px rgba(0,0,0,0.01);
        }
        .event-item:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08);
            border-color: #cbd5e1;
        }
        
        /* Interactive Zoom-In Icon Overlay */
        .poster-wrapper { 
            width: 100%; 
            height: 180px; 
            overflow: hidden; 
            background: #e2e8f0; 
            position: relative; 
            cursor: pointer; 
        }
        .poster-wrapper::after {
            content: '\f00e'; 
            font-family: 'Font Awesome 5 Free'; 
            font-weight: 900;
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(79, 70, 229, 0.4); color: #fff; display: flex;
            align-items: center; justify-content: center; font-size: 24px;
            opacity: 0; transition: 0.3s;
            backdrop-filter: blur(2px);
        }
        .poster-wrapper:hover::after { opacity: 1; }
        .event-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .event-item:hover img { transform: scale(1.05); }
        
        /* Details Area */
        .event-details { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .event-details h3 { margin: 0 0 10px 0; font-size: 16px; font-weight: 700; color: var(--text-main); line-height: 1.4; }
        
        .event-date { 
            font-size: 13px; color: var(--text-muted); font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
            background: #f1f5f9; padding: 6px 12px; border-radius: 8px;
            width: fit-content;
        }
        .event-date i { color: var(--primary); }

        /* LIGHTBOX MODAL SYSTEM */
        .notice-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 9999;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .notice-modal.active { display: flex; opacity: 1; }
        
        .modal-content {
            background: var(--card-bg); padding: 12px; border-radius: 20px;
            max-width: 90%; max-height: 85vh; position: relative;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .notice-modal.active .modal-content { transform: scale(1); }
        .modal-content img { max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 12px; display: block; }
        
        .modal-close {
            position: absolute; top: -16px; right: -16px;
            background: #ef4444; color: white; width: 36px; height: 36px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: 3px solid white; font-size: 16px; transition: 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .modal-close:hover { background: #dc2626; transform: scale(1.1); }
        .modal-caption { text-align: center; font-weight: 700; margin-top: 12px; color: var(--text-main); font-size: 18px; }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: white; border-radius: 20px; border: 1px solid var(--border);
        }
        .empty-state i { font-size: 55px; color: #cbd5e1; margin-bottom: 16px; }
        .empty-state p { margin: 0; font-weight: 600; font-size: 16px; color: var(--text-muted); }
    </style>
</head>
<body>

<div class="container">
    
    <div class="header">
        <h1><i class="fas fa-megaphone"></i> Campus Notice Board</h1>
        <p>Stay updated with the latest college events, seminars, and announcements</p>
    </div>

    <div class="event-list">
        <?php
       
        $res = $conn->query("SELECT * FROM events_table WHERE event_date >= '$today' ORDER BY event_date ASC");
        
        if ($res && $res->num_rows > 0):
            while($row = $res->fetch_assoc()): 
        ?>
            <div class="event-item">
                <div class="poster-wrapper" onclick="openNoticeModal('uploads/events/<?= $row['poster_file'] ?>', '<?= htmlspecialchars($row['title']) ?>')">
                    <img src="uploads/events/<?= $row['poster_file'] ?>" alt="Notice Poster" loading="lazy">
                </div>
                <div class="event-details">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <span class="event-date">
                        <i class="far fa-calendar-alt"></i> 
                        <?= date('d M Y', strtotime($row['event_date'])) ?>
                    </span>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
        ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <p>No active announcements or events posted right now.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="noticeModal" class="notice-modal" onclick="closeNoticeModal(event)">
    <div class="modal-content">
        <div class="modal-close" onclick="document.getElementById('noticeModal').classList.remove('active')">
            <i class="fas fa-times"></i>
        </div>
        <img id="modalImg" src="" alt="Zoomed Notice">
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