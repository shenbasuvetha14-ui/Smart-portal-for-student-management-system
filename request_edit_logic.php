<?php
ob_start();
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['student_logged_in'])) {
    
    $reg_no = mysqli_real_escape_string($conn, $_POST['s_reg']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // 1. மாணவரின் வகுப்பை எடுக்கிறோம்
    $get_st = "SELECT class_name FROM student_details_table WHERE s_reg = '$reg_no'";
    $st_res = $conn->query($get_st);

    if ($st_res && $st_res->num_rows > 0) {
        $st_row = $st_res->fetch_assoc();
        $student_class = trim($st_row['class_name']);


        $get_fac = "SELECT name FROM faculty_table WHERE TRIM(class_incharge) = '$student_class' LIMIT 1";
        $fac_res = $conn->query($get_fac);

        if ($fac_res && $fac_res->num_rows > 0) {
            $fac_row = $fac_res->fetch_assoc();
            $fac_name = $fac_row['name']; 

          
            $sql = "UPDATE student_details_table 
                    SET edit_status = 'Pending', 
                        edit_reason = '$reason', 
                        assigned_incharge = '$fac_name' 
                    WHERE s_reg = '$reg_no'";

            if ($conn->query($sql)) {
                echo "<script>alert('Request sent successfully to $fac_name!'); window.location.href='personal_details.php';</script>";
            } else {
                echo "Database Error: " . $conn->error;
            }
        } else {
            echo "<script>alert('Error: No faculty assigned for class $student_class'); window.history.back();</script>";
        }
    } else {
        echo "Error: Student not found.";
    }
}
?>