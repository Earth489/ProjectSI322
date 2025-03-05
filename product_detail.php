<?php 
    include 'connection.php';
    session_start();

    // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


if (!isset($_GET['product_Id'])) {
    echo "ไม่พบสินค้า";
    exit;
}

$product_Id = intval($_GET['product_Id']); // ป้องกัน SQL Injection

$sql = "SELECT * FROM product WHERE product_Id = $product_Id"; // แก้ id เป็น product_Id
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "ไม่พบสินค้า";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title><?php echo $row["product_Name"]; ?></title>
    <link rel="stylesheet" href="dt.css">
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

    <main class="product-detail">
    <div class="product-container">
    
            <img src="uploads/<?php echo $row["Image"]; ?>" alt="<?php echo $row["product_Name"]; ?>">
            <h1><?php echo $row["product_Name"]; ?></h1>
            <p><?php echo $row["product_detail"]; ?></p>
            <p><strong>ราคา:</strong> <span class="price"><?php echo $row["product_price"]; ?></span> บาท</p>
            <p><strong>สินค้าแลกเปลี่ยน:</strong> <span class="exchange"><?php echo $row["Product_exchanged"]; ?></span></p>

        </div>
    
</main>
<a href="main_product_post.php" class="btn_back">ย้อนกลับ</a>

</body>
</html>
