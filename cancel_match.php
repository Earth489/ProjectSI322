<?php
include 'connection.php';
session_start();

// ตรวจสอบการล็อกอินก่อน
if (!isset($_SESSION['user_id'])) {
    // อาจจะส่ง response เป็น JSON หรือข้อความ error ที่เหมาะสม
    http_response_code(401); // Unauthorized
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matchs_id'])) {
    $matchs_id = intval($_POST['matchs_id']);
    $user_id = $_SESSION['user_id'];

    // --- ใช้ Transaction เพื่อความปลอดภัย ---
    $conn->begin_transaction();

    try {
        // 1. ตรวจสอบสิทธิ์ และดึง interested_id ที่เกี่ยวข้อง
        // (สมมติว่าตาราง matchs มีคอลัมน์ interested_id)
        $check_sql = "SELECT interested_id, product_owner_id, interested_user_id
                      FROM matchs
                      WHERE matchs_id = ? AND (product_owner_id = ? OR interested_user_id = ?)";
        $stmt_check = $conn->prepare($check_sql);
        if (!$stmt_check) {
             throw new Exception("Prepare statement failed (check): " . $conn->error);
        }
        $stmt_check->bind_param("iii", $matchs_id, $user_id, $user_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $match_data = $result->fetch_assoc();
            $interested_id_to_delete = $match_data['interested_id']; // ดึง ID ของ interested

            // 2. ลบข้อมูลออกจากตาราง matchs
            $delete_match_sql = "DELETE FROM matchs WHERE matchs_id = ?";
            $stmt_delete_match = $conn->prepare($delete_match_sql);
             if (!$stmt_delete_match) {
                 throw new Exception("Prepare statement failed (delete match): " . $conn->error);
            }
            $stmt_delete_match->bind_param("i", $matchs_id);
            if (!$stmt_delete_match->execute()) {
                throw new Exception("ไม่สามารถยกเลิกการจับคู่ในตาราง matchs ได้");
            }
            $stmt_delete_match->close();

            // 3. ลบข้อมูลออกจากตาราง interested (ถ้ามี interested_id)
            if ($interested_id_to_delete !== null) {
                $delete_interested_sql = "DELETE FROM interested WHERE interested_id = ?";
                $stmt_delete_interested = $conn->prepare($delete_interested_sql);
                 if (!$stmt_delete_interested) {
                     // อาจจะเลือกที่จะไม่ throw exception ที่นี่ แต่ log error ไว้
                     // throw new Exception("Prepare statement failed (delete interested): " . $conn->error);
                     error_log("Prepare statement failed (delete interested): " . $conn->error . " for interested_id: " . $interested_id_to_delete);
                 } else {
                    $stmt_delete_interested->bind_param("i", $interested_id_to_delete);
                    if (!$stmt_delete_interested->execute()) {
                        // อาจจะเลือกที่จะไม่ throw exception ที่นี่ แต่ log error ไว้
                        // throw new Exception("ไม่สามารถลบข้อมูลในตาราง interested ได้");
                         error_log("Failed to delete from interested table for interested_id: " . $interested_id_to_delete);
                    }
                    $stmt_delete_interested->close();
                 }
            } else {
                 // Log กรณีที่ interested_id เป็น NULL ในตาราง matchs
                 error_log("interested_id is NULL for matchs_id: " . $matchs_id);
            }


            // --- ถ้าทุกอย่างสำเร็จ ให้ commit ---
            $conn->commit();
            // ส่ง response กลับไปให้ JavaScript (ควรใช้ JSON)
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'success', 'message' => 'ยกเลิกการจับคู่และข้อมูลที่เกี่ยวข้องเรียบร้อยแล้ว']);

        } else {
            // ไม่มีสิทธิ์ หรือไม่พบรายการ
            $conn->rollback(); // ไม่ต้องทำอะไร ก็ rollback กลับ
            http_response_code(403); // Forbidden
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ยกเลิกรายการนี้ หรือไม่พบรายการ']);
        }
        $stmt_check->close();

    } catch (Exception $e) {
        // --- หากเกิดข้อผิดพลาด ให้ rollback ---
        $conn->rollback();
        error_log("Error in cancel_match.php: " . $e->getMessage()); // บันทึก error ไว้ดู
        http_response_code(500); // Internal Server Error
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด ไม่สามารถยกเลิกได้ กรุณาลองใหม่']);
    }

} else {
    http_response_code(400); // Bad Request
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'คำขอไม่ถูกต้อง']);
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
