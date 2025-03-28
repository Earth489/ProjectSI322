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
    $user_type = $_POST['user_type']; // รับค่า user_type

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

    // อัปเดตสถานะการยืนยัน
    if ($user_type == 'owner') {
        $update_sql = "UPDATE matchs SET product_owner_confirm = 1 WHERE matchs_id = ?";
    } else {
        $update_sql = "UPDATE matchs SET interested_user_confirm = 1 WHERE matchs_id = ?";
    }
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $matchs_id);
    $update_stmt->execute();

    // ตรวจสอบการยืนยันทั้งสองฝ่าย
    $check_sql = "SELECT product_owner_confirm, interested_user_confirm FROM matchs WHERE matchs_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $matchs_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    // ตรวจสอบว่าทั้งสองฝ่ายยืนยันแล้วหรือไม่
    if ($check_row['product_owner_confirm'] == 1 && $check_row['interested_user_confirm'] == 1) {
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
        // ถ้ายังไม่ครบ ให้ส่ง waiting กลับไป
        echo "waiting";
    }
} else {
    echo "error";
}
?>
