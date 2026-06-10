<?php
include 'config.php';

// URL-la irundhu varra row ID-ai edukkirom
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Status-ah 'Pending'-la irundhu 'Granted'-nu update pandrom
    $sql = "UPDATE faculty_leaves SET status = 'Granted' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        // Success alert kaatti thirumba admin page-kae pogum
        echo "<script>alert('Leave Request Granted Successfully!'); window.location.href='admin_approval.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>