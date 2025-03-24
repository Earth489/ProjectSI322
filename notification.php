<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ที่สนใจสินค้าของผู้ใช้ โดยมีสถานะเป็น 'pending' เท่านั้น
$sql = "SELECT i.*, p.product_Name, p.Image AS product_image, u.firstname, u.lastname, u.tel
        FROM interested i
        JOIN product p ON i.product_id = p.product_Id
        JOIN users u ON i.user_id = u.user_id
        WHERE p.user_id = ? AND i.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเตือน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* เพิ่ม CSS สำหรับการแสดงรายการสินค้า */
        .product-list {
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .product-list ul {
            list-style: none;
            padding: 0;
            display: flex; /* จัดเรียงรายการสินค้าในแนวนอน */
            flex-wrap: wrap; /* ขึ้นบรรทัดใหม่เมื่อล้น */
            gap: 10px; /* ระยะห่างระหว่างรายการสินค้า */
        }
        .product-list li {
            margin-bottom: 5px;
            border: 1px solid #ddd;
            padding: 5px;
            width: 200px; /* กำหนดความกว้างของแต่ละรายการสินค้า */
            text-align: center;
        }
        .product-list img {
            width: 100px; /* ปรับขนาดความกว้างตามต้องการ */
            height: 100px; /* ปรับขนาดความสูงตามต้องการ */
            object-fit: cover; /* ครอบตัดรูปภาพให้พอดีกับขนาดที่กำหนด */
            object-position: center; /* จัดตำแหน่งรูปภาพให้อยู่ตรงกลาง */
        }
        .product-list input[type="radio"] {
            margin-right: 5px;
        }
        .product-list p {
            margin: 5px 0;
        }
        body {
            font-family: 'Roboto', sans-serif;
        }
        .text {
            color: #ffffff;
            margin-left: 1.5rem;
            display: block;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .text br {
            margin: 5px 0;
        }
        a{
            text-decoration: none;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            
        }
        .text {
            color: #ffffff;
            margin-left: 1.5rem;
            display: block;
            text-align: center; /* จัดให้อยู่ตรงกลาง */
            font-size: 24px;
            font-weight: bold;
        }
        .text br {
            margin: 5px 0; /* ช่องว่างระหว่างข้อความ */
        }
        a{
            text-decoration: none;
        }
        .navbar {
            display: flex;
            align-items: center;
            background-color: #333;
            justify-content: space-between; /* จัดให้เนื้อหาทั้งหมดอยู่ตรงข้ามกัน */
            position: relative;
            margin: 0 auto;

        }

        .navbar-right {
            margin-right: 1.5rem;
            font-size: 2.5rem;
            color: #ffffff;
        }
        .navbar-right a {
            color: #ffffff; /* ใช้สีของพาเรนต์ */
            text-decoration: none; /* เอาขีดเส้นใต้ของลิงก์ออก (ถ้ามี) */
        }
        .navbar-center a:hover {
            background-color: #007bff; /* Blue color on hover */
            color: #ffffff; /* Keep text white */
            transform: translateY(-3px); /* Slight movement on hover */
        }
        .navbar-center {
            position: relative;
            font-size: 20px;
            padding: 25px 20px;
            letter-spacing: 0.10em;
            display: flex;
            gap: 5s0px; /* เพิ่มระยะห่างระหว่างลิงก์ */
        }

        .navbar-center a {
            font-family: 'Roboto', sans-serif;
            color: #ffffff; /* สีตัวอักษร */
            text-decoration: none; /* ลบขีดเส้นใต้ */
            padding: 10px 20px;
            border-radius: 5px;
        }
        .interested-product-image {
            width: 150px; /* ปรับขนาดความกว้างตามต้องการ */
            height: auto; /* ให้ความสูงปรับตามสัดส่วน */
            display: block;
            margin: 0 auto 10px;
            object-fit: cover; /* ครอบตัดรูปภาพให้พอดีกับขนาดที่กำหนด */
            object-position: center; /* จัดตำแหน่งรูปภาพให้อยู่ตรงกลาง */
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="main_product_post.php">
        <span class="text">
            แลกเปลี่ยน<br>
            ทรัพยากร
        </span>
    </a>
    <div class="navbar-center">
        <a href="notification.php">แจ้งเตือน</a>
        <a href="matchslist.php">รายการจับคู่</a>
        <a href="history.php">ดูประวัติการแลกเปลี่ยน</a>
    </div>
    <div class="navbar-right">
        <a href="profile.php"><span><i class="fa-regular fa-user"></i></span></a>
    </div>
</div>
    <main>
        <h1>แจ้งเตือน</h1>
        <?php if ($result->num_rows > 0): ?>
            <form action="match.php" method="POST">
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <!-- แสดงรูปภาพสินค้าที่ถูกกดสนใจ -->
                        <img src="uploads/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_Name']; ?>" class="interested-product-image">
                        <p><strong>สินค้าของคุณ:</strong> <?php echo $row['product_Name']; ?></p>
                        <p><strong>ผู้สนใจ:</strong> <?php echo $row['firstname'] . ' ' . $row['lastname']; ?></p>
                        <p><strong>เบอร์โทร:</strong> <?php echo $row['tel']; ?></p>
                        <p><strong>วันที่สนใจ:</strong> <?php echo $row['interested_date']; ?></p>

                        <!-- แสดงรายการสินค้าของผู้สนใจ -->
                        <div class="product-list">
                            <p><strong>สินค้าของผู้สนใจ:</strong></p>
                            <?php
                                // ดึงข้อมูลสินค้าของผู้สนใจ
                                $interested_user_id = $row['user_id'];
                                // แก้ไขคำสั่ง SQL เพื่อดึงข้อมูลเพิ่มเติม
                                $product_sql = "SELECT product_Id, product_Name, product_detail, Image, product_price, Product_exchanged, product_category FROM product WHERE user_id = ?";
                                $product_stmt = $conn->prepare($product_sql);
                                $product_stmt->bind_param("i", $interested_user_id);
                                $product_stmt->execute();
                                $product_result = $product_stmt->get_result();

                                if ($product_result->num_rows > 0) {
                                    echo "<ul>";
                                    $has_product = true; // เพิ่มตัวแปรตรวจสอบว่ามีสินค้าหรือไม่
                                    while ($product_row = $product_result->fetch_assoc()) {
                                        echo "<li>";
                                        echo "<img src='uploads/".$product_row['Image']."' alt='".$product_row['product_Name']."'>";
                                        // ตรวจสอบว่า $has_product เป็น true ก่อนแสดง radio button
                                        if ($has_product) {
                                            echo "<p><input type='radio' name='selected_product_id[".$row['interested_id']."]' value='".$product_row['product_Id']."'> ".$product_row['product_Name']."</p>";
                                        }
                                        // เพิ่มข้อมูล product_detail, product_price, Product_exchanged, product_category
                                        echo "<p><strong>รายละเอียด:</strong> ".$product_row['product_detail']."</p>";
                                        echo "<p><strong>ราคา:</strong> ".$product_row['product_price']." บาท</p>";
                                        echo "<p><strong>สินค้าแลกเปลี่ยน:</strong> ".$product_row['Product_exchanged']."</p>";
                                        echo "<p><strong>ประเภทสินค้า:</strong> ".$product_row['product_category']."</p>";
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>ไม่มีสินค้า</p>";
                                    $has_product = false; // กำหนดค่าเป็น false เมื่อไม่มีสินค้า
                                }
                            ?>
                        </div>
                        <input type="hidden" name="product_owner_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="product_owner_product_id" value="<?php echo $row['product_id']; ?>">
                        <input type="hidden" name="interested_user_id" value="<?php echo $row['user_id']; ?>">
                    </li>
                <?php endwhile; ?>
            </ul>
            <button type="submit" class="btn btn-primary">จับคู่</button>
            </form>
        <?php else: ?>
            <p>ไม่มีการแจ้งเตือน</p>
        <?php endif; ?>
    </main>
</body>
</html>
