<?php 
    include 'connection.php';
    session_start();

    // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

?>    

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>รายการสินค้า</title>
    <link rel="stylesheet" href="mpp.css">
</head>

<body>

<div class="navbar">
<a href="main_product_post.php">
<span class="text">
        แลกเปลี่ยน<br>
        ทรัพยากร
    </span>


    <div class="navbar-center">
    <a href="notification.php">แจ้งเตือน</a>
    <a href="history.php">ดูประวัติการแลกเปลี่ยน</a>
        </div>
        <div class="navbar-right">
    <a href="profile.php"><span><i class="fa-regular fa-user"></i></span></a>
</div>
</div>
    <main>
    <a class="post btn btn-primary" href="product_post_page.php">โพสสินค้า</a>

        <div class="item-list">
            <?php
            // ดึงข้อมูลสินค้าจากฐานข้อมูลและชื่อผู้ใช้จากตาราง users
            $sql = "SELECT product.*, users.firstname, users.lastname 
        FROM product
        JOIN users ON product.user_id = users.user_id";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="item">';
                    echo '<p><strong></strong> '.$row["firstname"].' '.$row["lastname"].'</p>'; // แสดงชื่อผู้โพสต์
                    echo '<a href="product_detail.php?product_Id='.$row["product_Id"].'">';
                    echo '<img src="uploads/'.$row["Image"].'" alt="'.$row["product_Name"].'">';
                    echo '<h2>'.$row["product_Name"].'</h2>';
                    echo '</a>';
                    echo '<p><strong>ราคา:</strong> '.$row["product_price"].' บาท</p>';
                    echo '<p><strong>สินค้าแลกเปลี่ยน:</strong> '.$row["Product_exchanged"].'</p>';
                    echo '<button class="like-button" onclick="handleLike('.$row["product_Id"].')">สนใจแลก</button>';
                    echo '</div>';
                }
            } else {
                echo "<p>ไม่มีสินค้าในระบบ</p>";
            }

            // ปิดการเชื่อมต่อฐานข้อมูล
            $conn->close();
            ?>
        </div>
    </main>

</body>
</html>
