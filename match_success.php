<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามี matchs_id หรือไม่
if (!isset($_GET['matchs_id'])) {
    header("Location: notification.php");
    exit();
}

$matchs_id = $_GET['matchs_id'];

// ดึงข้อมูลการจับคู่
$sql = "SELECT m.*, po.firstname AS product_owner_name, po.tel AS product_owner_tel, iu.firstname AS interested_user_name, iu.tel AS interested_user_tel,
        pop.product_Name AS product_owner_product_name, pop.Image AS product_owner_product_image, iup.product_Name AS interested_user_product_name, iup.Image AS interested_user_product_image
        FROM matchs m
        JOIN users po ON m.product_owner_id = po.user_id
        JOIN users iu ON m.interested_user_id = iu.user_id
        JOIN product pop ON m.product_owner_product_id = pop.product_Id
        JOIN product iup ON m.interested_user_product_id = iup.product_Id
        WHERE m.matchs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $matchs_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "ไม่พบข้อมูลการจับคู่";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จับคู่สำเร็จ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            text-align: center;
        }
        .container {
            margin-top: 50px;
        }
        .chat-btn {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .info-row {
            display: flex; /* จัดเรียงในแนวนอน */
            justify-content: space-around; /* กระจายพื้นที่ว่างรอบๆ */
            align-items: center; /* จัดให้อยู่ตรงกลางแนวตั้ง */
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .info-box {
            text-align: center; /* จัดข้อความให้อยู่ตรงกลาง */
            width: 45%; /* กำหนดความกว้างของแต่ละกล่อง */
        }
        .product-image {
            width: 150px; /* ปรับขนาดตามต้องการ */
            height: 150px; /* ปรับขนาดตามต้องการ */
            object-fit: cover; /* ครอบตัดรูปภาพให้พอดีกับขนาดที่กำหนด */
            object-position: center; /* จัดตำแหน่งรูปภาพให้อยู่ตรงกลาง */
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>จับคู่สำเร็จ!</h1>
        <div class="info-row">
            <div class="info-box">
                <!-- แสดงรูปภาพสินค้าของเจ้าของ -->
                <img src="uploads/<?php echo $row['product_owner_product_image']; ?>" alt="<?php echo $row['product_owner_product_name']; ?>" class="product-image">
                <p><strong>สินค้าของคุณ:</strong> <?php echo $row['product_owner_product_name']; ?></p>
                <p><strong>ชื่อของคุณ:</strong> <?php echo $row['product_owner_name']; ?></p>
                <p><strong>เบอร์โทรของคุณ:</strong> <?php echo $row['product_owner_tel']; ?></p>
            </div>
            <div class="info-box">
                <!-- แสดงรูปภาพสินค้าของผู้สนใจ -->
                <img src="uploads/<?php echo $row['interested_user_product_image']; ?>" alt="<?php echo $row['interested_user_product_name']; ?>" class="product-image">
                <p><strong>สินค้าที่สนใจ:</strong> <?php echo $row['interested_user_product_name']; ?></p>
                <p><strong>ชื่อผู้สนใจ:</strong> <?php echo $row['interested_user_name']; ?></p>
                <p><strong>เบอร์โทรผู้สนใจ:</strong> <?php echo $row['interested_user_tel']; ?></p>
            </div>
        </div>
        <p>วันที่จับคู่: <?php echo $row['match_date']; ?></p>
        <a href="main_product_post.php" class="chat-btn">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>
