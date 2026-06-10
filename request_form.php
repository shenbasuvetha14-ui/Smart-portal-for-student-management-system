<?php 
session_start();
include 'config.php'; 

// Check student login
if(!isset($_SESSION['s_reg'])) {
    header("Location: student_login.php");
    exit();
}

$student_reg = $_SESSION['s_reg'];

// --- FIXED: SQL Query column name ---
$get_student = mysqli_query($conn, 
    "SELECT name, class_name FROM student_table WHERE reg_no='$student_reg'"
);

if($get_student && mysqli_num_rows($get_student) > 0){
    $data = mysqli_fetch_assoc($get_student);
    $student_name_session = $data['name']; 
    $current_class = $data['class_name']; 
} else {
    $student_name_session = "Student";
    $current_class = "N/A";
}

if(isset($_POST['submit'])) {

    $sname = mysqli_real_escape_string($conn, $student_name_session);
    $sclass = mysqli_real_escape_string($conn, $current_class); 
    $ename = mysqli_real_escape_string($conn, $_POST['event_name']);
    $etype = mysqli_real_escape_string($conn, $_POST['event_type']); 
    $college = mysqli_real_escape_string($conn, $_POST['college_name']);
    $from_date = mysqli_real_escape_string($conn, $_POST['from_date']);
    $to_date = mysqli_real_escape_string($conn, $_POST['to_date']);
    
    if(empty($to_date)) { 
        $to_date = $from_date; 
    }

    $target_dir = "uploads/events/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $filename = time() . "_" . basename($_FILES['invitation']['name']);
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES['invitation']['tmp_name'], $target_file)) {

        $sql = "INSERT INTO permission_request 
        (reg_no, student_name, class_name, event_type, college_name, event_name, from_date, to_date, invitation_file, overall_status) 
        VALUES 
        ('$student_reg', '$sname', '$sclass', '$etype', '$college', '$ename', '$from_date', '$to_date', '$filename', 'Pending Faculty Approval')";        

        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Application Submitted Successfully!'); window.location.href='request_form.php';</script>";
        } else {
            die("Database Error: " . mysqli_error($conn));
        }
    } else {
        echo "<script>alert('File upload failed!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Permission Request</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;       
            --primary-light: #e0e7ff;
            --accent: #059669;        
            --warning: #d97706;       
            --bg-glass: rgba(255, 255, 255, 0.75); /* Soft semi-transparent white paper look */
            --border: rgba(15, 23, 42, 0.08);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: url("formbackground.jpg") no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 20px;
            margin: 0;
            box-sizing: border-box;
            position: relative;
        }

        /* Subtle clean blur over your background photo */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(241, 245, 249, 0.2); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: -1;
        }

        /* --- Minimalist White Glass Card --- */
        .card-container {
            background: var(--bg-glass);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 35px 40px;
            width: 100%;
            max-width: 580px;
            box-sizing: border-box;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.1);
        }

        h2 { 
            text-align: center; 
            margin: 0 0 30px 0; 
            font-weight: 800; 
            font-size: 22px;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        h2 i { color: var(--primary); }

        .form-group { 
            position: relative; 
            margin-bottom: 20px; 
        }

        .form-group i { 
            position: absolute; 
            left: 16px; 
            bottom: 14px; 
            color: var(--text-muted); 
            font-size: 15px;
        }

        label { 
            display: block; 
            margin-bottom: 6px; 
            font-size: 13px; 
            font-weight: 700;
            color: #334155; 
        }

        input, select {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        input[type="file"] {
            padding: 9px 16px 9px 44px;
            background: rgba(255, 255, 255, 0.5);
        }

        input[readonly] {
            background: rgba(15, 23, 42, 0.04);
            color: var(--text-muted);
            border-color: rgba(15, 23, 42, 0.06);
        }

        input:focus, select:focus { 
            border-color: var(--primary); 
            outline: none; 
            background: #fff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .date-layout { display: flex; gap: 16px; }
        .date-layout .form-group { flex: 1; }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        
        .submit-btn:hover { background: #4338ca; }

        /* --- Minimalist Table Layout --- */
        .status-container {
            width: 100%;
            max-width: 950px;
            margin-top: 40px;
            background: var(--bg-glass);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        }
        
        .status-title {
            padding: 20px 24px; 
            margin: 0; 
            font-size: 15px; 
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(15, 23, 42, 0.02); padding: 14px 24px; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 16px 24px; border-top: 1px solid var(--border); font-size: 14px; }
        tr:hover td { background: rgba(255,255,255,0.3); }
        
        /* --- Clean Solid Badges --- */
        .badge { 
            padding: 4px 12px; 
            border-radius: 6px; 
            font-size: 12px; 
            font-weight: 700; 
            display: inline-block;
        }
        .pending { background: #fef3c7; color: #b45309; }
        .approved { background: #d1fae5; color: #065f46; }
        .admin-wait { background: #dbeafe; color: #1e40af; }
        
        .upload-action-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .upload-action-link:hover { text-decoration: underline; }

        /* --- Minimal Modal --- */
        .modal-view {
            display: none; position: fixed; 
            top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(15, 23, 42, 0.4); 
            backdrop-filter: blur(8px);
            z-index: 1000;
        }
        .modal-content {
            background: #fff; width: 90%; max-width: 380px; 
            margin: 20vh auto; padding: 30px; 
            border-radius: 16px; text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

    <div class="card-container">
        <h2><i class="fas fa-calendar-check"></i> Event Request</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Student Name</label>
                <input type="text" value="<?php echo $student_name_session; ?>" readonly>
                <i class="fas fa-user"></i>
            </div>

            <div class="form-group">
                <label>Class / Department</label>
                <input type="text" value="<?php echo $current_class; ?>" readonly>
                <i class="fas fa-graduation-cap"></i>
            </div>

            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="event_name" placeholder="e.g. Symposium" required>
                <i class="fas fa-award"></i>
            </div>

            <div class="form-group">
                <label>Event Type</label>
                <select name="event_type" required>
                    <option value="" disabled selected>Select Event Type</option>
                    <option value="Intercollegiate Meet">Intercollegiate Meet</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Conference">Conference</option>
                    <option value="Internship">Internship</option>
                </select>
                <i class="fas fa-list"></i>
            </div>

            <div class="form-group">
                <label>College Name</label>
                <input type="text" name="college_name" placeholder="Enter College Name" required>
                <i class="fas fa-university"></i>
            </div>

            <div class="date-layout">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" required>
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to_date"> 
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Upload Invitation File</label>
                <input type="file" name="invitation" required>
                <i class="fas fa-file-pdf"></i>
            </div>

            <button type="submit" name="submit" class="submit-btn">Submit Application</button>
        </form>
    </div>

    <div class="status-container">
        <div class="status-title"><i class="fas fa-history"></i> Tracking Status</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Student</th> 
                        <th>Event</th>
                        <th>Dates</th> 
                        <th>Status</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, 
                        "SELECT * FROM permission_request WHERE reg_no = '$student_reg' ORDER BY id DESC"
                    );
                    
                    while($row = mysqli_fetch_assoc($result)) {
                        $status_class = 'pending';
                        if($row['overall_status'] == 'Approved') $status_class = 'approved';
                        if($row['overall_status'] == 'Pending Admin Approval') $status_class = 'admin-wait';
                        if($row['overall_status'] == 'Certificate Uploaded') $status_class = 'approved'; 
                        
                        $date_display = date('d M Y', strtotime($row['from_date']));
                        if($row['from_date'] != $row['to_date']) {
                            $date_display .= " - " . date('d M Y', strtotime($row['to_date']));
                        }

                        echo "<tr>
                            <td>
                                <span style='font-weight:700; color:#0f172a;'>" . htmlspecialchars($row['student_name']) . "</span><br>
                                <small style='color:var(--text-muted);'>" . htmlspecialchars($row['class_name']) . "</small>
                            </td>
                            <td>
                                <div style='font-weight: 600; color: #0f172a;'>" . htmlspecialchars($row['event_name']) . "</div>
                                <div style='color: var(--text-muted); font-size: 13px;'>" . htmlspecialchars($row['event_type']) . "</div>
                            </td>
                            <td style='color:var(--text-main); font-weight:500;'>" . htmlspecialchars($date_display) . "</td>
                            <td><span class='badge $status_class'>" . htmlspecialchars($row['overall_status']) . "</span></td>
                            <td>";

                            if ($row['overall_status'] == 'Approved' || $row['overall_status'] == 'Certificate Uploaded') {
                                if (empty($row['certificate_file'])) {
                                    echo "<a href='#' onclick='openModal(" . $row['id'] . ")' class='upload-action-link'><i class='fa-solid fa-upload'></i> Upload</a>";
                                } else {
                                    echo "<span style='color:var(--accent); font-weight:700;'><i class='fa-solid fa-circle-check'></i> Sent</span>";
                                }
                            } else {
                                echo "-";
                            }

                        echo "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Box Window -->
    <div id="certModal" class="modal-view">
        <div class="modal-content">
            <h3 style="margin: 0 0 10px 0; font-weight: 800;">Upload Certificate</h3>
            <p style="color: var(--text-muted); font-size:13px; margin-bottom:20px;">Choose file to update your records.</p>
            <form method="POST" action="upload_handler.php" enctype="multipart/form-data">
                <input type="hidden" name="request_id" id="modal_id">
                <input type="file" name="certificate" required>
                <br><br>
                <button type="submit" name="upload" class="submit-btn" style="margin-top:0;">Upload Now</button>
                <button type="button" onclick="document.getElementById('certModal').style.display='none'" style="background:none; border:none; color:var(--text-muted); font-weight:600; cursor:pointer; margin-top:15px;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(id) {
        document.getElementById('modal_id').value = id;
        document.getElementById('certModal').style.display = 'block';
    }
    </script>
</body>
</html>