<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get and Sanitize Input
    $input_reg = mysqli_real_escape_string($conn, $_POST['s_reg']);
    $input_pass = mysqli_real_escape_string($conn, $_POST['s_pass']);

    // 2. Query the database
    $sql = "SELECT * FROM student_table WHERE reg_no = '$input_reg' AND password = '$input_pass'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // 3. Set Session Variables
        $_SESSION['student_logged_in'] = true;
        $_SESSION['s_id']   = $row['id']; 
        $_SESSION['s_name'] = $row['name']; 
        $_SESSION['s_reg']  = $row['reg_no']; 
        $_SESSION['s_dept'] = $row['department'] ?? ''; 

        // 4. Force session save and redirect
        session_write_close(); 
        header("Location: student_dashboard.php");
        exit();
    } else {
        // Invalid credentials
        echo "<script>
                alert('Invalid Registration Number or Password!'); 
                window.location.href='student_login.php';
              </script>";
        exit();
    }
} else {
    header("Location: student_login.php");
    exit();
}
?>