<?php
session_start();
include 'config.php';

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: student_login.php");
    exit();
}

function clean($data, $conn) {
    return mysqli_real_escape_string($conn, trim($data));
}

function validate($data, $pattern) {
    return preg_match($pattern, $data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['s_reg'])) {
        die("Session expired. Please login again.");
    }

    $reg_no = $_SESSION['s_reg'];
    $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;

    // --- 1. FETCH CURRENT PROGRESS FOR VALIDATION ---
    $check_user = $conn->query("SELECT * FROM student_details_table WHERE s_reg='$reg_no'");
    $user_data = $check_user->fetch_assoc();
    
    $edit_count = (int)($user_data['edit_count'] ?? 0);
    $edit_status = $user_data['edit_status'] ?? '';
    
    // Safety check: Profile status
    if ($edit_count > 0 && $edit_status !== 'Approved') {
        echo "<script>alert('Profile is locked.'); window.location.href='student_dashboard.php';</script>";
        exit();
    }

    // --- 2. SEQUENTIAL VALIDATION (Prevents skipping steps) ---
    if ($step > 1) {
        $is_valid = true;
        $error_msg = "";

        // Check if previous steps have data in mandatory fields
        if ($step >= 2 && empty($user_data['dob'])) { 
            $is_valid = false; $error_msg = "Please complete Step 1 first."; 
        }
        elseif ($step >= 3 && empty($user_data['self_no'])) { 
            $is_valid = false; $error_msg = "Please complete Step 2 first."; 
        }
        elseif ($step >= 4 && empty($user_data['father_name'])) { 
            $is_valid = false; $error_msg = "Please complete Step 3 first."; 
        }
        elseif ($step >= 5 && empty($user_data['aadhar_no'])) { 
            $is_valid = false; $error_msg = "Please complete Step 4 first."; 
        }
        elseif ($step >= 6 && empty($user_data['acc_number'])) { 
            $is_valid = false; $error_msg = "Please complete Step 5 first."; 
        }

        if (!$is_valid) {
            echo "<script>alert('$error_msg'); window.location.href='personal_details.php?step=" . ($step - 1) . "';</script>";
            exit();
        }
    }

    $sql = "";

    // ---------------- STEP 1: BASIC INFO & PHOTO ----------------
    if ($step == 1) {
        $dob = clean($_POST['dob'], $conn);
        $blood = strtoupper(clean($_POST['blood_group'], $conn));
        $religion = clean($_POST['religion'], $conn);
        $caste = clean($_POST['caste'], $conn);
        $nationality = clean($_POST['nationality'], $conn);

        if (empty($dob) || empty($blood) || empty($religion)) {
            echo "<script>alert('Required fields missing!'); window.history.back();</script>";
            exit();
        }

        $photo_query_part = ""; 
        if (isset($_FILES['s_photo']) && $_FILES['s_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

            $file_ext = strtolower(pathinfo($_FILES["s_photo"]["name"], PATHINFO_EXTENSION));
            $allowed = array("jpg", "jpeg", "png");

            if (in_array($file_ext, $allowed)) {
                $new_filename = "profile_" . $reg_no . "_" . time() . "." . $file_ext;
                if (move_uploaded_file($_FILES["s_photo"]["tmp_name"], $target_dir . $new_filename)) {
                    $photo_query_part = ", s_photo='$new_filename'";
                }
            }
        }

        $sql = "UPDATE student_details_table SET dob='$dob', blood_group='$blood', religion='$religion', caste='$caste', nationality='$nationality' $photo_query_part WHERE s_reg='$reg_no'";
    }

    // ---------------- STEP 2: CONTACT ----------------
    elseif ($step == 2) {
        $self_no = clean($_POST['self_no'], $conn);
        $email = clean($_POST['s_email'], $conn);
        $pincode = clean($_POST['pincode'], $conn);

        if (!validate($self_no, "/^[6-9]\d{9}$/") || !filter_var($email, FILTER_VALIDATE_EMAIL) || !validate($pincode, "/^\d{6}$/")) {
            echo "<script>alert('Invalid input format in Contact details.'); window.history.back();</script>";
            exit();
        }

        $sql = "UPDATE student_details_table SET self_no='$self_no', s_email='$email', address='".clean($_POST['address'], $conn)."', city='".clean($_POST['city'], $conn)."', state='".clean($_POST['state'], $conn)."', pincode='$pincode' WHERE s_reg='$reg_no'";
    }

    // ---------------- STEP 3: FAMILY ----------------
    elseif ($step == 3) {
        $f_no = clean($_POST['father_number'], $conn);
        $m_no = clean($_POST['mother_number'], $conn);
        $income = (int)$_POST['annual_income'];

        if (!validate($f_no, "/^[6-9]\d{9}$/") || !validate($m_no, "/^[6-9]\d{9}$/") || $income < 0) {
            echo "<script>alert('Invalid parent contact or income.'); window.history.back();</script>";
            exit();
        }

        $sql = "UPDATE student_details_table SET father_name='".clean($_POST['father_name'], $conn)."', mother_name='".clean($_POST['mother_name'], $conn)."', father_number='$f_no', mother_number='$m_no', annual_income='$income' WHERE s_reg='$reg_no'";
    }

    // ---------------- STEP 4: IDENTITY ----------------
    elseif ($step == 4) {
        $aadhar = clean($_POST['aadhar_no'], $conn);
        $pan = strtoupper(clean($_POST['pan_no'], $conn));

        if (!validate($aadhar, "/^\d{12}$/") || !validate($pan, "/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/")) {
            echo "<script>alert('Invalid Aadhar or PAN format.'); window.history.back();</script>";
            exit();
        }

        $sql = "UPDATE student_details_table SET aadhar_no='$aadhar', pan_no='$pan' WHERE s_reg='$reg_no'";
    }

    // ---------------- STEP 5: BANK ----------------
    elseif ($step == 5) {
        $ifsc = strtoupper(clean($_POST['ifsc_code'], $conn));
        $acc = clean($_POST['acc_number'], $conn);

        if (!validate($ifsc, "/^[A-Z]{4}0[A-Z0-9]{6}$/") || strlen($acc) < 9) {
            echo "<script>alert('Invalid Bank details.'); window.history.back();</script>";
            exit();
        }

        $sql = "UPDATE student_details_table SET acc_number='$acc', bank_name='".clean($_POST['bank_name'], $conn)."', ifsc_code='$ifsc' WHERE s_reg='$reg_no'";
    }

    // ---------------- STEP 6: TRANSPORT & FINAL LOCK ----------------
    elseif ($step == 6) {
        $bus_required = clean($_POST['bus_required'], $conn);
        $bus_name = ($bus_required == 'Yes') ? clean($_POST['bus_name'], $conn) : NULL;
        $bus_stop = ($bus_required == 'Yes') ? clean($_POST['bus_stop'], $conn) : NULL;

        if ($bus_required == 'Yes' && (empty($bus_name) || empty($bus_stop))) {
            echo "<script>alert('Please provide Bus Details'); window.history.back();</script>";
            exit();
        }

        // Final Submit: Set edit_count to 1 and status to Locked
        $sql = "UPDATE student_details_table SET bus_required='$bus_required', bus_name='$bus_name', bus_stop='$bus_stop', edit_count=1, edit_status='Locked' WHERE s_reg='$reg_no'";
    }

    // --- 3. EXECUTE UPDATE ---
    if (!empty($sql)) {
        if ($conn->query($sql)) {
            if ($step == 6) {
                echo "<script>alert('Profile Submitted and Locked successfully!'); window.location.href='student_dashboard.php';</script>";
            } else {
                $next = $step + 1;
                echo "<script>window.location.href='personal_details.php?step=$next';</script>";
            }
        } else {
            echo "Error Updating Database: " . $conn->error;
        }
    }
}
?>