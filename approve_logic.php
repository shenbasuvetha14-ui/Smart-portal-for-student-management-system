<?php
include 'config.php';

// Button click aanathum inga data varum
if (isset($_GET['reg']) && isset($_GET['status'])) {
    $reg = mysqli_real_escape_string($conn, $_GET['reg']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    // Status-ah update panrom
    // Approved nu iruntha, 'Approved' nu status maarum
    $sql = "UPDATE student_details_table 
            SET edit_status = '$status' 
            WHERE s_reg = '$reg'";

    if ($conn->query($sql)) {
        // Success aana udane thirumba request page-ke redirect aagum
        echo "<script>
                alert('Request $status successfully!'); 
                window.location='view_edit_request.php';
              </script>";
    } else {
        echo "Database Error: " . $conn->error;
    }
} else {
    // File irukku, aana direct-ah link illama vantha inga varum
    echo "<h2>No action performed. <a href='view_edit_request.php'>Go Back</a></h2>";
}
?>