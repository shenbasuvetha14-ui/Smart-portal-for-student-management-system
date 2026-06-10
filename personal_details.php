<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: student_login.php");
    exit();
}

$reg_no = $_SESSION['s_reg'] ?? '';
$student_name = 'Student'; 

$name_query = $conn->query("SELECT name FROM student_table WHERE reg_no='$reg_no'");
if ($name_query && $name_query->num_rows > 0) {
    $name_data = $name_query->fetch_assoc();
    $student_name = $name_data['name'];
}

$sql = "SELECT * FROM student_details_table WHERE s_reg = '$reg_no'";
$result = $conn->query($sql);
$user = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];

if (!empty($user['s_name'])) {
    $student_name = $user['s_name'];
}

$edit_status = $user['edit_status'] ?? 'Locked'; 
$edit_count = isset($user['edit_count']) ? (int)$user['edit_count'] : 0;
// A student can edit if they haven't submitted yet (count 0) OR if admin approved a re-edit
$can_edit = ($edit_count === 0 || $edit_status === 'Approved');
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Portal Hub | Student Profile</title>
    <style>
        :root { --bg: #f0f4f8; --side: #1a2236; --accent: #5c67f2; --white: #ffffff; --text: #334155; }
        body, html { margin: 0; padding: 0; min-height: 100vh; font-family: 'Inter', sans-serif; background: var(--bg); }
        .main-container { display: flex; min-height: 100vh; width: 100vw; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box; }
        .hub-card { display: flex; width: 100%; max-width: 1100px; min-height: 85vh; background: var(--white); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .sidebar { width: 280px; background: var(--side); color: white; padding: 35px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 40px; border-bottom: 1px solid #334155; padding-bottom: 20px; }
        .nav-item { display: flex; align-items: center; padding: 15px 0; color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.3s; }
        .nav-item.active { color: white; }
        .nav-item span { width: 35px; height: 35px; border-radius: 10px; border: 1px solid #334155; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px; }
        .nav-item.active span { background: var(--accent); border-color: var(--accent); box-shadow: 0 0 15px rgba(92, 103, 242, 0.4); }
        .content { flex: 1; padding: 50px; overflow-y: auto; display: flex; flex-direction: column; }
        .header-box { margin-bottom: 30px; }
        .header-box h1 { margin: 0; font-size: 26px; color: #1e293b; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-col { grid-column: span 2; }
        .input-group label { display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
        .input-group input, .input-group select { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; box-sizing: border-box; }
/* Request Box Styles */
.request-box {
    margin-top: 20px;
    padding: 25px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    text-align: left;
}
.request-box h3 {
    font-size: 16px;
    color: #1e293b;
    margin-top: 0;
}
.request-box textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    margin-top: 10px;
    resize: vertical;
    font-family: inherit;
}
.btn-request {
    background: #475569;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
}
.btn-request:hover { background: #1e293b; }
.status-tag {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    margin-top: 10px;
}
.status-pending { background: #fef3c7; color: #92400e; }
        .readonly { background: #f1f5f9; color: #64748b; cursor: not-allowed; }
        .photo-section { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 15px; border: 1px dashed #cbd5e1; }
        .profile-preview { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-upload { border: 1px solid #cbd5e1; color: #475569; background: white; padding: 10px 20px; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; }
        .footer-actions { margin-top: auto; padding-top: 30px; display: flex; justify-content: flex-end; }
        .save-btn { background: var(--accent); color: white; border: none; padding: 14px 40px; border-radius: 12px; font-weight: 700; cursor: pointer; }
        .locked-notice { background: #fff7ed; color: #9a3412; padding: 20px; border-radius: 12px; border: 1px solid #ffedd5; text-align: center; width: 100%; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="hub-card">
        <div class="sidebar">
            <h2>PORTAL HUB</h2>
            <a href="?step=1" class="nav-item <?= $step==1?'active':'' ?>"><span>👤</span> BASIC INFO</a>
            <a href="?step=2" class="nav-item <?= $step==2?'active':'' ?>"><span>📞</span> CONTACT</a>
            <a href="?step=3" class="nav-item <?= $step==3?'active':'' ?>"><span>👨‍👩‍👦</span> FAMILY</a>
            <a href="?step=4" class="nav-item <?= $step==4?'active':'' ?>"><span>🆔</span> IDENTITY DOCS</a>
            <a href="?step=5" class="nav-item <?= $step==5?'active':'' ?>"><span>🏦</span> BANK DETAILS</a>
            <a href="?step=6" class="nav-item <?= $step==6?'active':'' ?>"><span>🚌</span> TRANSPORT</a>
            <div style="margin-top: auto;"><a href="student_dashboard.php" style="color:white; text-decoration:none; font-size:12px; opacity:0.6;">← Back to Dashboard</a></div>
        </div>

        <div class="content">
            <form action="process_update.php" method="POST" enctype="multipart/form-data" id="hubForm" onsubmit="return validateForm()">
                <input type="hidden" name="step" value="<?= $step ?>">
                <input type="hidden" name="s_reg" value="<?= $reg_no ?>">

                <?php if($step == 1): ?>
                    <div class="header-box"><h1>Basic Information</h1></div>
                    <div class="photo-section">
                        <?php $img = !empty($user['s_photo']) ? "uploads/".$user['s_photo'] : "https://via.placeholder.com/90"; ?>
                        <img src="<?= $img ?>" id="preview" class="profile-preview">
                        <label class="btn-upload" for="photo-input">Upload Photo</label>
                        <input type="file" name="s_photo" id="photo-input" style="display:none;" onchange="previewImg(event)">
                    </div>
                    <div class="form-grid">
                        <div class="input-group"><label>Full name</label><input type="text" value="<?= htmlspecialchars($student_name) ?>" class="readonly" readonly></div>
                        <div class="input-group"><label>Registration No</label><input type="text" value="<?= htmlspecialchars($reg_no) ?>" class="readonly" readonly></div>
                        <div class="input-group"><label>Date of Birth</label><input type="date" name="dob" value="<?= $user['dob']??'' ?>" required></div>
                        <div class="input-group">
                            <label>Blood Group</label>
                            <select name="blood_group" required>
                                <option value="">-- Select --</option>
                                <?php 
                                $bg_list = ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"];
                                foreach($bg_list as $bg) {
                                    $sel = (isset($user['blood_group']) && strtoupper($user['blood_group']) == $bg) ? 'selected' : ''; 
                                    echo "<option value='$bg' $sel>$bg</option>"; 
                                } ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Religion</label>
                            <select name="religion" required>
                                <option value="">-- Select --</option>
                                <?php foreach(["Hindu", "Christian", "Muslim", "Sikh", "Jain", "Others"] as $rel) { 
                                    $sel = (isset($user['religion']) && $user['religion'] == $rel) ? 'selected' : ''; 
                                    echo "<option value='$rel' $sel>$rel</option>"; 
                                } ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Caste</label>
                            <select name="caste" required>
                                <option value="">-- Select --</option>
                                <?php foreach(["OC", "BC", "BCM", "MBC", "DNC", "SC", "SC(A)", "ST"] as $c) { 
                                    $sel = (isset($user['caste']) && $user['caste'] == $c) ? 'selected' : ''; 
                                    echo "<option value='$c' $sel>$c</option>"; 
                                } ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Nationality</label>
                            <input type="text" name="nationality" value="<?= $user['nationality']??'' ?>" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                    </div>

                <?php elseif($step == 2): ?>
                    <div class="header-box"><h1>Contact Information</h1></div>
                    <div class="form-grid">
                        <div class="input-group"><label>Self Mobile</label><input type="text" name="self_no" value="<?= $user['self_no']??'' ?>" maxlength="10" required></div>
                        <div class="input-group"><label>Email Address</label><input type="email" name="s_email" value="<?= $user['s_email']??'' ?>" required></div>
                        <div class="input-group full-col"><label>Residential Address</label><input type="text" name="address" value="<?= $user['address']??'' ?>" required></div>
                        <div class="input-group"><label>City</label><input type="text" name="city" value="<?= $user['city']??'' ?>" required></div>
                        <div class="input-group"><label>State</label><input type="text" name="state" value="<?= $user['state']??'' ?>" required></div>
                        <div class="input-group"><label>Pincode</label><input type="text" name="pincode" value="<?= $user['pincode']??'' ?>" maxlength="6" required></div>
                    </div>

                <?php elseif($step == 3): ?>
                    <div class="header-box"><h1>Family Details</h1></div>
                    <div class="form-grid">
                        <div class="input-group"><label>Father Name</label><input type="text" name="father_name" value="<?= $user['father_name']??'' ?>" required></div>
                        <div class="input-group"><label>Mother Name</label><input type="text" name="mother_name" value="<?= $user['mother_name']??'' ?>" required></div>
                        <div class="input-group"><label>Father Number</label><input type="text" name="father_number" value="<?= $user['father_number']??'' ?>" maxlength="10" required></div>
                        <div class="input-group"><label>Mother Number</label><input type="text" name="mother_number" value="<?= $user['mother_number']??'' ?>" maxlength="10" required></div>
                        <div class="input-group"><label>Father Occupation</label><input type="text" name="f_occ" value="<?= $user['f_occ']??'' ?>" required></div>
                        <div class="input-group"><label>Mother Occupation</label><input type="text" name="m_occ" value="<?= $user['m_occ']??'' ?>" required></div>
                        <div class="input-group"><label>Annual Income (₹)</label><input type="number" name="annual_income" value="<?= $user['annual_income']??'' ?>" required></div>
                        <div class="input-group"><label>Guardian Name</label><input type="text" name="guardian_name" value="<?= $user['guardian_name']??'' ?>" required></div>
                    </div>

                <?php elseif($step == 4): ?>
                    <div class="header-box"><h1>Identity Documents</h1></div>
                    <div class="form-grid">
                        <div class="input-group full-col"><label>Aadhar Number</label><input type="text" name="aadhar_no" value="<?= $user['aadhar_no']??'' ?>" maxlength="12" required></div>
                        <div class="input-group full-col"><label>PAN Card No</label><input type="text" name="pan_no" value="<?= $user['pan_no']??'' ?>" maxlength="10" style="text-transform:uppercase" required></div>
                    </div>

                <?php elseif($step == 5): ?>
                    <div class="header-box"><h1>Bank Account Details</h1></div>
                    <div class="form-grid">
                        <div class="input-group full-col"><label>Account Number</label><input type="text" name="acc_number" value="<?= $user['acc_number']??'' ?>" required></div>
                        <div class="input-group"><label>Bank Name</label><input type="text" name="bank_name" value="<?= $user['bank_name']??'' ?>" required></div>
                        <div class="input-group"><label>IFSC Code</label><input type="text" name="ifsc_code" value="<?= $user['ifsc_code']??'' ?>" required></div>
                    </div>

                <?php elseif($step == 6): ?>
                    <div class="header-box"><h1>Transportation Details</h1></div>
                    <div class="form-grid">
                        <div class="input-group full-col">
                            <label>Do you use College Bus?</label>
                            <select name="bus_required" id="bus_required" onchange="toggleBusFields()" required>
                                <option value="No" <?= (isset($user['bus_required']) && $user['bus_required'] == 'No') ? 'selected' : '' ?>>No, I use Private Transport</option>
                                <option value="Yes" <?= (isset($user['bus_required']) && $user['bus_required'] == 'Yes') ? 'selected' : '' ?>>Yes, I use College Bus</option>
                            </select>
                        </div>
                        <div id="bus_details_box" class="full-col" style="display: <?= (isset($user['bus_required']) && $user['bus_required'] == 'Yes') ? 'grid' : 'none' ?>; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px;">
                            <div class="input-group"><label>Bus Number</label><input type="text" name="bus_name" id="bus_name" value="<?= $user['bus_name']??'' ?>"></div>
                            <div class="input-group"><label>Boarding Point</label><input type="text" name="bus_stop" id="bus_stop" value="<?= $user['bus_stop']??'' ?>"></div>
                        </div>
                    </div>
                <?php endif; ?>

       <div class="footer-actions">
    <?php if($can_edit): ?>
        <!-- மெயின் சேவ் பட்டன் -->
        <button type="submit" class="save-btn"><?= $step == 6 ? 'FINAL SUBMIT & LOCK' : 'SAVE & PROCEED' ?></button> 
    <?php else: ?>
        <div style="width: 100%;">
            <div class="locked-notice">
                <strong>🚫 Profile Locked</strong>
                <?php if($edit_status === 'Pending'): ?>
                    <br><span style="color: #92400e;">Status: Request Pending with Faculty</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

</form> <!-- மெயின் ஃபார்ம் இங்கே முடிவடைகிறது -->

<!-- எடிட் ரிக்வெஸ்ட் செக்ஷன் (ஃபார்மிற்கு வெளியே) -->
<?php if(!$can_edit && $edit_status !== 'Pending'): ?>
    <div class="request-box" style="margin-top: 20px; padding: 20px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 15px;">
        <h3 style="font-size: 16px; margin-top: 0;">Request Profile Unlock</h3>
        <form action="request_edit_logic.php" method="POST">
            <input type="hidden" name="s_reg" value="<?= $reg_no ?>">
            <textarea name="reason" placeholder="Enna reason-kagaga edit pannanum?" 
                      style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; margin-top: 10px;" required></textarea>
            <button type="submit" style="margin-top: 10px; background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                Send Request to Faculty
            </button>
        </form>
    </div>
<?php endif; ?></div><script>
    window.onload = toggleBusFields;
    function toggleBusFields() {
        var busReq = document.getElementById('bus_required');
        var busBox = document.getElementById('bus_details_box');
        if(!busReq || !busBox) return;
        if (busReq.value === 'Yes') {
            busBox.style.display = 'grid';
            document.getElementById('bus_name').required = true;
            document.getElementById('bus_stop').required = true;
        } else {
            busBox.style.display = 'none';
            document.getElementById('bus_name').required = false;
            document.getElementById('bus_stop').required = false;
        }
    }
    function previewImg(event) {
        var reader = new FileReader();
        reader.onload = function() { document.getElementById('preview').src = reader.result; }
        reader.readAsDataURL(event.target.files[0]);
    }
    function validateForm() {
        if ("<?= $step ?>" == "6") return confirm("Are you sure? This will lock your profile.");
        return true;
    }
</script>
</body>
</html>