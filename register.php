<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="container">
    <?php 

        require 'connection.php';

        if(isset($_POST['reg'])){

            $email = $_POST['email'];
            $password = $_POST['pws'];
            $password_con = $_POST['con_pws'];
            $first_name = $_POST['firstname'];
            $last_name = $_POST['lastname'];
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $tel = $_POST['tel'];
            $birth_date = $_POST['birth_date'];
            $address = $_POST['address'];
            $community = $_POST['community'];
       
            $errors = array();

            if(empty($email) OR empty($password) OR empty($password_con) OR empty($first_name) OR empty($last_name) OR empty($gender) OR empty($tel) OR empty($birth_date) OR empty($address)OR empty($community)) {

                array_push($errors, "กรุณากรอกข้อมูลให้ครบ");
       
        }
        if (empty($email)) {
            array_push($errors, "อีเมลนี้ถูกใช้งานแล้ว!");
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "อีเมลไม่ถูกต้อง");
        }
        if(empty($gender)) {

            array_push($errors, "กรุณาเลือกเพศ");

        }

        if(strlen($password) < 8) {

            array_push($errors, "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร");

        }

        if(empty($first_name)) {

            array_push($errors, "กรุณากรอกชื่อ");

        }

        if(empty($last_name)) {

            array_push($errors, "กรุณากรอกนามสกุล");

        }

        if($password !== $password_con) {

            array_push($errors, "รหัสผ่านไม่ตรงกัน");

        }

        if(strlen($tel) <= 0 AND strlen($tel) >= 10) {

            array_push($errors, "หมายเลขโทรศัพท์ไม่ถูกต้อง");

        }
        if(empty($birth_date)) {

            array_push($errors, "กรุณากรอกวันเกิด");
        }
        if(empty($address)) {

            array_push($errors, "กรุณากรอกที่อยู่");
        }
        if(empty($community)) {

            array_push($errors, "กรุณากรอกชุมชน");
        }
        $sql_users = "SELECT * FROM users WHERE email = '$email'";

        $result_users = mysqli_query($conn, $sql_users);
        
        $rowCount = mysqli_num_rows($result_users);
        
        if($rowCount > 0) {
            array_push($errors, "อีเมลนี้ถูกใช้งานแล้ว!");
        }
        
        if(count($errors) > 0) {
            foreach ($errors as $error) {
                echo "
                    <div class='box-alert'>
                        <div class='alert alert-danger'>$error</div>
                    </div>";
            }
        } else {
            $sql_insert_user = "INSERT INTO users(email, password, firstname, lastname, gender, tel, birth_date, address, community) 
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if(mysqli_stmt_prepare($stmt, $sql_insert_user)) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $email, $password, $first_name, $last_name, $gender, $tel, $birth_date, $address, $community);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>คุณได้ลงทะเบียนเรียบร้อยแล้ว!</div>";
            } else {
                echo "<div class='alert alert-danger'>เกิดข้อผิดพลาดบางประการ!</div>";
            }

        }
    }   
        
    ?>

        <form action="Register.php" method="POST">

        <h1>สมัครสมาชิก</h1>
        
        <div class="form-group">
            <p class="email">E-mail</p>
            <input type="email" name="email" id="email"  placeholder="email" class="form-control">
        </div>

        <div class="form-group">
            <p class="password">รหัสผ่าน</p>
            <input type="password" name="pws" id="pws"  placeholder="passoword" class="form-control">
        </div>

        <div class="form-group">
            <p class="confirmpassword">ยืนยันรหัสผ่าน</p>
            <input type="password" name="con_pws" id="con_pws"  placeholder="confirm password" class="form-control">
        </div>

    <div class="row">

        <div class="col-md-6">
            <p class="firstname">ชื่อ</p>
            <input type="text" name="firstname" id="firstname" placeholder="ชื่อ" class="form-control">
        </div>

        <div class="col-md-6">
            <p class="lastname">นามสกุล</p>
            <input type="text" name="lastname" id="lastname" placeholder="นามสกุล" class="form-control">
        </div>

    </div>

    <div class="radio-box mb-3">

        <p class="gender">เพศ</p>

        <div class="d-flex gap-3">

        <div class="form-check">
        <input type="radio" name="gender" id="flexRadioDefault1" value="ชาย" class="form-check-input">
        <label for="flexRadioDefault1" class="form-check-label">ชาย</label>      
        </div>

        <div class="form-check">
            <input type="radio" name="gender" id="flexRadioDefault2" value="หญิง" class="form-check-input">
            <label for="flexRadioDefault2" class="form-check-label">หญิง</label>
        </div>

        <div class="form-check">
            <input type="radio" name="gender" id="flexRadioDefault3" value="อื่นๆ" class="form-check-input">
            <label for="flexRadioDefault3" class="form-check-label">อื่นๆ</label>
        </div>
        </div>
    </div>
        <div class="row tel-birth-row">
        <div class="col-md-6">
            <p class="tel">เบอร์โทรศัพท์</p>
            <input type="text" name="tel" placeholder="xxx-xxx-xxxx" class="form-control">
        </div>

        <div class="col-md-6">
            <p class="birthdate">วัน/เดือน/ปีเกิด</p>
            <input type="date"  name="birth_date" placeholder="dd/mm/yyyy"  class="form-control">
        </div>
        </div>

        <div class="row mb-3">
        <div class="col-md-6">
            <label for="address" class="form-label">ที่อยู่บ้าน</label>
            <input type="text" name="address" id="address" placeholder="กรอกที่อยู่" class="form-control">
        </div>

        <div class="col-md-6">
            <label for="community" class="form-label">ชุมชนที่อยู่</label>
            <input type="text" name="community" id="community" placeholder="กรอกชุมชนที่อยู่" class="form-control">
        </div>

        <input type="submit" value="ยืนยัน" name="reg" >
        <p class="register-info">มีบัญชีอยู่แล้ว <span><a href="Login.php">ไปยังหน้าเข้าสู่ระบบ</a></span></p>

        </div>

        </div>

    </div>
    </form>
</body>
</html>