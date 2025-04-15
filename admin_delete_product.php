<?php
    session_start();
    require "connection.php";

    // --- ตรวจสอบการเชื่อมต่อฐานข้อมูล ---
    if (!$conn) {
        // หากเชื่อมต่อไม่ได้ ให้หยุดการทำงานและแสดงข้อผิดพลาด (หรือ redirect ไปหน้า error)
        // ควร log error ไว้ด้วย
        error_log("Database connection failed in admin_delete_product.php: " . mysqli_connect_error());
        header("Location: admin_product.php?error=db_connection_failed");
        exit();
    }

    // --- ตรวจสอบการล็อกอินของ Admin ---
    if (!isset($_SESSION['admin_id'])) {
        header("location: Login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id']; // เก็บไว้เผื่อใช้ตรวจสอบสิทธิ์เพิ่มเติม
    $product_id_to_delete = null;
    $image_to_delete = null;

    // --- รับ product_id จาก GET parameter ---
    if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
        $product_id_to_delete = intval($_GET['product_id']);

        // --- เริ่ม Transaction ---
        mysqli_begin_transaction($conn);

        try {
            // 1. ดึงชื่อไฟล์รูปภาพก่อนลบข้อมูลออกจาก DB
            $sql_get_image = "SELECT Image FROM product WHERE product_Id = ?";
            $stmt_get_image = mysqli_prepare($conn, $sql_get_image);
            if (!$stmt_get_image) {
                throw new Exception("Prepare statement failed (get image): " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt_get_image, "i", $product_id_to_delete);
            mysqli_stmt_execute($stmt_get_image);
            $result_image = mysqli_stmt_get_result($stmt_get_image);

            if ($row_image = mysqli_fetch_assoc($result_image)) {
                $image_to_delete = $row_image['Image'];
            }
            mysqli_stmt_close($stmt_get_image);

            // 2. เตรียมคำสั่งลบข้อมูลสินค้าออกจากตาราง product
            $sql_delete = "DELETE FROM product WHERE product_Id = ?";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);
            if (!$stmt_delete) {
                throw new Exception("Prepare statement failed (delete product): " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt_delete, "i", $product_id_to_delete);

            // 3. ทำการลบข้อมูลสินค้า
            if (mysqli_stmt_execute($stmt_delete)) {
                // ตรวจสอบว่ามีแถวที่ถูกลบจริงหรือไม่
                if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                    // 4. ลบไฟล์รูปภาพ (ถ้ามีชื่อไฟล์และไฟล์นั้นมีอยู่จริง)
                    if (!empty($image_to_delete)) {
                        $image_path = "uploads/" . $image_to_delete;
                        if (file_exists($image_path)) {
                            if (!unlink($image_path)) {
                                // ถ้าลบไฟล์ไม่สำเร็จ อาจจะ Log error ไว้ แต่ยังถือว่าลบข้อมูล DB สำเร็จ
                                error_log("Failed to delete image file: " . $image_path . " for product_id: " . $product_id_to_delete);
                                // ไม่จำเป็นต้อง throw exception ที่นี่ เพราะข้อมูลใน DB ถูกลบไปแล้ว
                            }
                        } else {
                             error_log("Image file not found: " . $image_path . " for product_id: " . $product_id_to_delete);
                        }
                    }

                    // --- ถ้าทุกอย่างสำเร็จ ให้ Commit ---
                    mysqli_commit($conn);
                    header("Location: admin_product.php?delete_success=1");
                    exit();

                } else {
                    // ไม่พบ Product ID ที่ต้องการลบ
                    throw new Exception("Product not found for deletion.");
                }
            } else {
                // เกิดข้อผิดพลาดในการ execute delete
                throw new Exception("Failed to execute delete statement: " . mysqli_stmt_error($stmt_delete));
            }
            mysqli_stmt_close($stmt_delete);

        } catch (Exception $e) {
            // --- หากเกิดข้อผิดพลาด ให้ Rollback ---
            mysqli_rollback($conn);
            error_log("Error in admin_delete_product.php: " . $e->getMessage()); // บันทึก error ไว้ดู
            // Redirect กลับพร้อม error message
            if ($e->getMessage() === "Product not found for deletion.") {
                 header("Location: admin_product.php?error=delete_not_found");
            } else {
                 header("Location: admin_product.php?error=delete_failed");
            }
            exit();
        }

    } else {
        // ถ้าไม่มี product_id หรือไม่ใช่ตัวเลข
        header("location: admin_product.php?error=invalid_request");
        exit();
    }

    mysqli_close($conn);
?>
