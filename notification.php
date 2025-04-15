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
if (!$stmt) { // เพิ่มการตรวจสอบ prepare statement
    die("Prepare failed (main query): (" . $conn->errno . ") " . $conn->error);
}
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
    <link rel="stylesheet" href="style.css">
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
        .interested-product-image {
            width: 150px; /* ปรับขนาดความกว้างตามต้องการ */
            height: auto; /* ให้ความสูงปรับตามสัดส่วน */

            margin: 0 auto 10px;
            object-fit: cover; /* ครอบตัดรูปภาพให้พอดีกับขนาดที่กำหนด */
            object-position: center; /* จัดตำแหน่งรูปภาพให้อยู่ตรงกลาง */
        }
        .cancel-button {
            background-color: #f44336; /* Red */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 5px;
            width: 100px; /* หรือขนาดที่ต้องการ */
        }
        .cancel-button:hover {
            opacity: 0.8;
        }
        /* CSS ใหม่สำหรับจัดตำแหน่งปุ่ม */
        .button-container {
            align-items: center; /* จัดให้ปุ่มอยู่ตรงกลางแนวตั้ง */
            margin-top: 10px; /* เพิ่มระยะห่างด้านบน */
            margin-bottom: 20px; /* เพิ่มระยะห่างด้านล่าง */;
        }

        .match-button {
            background-color: #007bff; /* Blue */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: 100px;
            margin-right: 10px;
        }

        .match-button:hover {
            opacity: 0.8;
        }
    </style>
    <script>
        function cancelInterest(interestedId) {
            if (confirm("คุณต้องการยกเลิกการสนใจนี้หรือไม่?")) {
                fetch('cancel_interested.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'interested_id=' + interestedId,
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('ยกเลิกการสนใจสำเร็จ');
                        location.reload(); // รีโหลดหน้าเพื่ออัปเดตข้อมูล
                    } else {
                        alert('เกิดข้อผิดพลาดในการยกเลิกการสนใจ');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
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
        <a href="myproduct.php">สินค้าของฉัน</a>
        <a href="notification.php">แจ้งเตือน</a>
        <a href="matchslist.php">รายการจับคู่</a>
        <a href="history.php">ดูประวัติการแลกเปลี่ยน</a>
    </div>
    <div class="navbar-right">
        <a href="profile.php"><span><i class="fa-regular fa-user"></i></span></a>
    </div>
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
                        <img src="uploads/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_Name']); ?>" class="interested-product-image">
                        <p><strong>สินค้าของคุณ:</strong> <?php echo htmlspecialchars($row['product_Name']); ?></p>
                        <p><strong>ผู้สนใจ:</strong> <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></p>
                        <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($row['tel']); ?></p>
                        <p><strong>วันที่สนใจ:</strong> <?php echo date("d/m/Y H:i", strtotime($row['interested_date'])); ?></p>

                        <!-- แสดงรายการสินค้าของผู้สนใจ -->
                        <div class="product-list">
                            <p><strong>สินค้าของผู้สนใจ:</strong></p>
                            <?php
                                // ดึงข้อมูลสินค้าของผู้สนใจ
                                $interested_user_id = $row['user_id'];
                                // แก้ไขคำสั่ง SQL เพื่อดึงข้อมูลเพิ่มเติม
                                $product_sql = "SELECT product_Id, product_Name, product_detail, Image, product_price, Product_exchanged, product_category FROM product WHERE user_id = ?";
                                $product_stmt = $conn->prepare($product_sql);
                                // เพิ่มการตรวจสอบ prepare statement
                                if (!$product_stmt) {
                                    // จัดการข้อผิดพลาด เช่น แสดงข้อความ หรือ log ไว้
                                    // error_log("Prepare failed (interested user products): (" . $conn->errno . ") " . $conn->error);
                                    echo "<p>เกิดข้อผิดพลาดในการดึงข้อมูลสินค้าของผู้สนใจ</p>";
                                    continue; // ข้ามไปรายการแจ้งเตือนถัดไป
                                }
                                $product_stmt->bind_param("i", $interested_user_id);
                                $product_stmt->execute();
                                $product_result = $product_stmt->get_result();

                                if ($product_result->num_rows > 0) {
                                    echo "<ul>";
                                    $has_product = true; // เพิ่มตัวแปรตรวจสอบว่ามีสินค้าหรือไม่
                                    while ($product_row = $product_result->fetch_assoc()) {
                                        // --- โค้ดที่แก้ไข ---
                                        // ตรวจสอบว่าสินค้าถูกจับคู่ในสถานะ active หรือ completed หรือไม่
                                        $match_sql = "SELECT matchs_id FROM matchs WHERE (product_owner_product_id = ? OR interested_user_product_id = ?) AND (status = 'active' OR status = 'completed')";
                                        $match_stmt = $conn->prepare($match_sql);
                                        // เพิ่มการตรวจสอบ prepare statement เพื่อความปลอดภัย
                                        if (!$match_stmt) {
                                            // จัดการข้อผิดพลาด เช่น แสดงข้อความ หรือ log ไว้
                                            // error_log("Prepare failed (match check): (" . $conn->errno . ") " . $conn->error);
                                            continue; // ข้ามไปสินค้าถัดไปถ้า prepare ล้มเหลว
                                        }
                                        $match_stmt->bind_param("ii", $product_row['product_Id'], $product_row['product_Id']);
                                        $match_stmt->execute();
                                        $match_result = $match_stmt->get_result();

                                        // ถ้าไม่พบรายการจับคู่ที่ active หรือ completed ให้แสดงสินค้านี้
                                        if ($match_result->num_rows == 0) {
                                            echo "<li>";
                                            echo "<img src='uploads/".htmlspecialchars($product_row['Image'])."' alt='".htmlspecialchars($product_row['product_Name'])."'>";
                                            // ตรวจสอบว่า $has_product เป็น true ก่อนแสดง radio button (อาจจะไม่จำเป็นแล้ว)
                                            // if ($has_product) {
                                                echo "<p><input type='radio' name='selected_product_id[".$row['interested_id']."]' value='".$product_row['product_Id']."'> ".htmlspecialchars($product_row['product_Name'])."</p>";
                                            // }
                                            // เพิ่มข้อมูล product_detail, product_price, Product_exchanged, product_category
                                            echo "<p><strong>รายละเอียด:</strong> ".htmlspecialchars($product_row['product_detail'])."</p>";
                                            echo "<p><strong>ราคา:</strong> ".htmlspecialchars($product_row['product_price'])." บาท</p>";
                                            echo "<p><strong>สินค้าแลกเปลี่ยน:</strong> ".htmlspecialchars($product_row['Product_exchanged'])."</p>";
                                            echo "<p><strong>ประเภทสินค้า:</strong> ".htmlspecialchars($product_row['product_category'])."</p>";
                                            echo "</li>";
                                        }
                                        // ปิด statement ที่ใช้เสร็จแล้ว
                                        $match_stmt->close();
                                        // --- จบโค้ดที่แก้ไข ---
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "<p>ไม่มีสินค้า</p>";
                                    $has_product = false; // กำหนดค่าเป็น false เมื่อไม่มีสินค้า
                                }
                                // ปิด statement ที่ใช้เสร็จแล้ว
                                $product_stmt->close();
                            ?>
                        </div>
                        <div class="button-container">
                            <?php if ($has_product): // แสดงปุ่มจับคู่เฉพาะเมื่อผู้สนใจมีสินค้าให้เลือก ?>
                                <button type="submit" class="match-button">จับคู่</button>
                            <?php endif; ?>
                            <input type="hidden" name="product_owner_id" value="<?php echo $user_id; ?>">
                            <input type="hidden" name="product_owner_product_id" value="<?php echo $row['product_id']; ?>">
                            <input type="hidden" name="interested_user_id" value="<?php echo $row['user_id']; ?>">
                            <button type="button" class="cancel-button" onclick="cancelInterest(<?php echo $row['interested_id']; ?>)">ยกเลิก</button>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
            </form>
        <?php else: ?>
            <p>ไม่มีการแจ้งเตือน</p>
        <?php endif; ?>
        <?php $stmt->close(); // ปิด statement หลัก ?>
    </main>
</body>
</html>
