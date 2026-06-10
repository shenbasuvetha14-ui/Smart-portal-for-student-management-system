<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture and Clean Data
    // Use strtoupper() to force uppercase for Name and Reg No
    $name     = strtoupper(mysqli_real_escape_string($conn, $_POST['s_name']));
    $reg_no   = strtoupper(mysqli_real_escape_string($conn, $_POST['reg_no']));
    $batch    = mysqli_real_escape_string($conn, $_POST['batch']);
    // Password should usually stay case-sensitive, but if you want it uppercase too:
    $pass     = mysqli_real_escape_string($conn, $_POST['password']);
    
    // 2. Capture Hidden Faculty Data
    $class_name = mysqli_real_escape_string($conn, $_POST['class_name']); 
    $f_id       = mysqli_real_escape_string($conn, $_POST['faculty_id']); 

    // 3. Insert into student_table (using 'added_by' based on your database screenshot)
    $sql1 = "INSERT INTO student_table (name, reg_no, password,batch, class_name, added_by) 
             VALUES ('$name', '$reg_no', '$pass','$batch', '$class_name', '$f_id')";

    // 4. Insert into student_details_table
    $sql2 = "INSERT INTO student_details_table (s_reg, s_name, class_name, assigned_incharge, edit_status) 
             VALUES ('$reg_no', '$name', '$class_name', '$f_id', 'Approved')";

  if (mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
    echo "<script>
            alert('Student $name added successfully! You can now add the next student.'); 
            window.location='add_student.php'; 
          </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>