<?php  
    include 'connection.php';
    session_start();

    // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $productName = $_POST['product-name'];
        $productDescription = $_POST['product-description'];
        $productPrice = $_POST['product-price'];
        $exchangeItem = $_POST['exchange-item'];
        $productCategory = $_POST['product-category'];
        $productStatus = "ต้องการแลก"; // กำหนดสถานะสินค้าเป็น "ต้องการแลก"

        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = !empty($_FILES["product-image"]["name"]) ? basename($_FILES["product-image"]["name"]) : NULL;
        $targetFile = $uploadDir . $imageName;

        // ตรวจสอบการอัปโหลดไฟล์
        if ($imageName) {
            $check = getimagesize($_FILES["product-image"]["tmp_name"]);
            if ($check !== false) {
                if (!move_uploaded_file($_FILES["product-image"]["tmp_name"], $targetFile)) {
                    echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์'); window.location.href='index.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ'); window.location.href='index.php';</script>";
                exit;
            }
        }

        // เพิ่ม user_id ลงในฐานข้อมูล
        $user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO product (product_Name, product_detail, product_price, Product_exchanged, Image, product_category, product_status, user_id) 
                VALUES ('$productName', '$productDescription', '$productPrice', '$exchangeItem', '$imageName','$productCategory','$productStatus', '$user_id')";

        // ตรวจสอบการกรอกข้อมูลราคา
        if (!is_numeric($_POST['product-price'])) {
            echo "<script>alert('กรุณากรอกราคาเป็นตัวเลขเท่านั้น'); window.history.back();</script>";
            exit;
        }

        // แทรกข้อมูลลงฐานข้อมูล
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location.href='main_product_post.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "'); window.location.href='main_product_post.php';</script>";
        }

        mysqli_close($conn);
    }
?>
