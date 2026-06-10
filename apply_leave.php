<?php 
session_start(); 
if (!isset($_SESSION['f_id'])) { // Security check using your session key
    header("Location: faculty_login.php");
    exit();
}
include 'config.php';
$leave_type = isset($_GET['type']) ? $_GET['type'] : 'Casual'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Leave | Smart Portal</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f0f0f; color: #e0e0e0; margin: 0; padding: 20px; }
        .container { display: flex; flex-direction: row; align-items: flex-start; gap: 30px; max-width: 1200px; margin: 0 auto; }
        #calendar { background: #fff; color: #333; padding: 15px; border-radius: 15px; flex: 2; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
        .form-card { background: #1a1a1a; padding: 30px; border-radius: 15px; flex: 1; border: 1px solid #333; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        h2 { text-align: center; color: #fff; font-weight: 600; }
        .leave-highlight { color: #4ade80; }
        label { display: block; margin-top: 15px; font-size: 0.85rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        input, textarea { width: 100%; padding: 12px; margin-top: 5px; border-radius: 8px; border: 1px solid #333; background: #262626; color: #fff; transition: 0.3s; }
        input:focus { border-color: #4ade80; outline: none; }
        input[readonly] { background: #1f2937; color: #4ade80; cursor: not-allowed; font-weight: bold; border: 1px dashed #4b5563; }
        .apply-btn { background: #10b981; color: white; border: none; padding: 15px; width: 100%; cursor: pointer; font-weight: bold; border-radius: 8px; margin-top: 20px; transition: 0.3s; font-size: 1rem; }
        .apply-btn:hover { background: #059669; transform: translateY(-2px); }
        @media (max-width: 900px) { .container { flex-direction: column; } #calendar { width: 100%; } }
    </style>
</head>
<body>

    <h2>Leave Portal: <span class="leave-highlight"><?php echo $leave_type; ?> Request</span></h2>

    <div class="container">
        <div id='calendar'></div>

        <div class="form-card">
            <form action="save_leave_request.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="leave_type" value="<?php echo $leave_type; ?>">
                
                <label>Selected Period</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" id="from_date" name="from_date" readonly required placeholder="From">
                    <input type="text" id="to_date" name="to_date" readonly required placeholder="To">
                </div>

                <label>Duration (Days)</label>
                <input type="text" id="total_days" name="total_days" readonly>

                <label>Official Reason</label>
                <textarea name="reason" rows="3" required placeholder="Reason for leave..."></textarea>

                <?php if($leave_type == 'Medical' || $leave_type == 'OD'): ?>
                    <label style="color: #fbbf24;">
                        <?php echo ($leave_type == 'OD') ? '📎 Invitation / Order Copy' : '📎 Medical Certificate'; ?>
                    </label>
                    <input type="file" name="attachment" required accept="image/*,.pdf">
                <?php endif; ?>

                <button type="submit" class="apply-btn">Submit Application</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                select: function(info) {
                    let start = info.startStr;
                    let endObj = new Date(info.endStr);
                    endObj.setDate(endObj.getDate() - 1);
                    let end = endObj.toISOString().split('T')[0];

                    document.getElementById('from_date').value = start;
                    document.getElementById('to_date').value = end;

                    let diff = Math.ceil(Math.abs(new Date(info.endStr) - new Date(info.startStr)) / (1000 * 60 * 60 * 24));
                    document.getElementById('total_days').value = diff;
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>