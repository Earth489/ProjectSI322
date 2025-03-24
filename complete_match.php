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

    // ดึงข้อมูลการจับคู่
    $sql = "SELECT * FROM matchs WHERE matchs_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $matchs_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "error";
        exit();
    }

    $row = $result->fetch_assoc();

    // บันทึกข้อมูลลงในตาราง history
    $insert_sql = "INSERT INTO history (matchs_id, product_owner_id, product_owner_product_id, interested_user_id, interested_user_product_id)
                   VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiiii", $matchs_id, $row['product_owner_id'], $row['product_owner_product_id'], $row['interested_user_id'], $row['interested_user_product_id']);

    if ($insert_stmt->execute()) {
        // ลบข้อมูลการจับคู่จากตาราง matchs
        $delete_sql = "DELETE FROM matchs WHERE matchs_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $matchs_id);
        $delete_stmt->execute();
        
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
