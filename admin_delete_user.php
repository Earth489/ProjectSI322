<?php
    session_start();
    require "connection.php";

    // --- ตรวจสอบการล็อกอินของ Admin ---
    if (!isset($_SESSION['admin_id'])) {
        header("location: Login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];
    $user_id_to_delete = null;

    // --- รับ user_id จาก GET parameter ---
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $user_id_to_delete = intval($_GET['user_id']);

        // --- (Optional) ป้องกัน Admin ลบตัวเอง ---
        // if ($user_id_to_delete === $admin_id) {
        //     header("Location: admin_users.php?error=cannot_delete_self");
        //     exit();
        // }

        // --- เตรียมคำสั่งลบ ---
        // ลบเฉพาะ user_type = 'user' เพื่อความปลอดภัย
        $sql_delete = "DELETE FROM users WHERE user_id = ? AND user_type = 'user'";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);

        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $user_id_to_delete);

            // --- ทำการลบ ---
            if (mysqli_stmt_execute($stmt_delete)) {
                // ตรวจสอบว่ามีแถวที่ถูกลบจริงหรือไม่
                if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                    // ลบสำเร็จ
                    header("Location: admin_users.php?delete_success=1");
                    exit();
                } else {
                    // ไม่พบ User ID หรือ User ไม่ใช่ type 'user'
                    header("Location: admin_users.php?error=delete_not_found");
                    exit();
                }
            } else {
                // เกิดข้อผิดพลาดในการ execute
                // อาจจะ log error ไว้: error_log("Admin Delete Error: " . mysqli_stmt_error($stmt_delete));
                header("Location: admin_users.php?error=delete_failed");
                exit();
            }
            mysqli_stmt_close($stmt_delete);
        } else {
            // เกิดข้อผิดพลาดในการ prepare statement
             // อาจจะ log error ไว้: error_log("Admin Delete Prepare Error: " . mysqli_error($conn));
            header("Location: admin_users.php?error=delete_prepare_failed");
            exit();
        }

    } else {
        // ถ้าไม่มี user_id หรือไม่ใช่ตัวเลข
        header("location: admin_users.php");
        exit();
    }

    mysqli_close($conn);
?>
