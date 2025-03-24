<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "error";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matchs_id = $_POST['matchs_id'];

    // ตรวจสอบว่า matchs_id ถูกต้องหรือไม่
    if (empty($matchs_id) || !is_numeric($matchs_id)) {
        echo "error";
        exit();
    }

    // ลบข้อมูลการจับคู่จากตาราง matchs
    $sql = "DELETE FROM matchs WHERE matchs_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $matchs_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
