<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "error";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $interested_id = $_POST['interested_id'];

    // ตรวจสอบว่า interested_id ถูกต้องหรือไม่
    if (empty($interested_id) || !is_numeric($interested_id)) {
        echo "error";
        exit();
    }

    // ลบข้อมูลคำขอจากตาราง interested
    $sql = "DELETE FROM interested WHERE interested_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $interested_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
