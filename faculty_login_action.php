<?php
session_start();
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form-la irunthu data-va edukkurom
    $fac_id = isset($_POST['fac_id']) ? mysqli_real_escape_string($conn, $_POST['fac_id']) : '';
    $fac_pass = isset($_POST['fac_pass']) ? mysqli_real_escape_string($conn, $_POST['fac_pass']) : '';

    if (!empty($fac_id) && !empty($fac_pass)) {
        // Query remains the same, fetching everything from faculty_table
        $sql = "SELECT * FROM faculty_table WHERE faculty_id = '$fac_id' AND password = '$fac_pass'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            // --- SESSION SETTINGS ---
            $_SESSION['faculty_logged_in'] = true;
            $_SESSION['f_id'] = $row['faculty_id'];
            $_SESSION['f_name'] = $row['name'];
            
            // NEW: Incharge details-ah session-la store pandrom
            // Inga 'incharge_class' dhaan namma DB-la add panna puthu column name
            $_SESSION['class_incharge'] = $row['class_incharge']; 
            
            header("Location: faculty_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid Faculty ID or Password!'); window.location='faculty_login.php';</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields!'); window.location='faculty_login.php';</script>";
    }
} else {
    header("Location: faculty_login.php");
}
?>