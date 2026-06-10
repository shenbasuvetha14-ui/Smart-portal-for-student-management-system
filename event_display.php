<?php 
include 'config.php'; // Database connection
$today = date('Y-m-d');

// 1. Auto-delete old events
$conn->query("DELETE FROM events_table WHERE event_date < '$today'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Events | Vellalar College</title>
    <style>
        /* Glassmorphism Design */
        body { 
            margin: 0; padding: 40px; 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
            min-height: 100vh; color: #fff; 
        }
        
        h1 { text-align: center; margin-bottom: 50px; font-size: 2.5rem; color: #fff; }

        .home-btn { 
            display: inline-block; padding: 10px 20px; background: rgba(255,255,255,0.1); 
            color: #fff; text-decoration: none; border-radius: 8px; backdrop-filter: blur(5px);
        }

        .events-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; }

        .event-card { 
            width: 300px; background: rgba(255, 255, 255, 0.08); 
            backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 15px; transition: 0.4s;
            text-align: center; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .event-card:hover { transform: translateY(-15px); border: 1px solid rgba(255, 255, 255, 0.3); }

        .event-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 15px; }

        .event-card h3 { margin: 15px 0 10px 0; font-size: 1.2rem; }
        
        .date { 
            background: rgba(6, 182, 212, 0.2); padding: 5px 10px; 
            border-radius: 20px; display: inline-block; color: #06b6d4; font-size: 0.9rem;
        }

        /* Lightbox */
        .lightbox { 
            display: none; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; 
            height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); 
            justify-content: center; align-items: center; 
        }
        .lightbox img { max-width: 85%; max-height: 85%; border-radius: 20px; }
        .lightbox:target { display: flex; }
        .close-btn { position: absolute; top: 30px; right: 30px; font-size: 50px; color: #fff; text-decoration: none; }
.download-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    border-radius: 20px;
    font-size: 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: 0.3s;
}

.download-btn:hover {
    background: #06b6d4;
    border-color: #06b6d4;
    color: #fff;
}
    </style>
</head>
<body>

    <a href="index.php" class="home-btn">← Home</a>
    <h1>Upcoming Events</h1>

    <div class="events-container">
        <?php
        $res = $conn->query("SELECT * FROM events_table ORDER BY event_date ASC");
        
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $img_path = "uploads/events/" . $row['poster_file'];
        ?>
            <div class="event-card">
    <a href="#img<?= $row['id'] ?>"><img src="<?= $img_path ?>" alt="Event"></a>
    <h3><?= htmlspecialchars($row['title']) ?></h3>
    <p class="date">📅 <?= date('d M Y', strtotime($row['event_date'])) ?></p>
    
    <a href="<?= $img_path ?>" download class="download-btn">Download Poster</a>
</div>            <div id="img<?= $row['id'] ?>" class="lightbox">
                <a href="#" class="close-btn">&times;</a>
                <img src="<?= $img_path ?>">
            </div>
        <?php 
            }
        } else {
            echo "<h2 style='text-align:center;'>No upcoming events found!</h2>";
        }
        ?>
    </div>

</body>
</html>