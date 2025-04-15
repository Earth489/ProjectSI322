<?php 

    require 'connection.php';
    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="login.css">
    <title>เข้าสู่ระบบ</title>
</head>
<body>
        <form action="Login.php" method="POST">
        <?php 
        
            if(isset($_POST['enter'])) {

                $email = $_POST['email'];
                $password = $_POST['pws'];

                $sql_user = "SELECT * FROM users WHERE email = '$email'";
                $result_users = mysqli_query($conn, $sql_user);
                

                $email = mysqli_fetch_array($result_users, MYSQLI_ASSOC);

                if($email) {

                    if($password === $email['password']) {

                        if($email['user_type'] === 'user') {

                            $_SESSION['user_email'] = $email["email"];
                            $_SESSION['user_id'] = $email["user_id"];
                            header("Location: main_product_post.php");
                            exit();
                        } elseif ($email['user_type'] === 'admin') {

                            $_SESSION['admin_email'] = $email['email'];
                            $_SESSION['admin_id'] = $email['user_id'];
                            header("Location: adminPage.php");
                            exit();

                        }
                    } else {

                        echo "<div class='alert alert-danger'>กรุณากรอกรหัสผ่านที่ถูกต้อง</div>";
                    }

                } else {

                    echo "<div class='alert alert-danger'>ไม่พบอีเมลผู้ใช้</div>";
                        
                }
            }
        ?>

        <h1 class="mb-4">เข้าสู่ระบบ</h1>
        <div class="mb-3 text-start">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" placeholder="E-mail" class="form-control">
        </div>
        <div class="mb-3 text-start">
            <label for="pws" class="form-label">รหัสผ่าน</label>
            <input type="password" name="pws" id="pws" placeholder="Password" class="form-control">
        </div>
        
        <button type="submit" name="enter" class="btn-login">เข้าสู่ระบบ</button>
        <div class="divider"></div>
        <a href="Register.php">สมัครสมาชิก</a>
    </form>
</body>
</html>