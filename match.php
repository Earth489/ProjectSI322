<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_owner_id = $_POST['product_owner_id'];
    $product_owner_product_id = $_POST['product_owner_product_id'];
    $interested_user_id = $_POST['interested_user_id'];

    // ตรวจสอบว่ามีการเลือกสินค้าของผู้สนใจหรือไม่
    if (!isset($_POST['selected_product_id'])) {
        echo "<script>alert('กรุณาเลือกสินค้าของผู้สนใจ'); window.location.href='notification.php';</script>";
        exit;
    }

    // วนลูปเพื่อดึงค่า selected_product_id
    foreach ($_POST['selected_product_id'] as $interested_id => $interested_user_product_id) {
        // ตรวจสอบว่ามีการเลือกสินค้าของผู้สนใจหรือไม่
        if (empty($interested_user_product_id)) {
            echo "<script>alert('กรุณาเลือกสินค้าของผู้สนใจ'); window.location.href='notification.php';</script>";
            exit;
        }

        // บันทึกข้อมูลการจับคู่ลงในตาราง matchs
        $sql = "INSERT INTO matchs (product_owner_id, product_owner_product_id, interested_user_id, interested_user_product_id)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $product_owner_id, $product_owner_product_id, $interested_user_id, $interested_user_product_id);

        if ($stmt->execute()) {
            // ดึง matchs_id ล่าสุด
            $matchs_id = $conn->insert_id;

            // อัปเดตสถานะในตาราง interested เป็น 'matched'
            $update_sql = "UPDATE interested SET status = 'matched' WHERE interested_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $interested_id);
            $update_stmt->execute();

            // Redirect ไปยังหน้า match_success.php พร้อมกับ matchs_id
            header("Location: match_success.php?matchs_id=" . $matchs_id);
            exit;
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการจับคู่'); window.location.href='notification.php';</script>";
            exit;
        }
    }
} else {
    header("Location: notification.php");
    exit;
}
?>
