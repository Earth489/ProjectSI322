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
    <title>สร้างโพสต์สินค้า</title>
    <link rel="stylesheet" href="style.css">
</head>
<script src="./js/scriptppp.js"></script>

<body>

<<div class="navbar">
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
        <div class="signup-box">
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                
                <main>
                <div class="image-upload">
                          <label for="file-input">
                           <div class="placeholder"></div>
                           </label>
                     <input type="file" id="file-input" name="product-image" accept="image/*" style="display: none;">
                       <img id="preview-image" src="" alt="ตัวอย่างรูปภาพ" style="display: none; width: 200px; margin-top: 10px;">
                 <p>ใส่รูปที่ต้องการโพสต์สินค้า</p>
            </div>

                    <div class="form-inputs">
                        <div class="input-group">
                            <label for="product-name">ชื่อสินค้า</label>
                            <input type="text" id="product-name" name="product-name" required>
                        </div>
                        <div class="input-group">
                            <label for="product-description">รายละเอียดสินค้า</label>
                            <textarea id="product-description" name="product-description" required></textarea>
                        </div>
                        <div class="input-group">
                            <label for="product-price">ราคาสินค้า (ถ้ามี)</label>
                            <input type="number" step="0.01" id="product-price" name="product-price" required>
                        </div>
                        <div class="input-group">
                            <label for="product-category">ประเภทสินค้า</label>
                            <select id="product-category" name="product-category" required>
                                <option value=""> เลือกประเภทสินค้า </option>
                                <option value="อุปกรณ์อิเล็กทรอนิกส์">อุปกรณ์อิเล็กทรอนิกส์</option>
                                <option value="เครื่องใช้ในบ้าน">เครื่องใช้ในบ้าน</option>
                                <option value="เสื้อผ้าและแฟชั่น">เสื้อผ้าและแฟชั่น</option>
                                <option value="หนังสือและเครื่องเขียน">หนังสือและเครื่องเขียน</option>
                                <option value="ของเล่นและเกม">ของเล่นและเกม</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="exchange-item">ประเภทสินค้าที่ต้องการแลกเปลี่ยน</label>
                            <select id="exchange-item" name="exchange-item" required>
                                <option value=""> เลือกประเภทสินค้าที่ต้องการแลก </option>
                                <option value="อุปกรณ์อิเล็กทรอนิกส์">อุปกรณ์อิเล็กทรอนิกส์</option>
                                <option value="เครื่องใช้ในบ้าน">เครื่องใช้ในบ้าน</option>
                                <option value="เสื้อผ้าและแฟชั่น">เสื้อผ้าและแฟชั่น</option>
                                <option value="หนังสือและเครื่องเขียน">หนังสือและเครื่องเขียน</option>
                                <option value="ของเล่นและเกม">ของเล่นและเกม</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="product_status" value="ต้องการแลก">

                    <div class="buttons">
                        <button type="submit" class="confirm">ยืนยัน</button>
                        <a href="main_product_post.php"><button type="button" class="cancel">ยกเลิก</button></a>
                    </div>
                </main>

            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
