<?php
include 'connection.php';
session_start();

header('Content-Type: application/json; charset=utf-8'); // ตั้งค่า header เป็น JSON

// --- ตรวจสอบการล็อกอิน ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit();
}

// --- ตรวจสอบว่าเป็น POST request และมี matchs_id ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['matchs_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'คำขอไม่ถูกต้อง']);
    exit();
}

$matchs_id = intval($_POST['matchs_id']);
$user_id = $_SESSION['user_id'];

// --- เริ่ม Transaction ---
$conn->begin_transaction();

try {
    // --- ดึงข้อมูลการจับคู่ปัจจุบัน ---
    $sql_select = "SELECT * FROM matchs WHERE matchs_id = ? AND status = 'active' FOR UPDATE"; // ล็อกแถวข้อมูลป้องกัน race condition
    $stmt_select = $conn->prepare($sql_select);
    if (!$stmt_select) throw new Exception("Prepare statement failed (select): " . $conn->error);
    $stmt_select->bind_param("i", $matchs_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("ไม่พบรายการจับคู่ที่ยังดำเนินการอยู่ หรือรายการถูกล็อก");
    }

    $match_data = $result->fetch_assoc();
    $stmt_select->close(); // ปิด statement ทันทีหลังใช้งาน

    // --- ตรวจสอบว่าผู้ใช้เป็นส่วนหนึ่งของการจับคู่นี้หรือไม่ ---
    if ($user_id != $match_data['product_owner_id'] && $user_id != $match_data['interested_user_id']) {
        throw new Exception("คุณไม่มีสิทธิ์ยืนยันรายการนี้");
    }

    // --- กำหนดค่าที่จะอัปเดตและตรวจสอบสถานะ ---
    $owner_confirmed = $match_data['owner_confirmed'];
    $interested_confirmed = $match_data['interested_confirmed'];
    $update_column = null;

    if ($user_id == $match_data['product_owner_id']) {
        if ($owner_confirmed == 1) {
            // ถ้าเจ้าของกดยืนยันไปแล้ว (อาจเกิดจากการกดซ้ำ)
             echo json_encode(['status' => 'success', 'message' => 'คุณได้ยืนยันรายการนี้ไปแล้ว', 'action' => 'already_confirmed']);
             $conn->rollback(); // ไม่ต้องทำอะไรต่อ
             exit();
        }
        $update_column = "owner_confirmed = 1";
        $owner_confirmed = 1; // อัปเดตค่าในตัวแปรเพื่อเช็คเงื่อนไขด้านล่าง
    } elseif ($user_id == $match_data['interested_user_id']) {
         if ($interested_confirmed == 1) {
            // ถ้าผู้สนใจกดยืนยันไปแล้ว
             echo json_encode(['status' => 'success', 'message' => 'คุณได้ยืนยันรายการนี้ไปแล้ว', 'action' => 'already_confirmed']);
             $conn->rollback(); // ไม่ต้องทำอะไรต่อ
             exit();
        }
        $update_column = "interested_confirmed = 1";
        $interested_confirmed = 1; // อัปเดตค่าในตัวแปร
    }

    // --- อัปเดตสถานะการยืนยันในตาราง matchs ---
    $sql_update = "UPDATE matchs SET $update_column WHERE matchs_id = ?";
    $stmt_update = $conn->prepare($sql_update);
     if (!$stmt_update) throw new Exception("Prepare statement failed (update confirm): " . $conn->error);
    $stmt_update->bind_param("i", $matchs_id);
    if (!$stmt_update->execute()) {
        throw new Exception("ไม่สามารถอัปเดตสถานะการยืนยันได้");
    }
    $stmt_update->close();

    // --- ตรวจสอบว่าทั้งสองฝ่ายยืนยันหรือยัง ---
    if ($owner_confirmed == 1 && $interested_confirmed == 1) {
        // --- ทั้งสองฝ่ายยืนยันแล้ว: บันทึกลง history และอัปเดต status ---

        // 1. บันทึกลงตาราง history
        $sql_history = "INSERT INTO history (product_owner_id, product_owner_product_id, interested_user_id, interested_user_product_id, exchange_date, matchs_id)
                        VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt_history = $conn->prepare($sql_history);
         if (!$stmt_history) throw new Exception("Prepare statement failed (insert history): " . $conn->error);
        $stmt_history->bind_param("iiiii",
            $match_data['product_owner_id'],
            $match_data['product_owner_product_id'],
            $match_data['interested_user_id'],
            $match_data['interested_user_product_id'],
            $matchs_id // เพิ่ม matchs_id ลงใน history (ถ้าต้องการ)
        );
        if (!$stmt_history->execute()) {
            throw new Exception("ไม่สามารถบันทึกประวัติการแลกเปลี่ยนได้: " . $stmt_history->error);
        }
        $stmt_history->close();

        // 2. อัปเดต status ในตาราง matchs เป็น 'completed'
        $sql_complete = "UPDATE matchs SET status = 'completed' WHERE matchs_id = ?";
        $stmt_complete = $conn->prepare($sql_complete);
         if (!$stmt_complete) throw new Exception("Prepare statement failed (update status): " . $conn->error);
        $stmt_complete->bind_param("i", $matchs_id);
        if (!$stmt_complete->execute()) {
            throw new Exception("ไม่สามารถอัปเดตสถานะการจับคู่เป็นเสร็จสิ้นได้");
        }
        $stmt_complete->close();

        // --- Commit Transaction ---
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'การแลกเปลี่ยนเสร็จสมบูรณ์!', 'action' => 'completed']);

    } else {
        // --- ยืนยันแค่ฝ่ายเดียว ---
        $conn->commit(); // Commit การอัปเดตสถานะยืนยัน
        echo json_encode(['status' => 'success', 'message' => 'ยืนยันสำเร็จ รออีกฝ่ายยืนยัน', 'action' => 'confirmed']);
    }

} catch (Exception $e) {
    // --- หากเกิดข้อผิดพลาด ให้ Rollback ---
    $conn->rollback();
    http_response_code(500); // Internal Server Error
    error_log("Error in confirm_exchange.php: " . $e->getMessage()); // บันทึก error ไว้ดู
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}

$conn->close();
?>
