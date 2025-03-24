<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_POST['user_id'];

    // ตรวจสอบว่าสินค้านี้เป็นของผู้ใช้เองหรือไม่
    $owner_sql = "SELECT user_id FROM product WHERE product_Id = ?";
    $owner_stmt = $conn->prepare($owner_sql);
    $owner_stmt->bind_param("i", $product_id);
    $owner_stmt->execute();
    $owner_result = $owner_stmt->get_result();

    if ($owner_result->num_rows > 0) {
        $owner_row = $owner_result->fetch_assoc();
        $owner_id = $owner_row['user_id'];

        if ($user_id == $owner_id) {
            echo json_encode(['status' => 'error', 'message' => 'คุณไม่สามารถกดสนใจสินค้าของตัวเองได้']);
            exit();
        }
    } else {
         echo json_encode(['status' => 'error', 'message' => 'ไม่พบสินค้านี้']);
        exit();
    }

    // ตรวจสอบว่าผู้ใช้ได้กดสนใจสินค้านี้ไปแล้วหรือยัง
    $check_sql = "SELECT * FROM interested WHERE product_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $product_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'คุณได้กดสนใจสินค้านี้ไปแล้ว']);
        exit();
    }

    // บันทึกข้อมูลลงในตาราง interested
    $insert_sql = "INSERT INTO interested (product_id, user_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $product_id, $user_id);

    if ($insert_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'บันทึกความสนใจสำเร็จ']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
