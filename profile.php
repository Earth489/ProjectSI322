<?php 
    include 'connection.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
?>    

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     <title>โปรไฟล์</title>
    <style>
        
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
        /* ปรับขนาด .profile-container */
        .profile-container {
            width: 100%; /* ใช้ความกว้างเต็มของหน้าจอ */
            max-width: 1300px; /* จำกัดความกว้างสูงสุดให้ไม่เกิน 1200px */
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            align-items: center;
            position: relative;
            margin-top: 50px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-content {
            flex: 1;
            width: 100%;
            padding: 10px;
        }

        .info-box {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 16px;
            display: flex;
            flex-direction: column;
        }

        .info-box strong {
            margin-bottom: 5px;
        }
        .container-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .logout-btn {
            background: red;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 200px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .edit_profile {
            background: green;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 200px;
            margin-top: 20px;
        }
        .edit_password{
            background: blue;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 200px;
            margin-top: 20px;
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

    <div class="container-center">
        
        <div class="profile-container">
        <h2>Profile</h2>
            <?php
                $sql = "SELECT email, firstname, lastname, gender, tel, birth_date, address, community 
                        FROM users WHERE user_id = '$user_id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    echo "<div class='profile-content'>";
                    // เปลี่ยนชื่อให้เหมือนกับข้อมูลอื่น ๆ
                    echo "<div class='info-box'><strong>ชื่อ</strong><span>" . $row["firstname"] . " " . $row["lastname"] . "</span></div>";
                    echo "<div class='info-box'><strong>อีเมล</strong><span>" . $row["email"] . "</span></div>";
                    echo "<div class='info-box'><strong>เพศ</strong><span>" . $row["gender"] . "</span></div>";

                    // เบอร์โทร + วันเกิด (ROW เดียวกัน)
                    echo "<div class='info-box'><strong>เบอร์โทร</strong><span>" . $row["tel"] . "</span></div>";
                    echo "<div class='info-box'><strong>วันเกิด</strong><span>" . $row["birth_date"] . "</span></div>";

                    // ที่อยู่ + ชุมชน (ROW เดียวกัน)
                    echo "<div class='info-box'><strong>ที่อยู่</strong><span>" . $row["address"] . "</span></div>";
                    echo "<div class='info-box'><strong>ชุมชน</strong><span>" . $row["community"] . "</span></div>";

                    echo "</div>";
                } else {
                    echo "<p class='text-center'>ไม่พบข้อมูลผู้ใช้</p>";
                }
            ?>
        </div>
     
            <a href="edit_profile.php" class="edit_profile">แก้ไขโปรไฟล์</a>
            <a href="edit_password.php" class="edit_password">เปลี่ยนรหัสผ่าน</a>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>

    </div>
</body>
</html>
