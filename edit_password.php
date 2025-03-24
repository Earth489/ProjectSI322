<?php
    include 'connection.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // ตรวจสอบรหัสผ่านปัจจุบัน
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($stored_password);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_password, $stored_password)) {
            $error = "รหัสผ่านปัจจุบันไม่ถูกต้อง";
        } elseif ($new_password !== $confirm_password) {
            $error = "รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            // อัปเดตรหัสผ่านใหม่
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            if ($update_stmt->execute()) {
                echo "<script>alert('เปลี่ยนรหัสผ่านสำเร็จ'); window.location='profile.php';</script>";
            } else {
                $error = "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน";
            }
            $update_stmt->close();
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
    <title>เปลี่ยนรหัสผ่าน</title>
    <style>
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
        .navbar {
            display: flex;
            align-items: center;
            background-color: #333;
            justify-content: space-between;
            position: relative;
            margin: 0 auto;
        }
        .navbar-right {
            margin-right: 1.5rem;
            font-size: 2.5rem;
            color: #ffffff;
        }
        .navbar-right a {
            color: #ffffff;
            text-decoration: none;
        }
        .navbar-center a:hover {
            background-color: #007bff;
            color: #ffffff;
            transform: translateY(-3px);
        }
        .navbar-center {
            position: relative;
            font-size: 20px;
            padding: 25px 20px;
            letter-spacing: 0.10em;
            display: flex;
            gap: 20px;
        }
        .navbar-center a {
            font-family: 'Roboto', sans-serif;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .container { 
            max-width: 500px; 
            margin: 50px auto; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 10px; 
            background: #f9f9f9; 
        }
        .error { 
            color: red; 
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
        <h2 class="text-center">เปลี่ยนรหัสผ่าน</h2>
        <?php if ($error) echo "<p class='error text-center'>$error</p>"; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">เปลี่ยนรหัสผ่าน</button>
        </form>
        <a href="profile.php" class="btn btn-secondary w-100 mt-3">ย้อนกลับ</a>
    </div>
</body>
</html>
