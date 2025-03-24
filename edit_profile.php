<?php
    include 'connection.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // เรียกข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = "SELECT firstname, lastname, gender, tel, birth_date, address, community
            FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<p>ไม่พบข้อมูลผู้ใช้</p>";
        exit();
    }

    // อัปเดตข้อมูลเมื่อมีการส่งฟอร์ม
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $gender = $_POST['gender'];
        $tel = $_POST['tel'];
        $birth_date = $_POST['birth_date'];
        $address = $_POST['address'];
        $community = $_POST['community'];

        $update_sql = "UPDATE users SET 
                        firstname = '$firstname',
                        lastname = '$lastname',
                        gender = '$gender',
                        tel = '$tel',
                        birth_date = '$birth_date',
                        address = '$address',
                        community = '$community'
                      WHERE user_id = '$user_id'";

        if ($conn->query($update_sql) === TRUE) {
            // เมื่อการอัปเดตสำเร็จ, redirect ไปยังหน้า profile.php
            header("Location: profile.php");
            exit();  // หยุดการทำงานของโค้ด
        } else {
            echo "<p>เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error . "</p>";
        }
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
    <title>แก้ไขโปรไฟล์</title>
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
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-container {
            width: 100%;
            max-width: 1300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            align-items: center;
            position: relative;
            margin-top: 50px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .buttons {
            display: flex;
            justify-content: space-between;

        }
        .save-btn {
            background: green;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 35%;
            border: none;  /* ลบขอบดำออก */
            outline: none; /* ป้องกันเส้นขอบตอนคลิก */
            margin-left: 100px;
        }

        .cancel-btn {
            background: red;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 35%;
            border: none;  /* ลบขอบดำออก */
            outline: none; /* ป้องกันเส้นขอบตอนคลิก */
            margin-right: 100px;
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

    <div class="container">
        <div class="profile-container">
            <h2>แก้ไขโปรไฟล์</h2>
            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="firstname">ชื่อ</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastname">นามสกุล</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">เพศ</label>
                    <select id="gender" name="gender" required>
                        <option value="ชาย" <?php if ($row['gender'] == 'ชาย') echo 'selected'; ?>>ชาย</option>
                        <option value="หญิง" <?php if ($row['gender'] == 'หญิง') echo 'selected'; ?>>หญิง</option>
                        <option value="อื่นๆ" <?php if ($row['gender'] == 'อื่นๆ') echo 'selected'; ?>>อื่นๆ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tel">เบอร์โทรศัพท์</label>
                    <input type="text" id="tel" name="tel" value="<?php echo $row['tel']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="birth_date">วันเกิด</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?php echo $row['birth_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">ที่อยู่</label>
                    <input type="text" id="address" name="address" value="<?php echo $row['address']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="community">ชุมชน</label>
                    <input type="text" id="community" name="community" value="<?php echo $row['community']; ?>" required>
                </div>

                <div class="buttons">
                    <button type="submit" class="save-btn">บันทึก</button>
                    <a href="profile.php" class="cancel-btn">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
