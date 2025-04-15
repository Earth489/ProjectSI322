<?php
    session_start();
    require "connection.php"; // ใช้ require เพื่อให้แน่ใจว่าไฟล์ connection ถูกโหลด

    // --- ตรวจสอบการเชื่อมต่อฐานข้อมูล ---
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // --- ตรวจสอบการล็อกอินของ Admin ---
    if (!isset($_SESSION['admin_id'])) {
        header("location: Login.php");
        exit(); // หยุดการทำงานทันทีหลังจาก redirect
    }

    $admin_id = $_SESSION['admin_id']; // เก็บ admin_id ไว้เผื่อใช้งาน

    // --- ดึงข้อมูลสินค้าทั้งหมด พร้อมชื่อเจ้าของ ---
    // ไม่จำเป็นต้องดึง product_status แล้ว แต่ดึงมาก็ไม่เป็นไร
    $sql_products = "SELECT p.product_Id, p.product_Name, p.product_price, p.Product_exchanged,
                           p.product_category, /* p.product_status, */ p.Image, p.user_id,
                           u.firstname AS owner_firstname, u.lastname AS owner_lastname
                    FROM product p
                    JOIN users u ON p.user_id = u.user_id
                    ORDER BY p.product_Id ASC"; // เรียงตาม ID สินค้า

    $result_products = mysqli_query($conn, $sql_products);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        /* --- Admin Navbar Styles (เหมือนเดิม) --- */
        .admin-navbar {
            background-color: #343a40;
            padding: 15px 30px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-navbar {
            background-color: #343a40;
            padding: 15px 30px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-navbar .navbar-brand {
            color: white;
            font-size: 1.7em;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }
        .admin-navbar .navbar-nav {
            display: flex;
            flex-direction: row;
            gap: 18px;
            align-items: center;
        }
        .admin-navbar .nav-link {
            text-decoration: none;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .admin-navbar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }
        .admin-navbar .nav-link.active { /* สไตล์สำหรับลิงก์ที่ Active */
            color: white;
            font-weight: bold;
        }
        .admin-navbar .logout-link {
            color: #dc3545;
            font-weight: bold;
        }
        .admin-navbar .logout-link:hover {
            color: #f8d7da;
            text-decoration: none;
        }
        .table th { background-color: #e9ecef; }
        .table td, .table th { vertical-align: middle; }
        .action-buttons a, .action-buttons button { margin-right: 5px; }
        .container { padding-top: 20px; }
        .product-image-thumbnail {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        /* ปรับความกว้างคอลัมน์ดำเนินการให้เล็กลง */
        .action-buttons {
             width: 80px; /* หรือค่าที่เหมาะสม */
             text-align: center; /* จัดปุ่มให้อยู่กลาง (ถ้าต้องการ) */
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <div class="admin-navbar">
        <div class="navbar-brand">Admin Dashboard</div>
        <div class="navbar-nav">
            <a href="adminPage.php" class="nav-link">Dashboard</a>
            <a href="admin_users.php" class="nav-link">Users</a>
            <a href="admin_product.php" class="nav-link active">Products</a>
            <a href="admin_reviews.php" class="nav-link">Reviews</a>
            <a href="logout.php" class="nav-link logout-link">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4">จัดการสินค้า</h2>

        <?php
            // แสดงข้อความแจ้งเตือน (ถ้ามี) - ส่วนนี้ยังคงเดิม
            if (isset($_GET['delete_success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ลบสินค้าเรียบร้อยแล้ว
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
            // ไม่จำเป็นต้องมี edit_success แล้ว
            // if (isset($_GET['edit_success'])) { ... }
             if (isset($_GET['error'])) {
                 $errorMsg = 'เกิดข้อผิดพลาดบางอย่าง';
                 if ($_GET['error'] === 'delete_failed') $errorMsg = 'เกิดข้อผิดพลาดในการลบสินค้า';
                 if ($_GET['error'] === 'delete_not_found') $errorMsg = 'ไม่พบสินค้าที่ต้องการลบ';
                 echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($errorMsg) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
        ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อสินค้า</th>
                        <th>เจ้าของ</th>
                        <th>ราคา (บาท)</th>
                        <th>ต้องการแลกกับ</th>
                        <th>ประเภท</th>
                        <!-- <th>สถานะ</th> ลบหัวข้อสถานะออก -->
                        <th class="action-buttons">การดำเนินการ</th> <!-- ปรับความกว้างผ่าน class -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_products && mysqli_num_rows($result_products) > 0) {
                        while ($product = mysqli_fetch_assoc($result_products)) {
                            echo "<tr>";
                            echo "<td>" . $product['product_Id'] . "</td>";
                            echo "<td>";
                            if (!empty($product['Image']) && file_exists("uploads/" . $product['Image'])) {
                                echo '<img src="uploads/' . htmlspecialchars($product['Image']) . '" alt="' . htmlspecialchars($product['product_Name']) . '" class="product-image-thumbnail">';
                            } else {
                                echo '<i class="fas fa-image text-muted" title="ไม่มีรูปภาพ"></i>';
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($product['product_Name']) . "</td>";
                            echo "<td>" . htmlspecialchars($product['owner_firstname'] . ' ' . $product['owner_lastname']) . " (ID: " . $product['user_id'] . ")</td>";
                            echo "<td>" . htmlspecialchars(number_format($product['product_price'], 2)) . "</td>";
                            echo "<td>" . htmlspecialchars($product['Product_exchanged']) . "</td>";
                            echo "<td>" . htmlspecialchars($product['product_category']) . "</td>";

                            // --- ลบส่วนแสดงสถานะออก ---
                            // echo "<td>"; ... echo "</td>";

                            echo "<td class='action-buttons'>";
                            // --- ลบปุ่มแก้ไขออก ---
                            // echo '<a href="admin_edit_product.php?product_id=' . $product['product_Id'] . '" ... >...</a>';

                            // ปุ่มลบ (ยังคงอยู่)
                            echo '<a href="admin_delete_product.php?product_id=' . $product['product_Id'] . '" class="btn btn-danger btn-sm" title="ลบ" onclick="return confirm(\'คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า ' . htmlspecialchars($product['product_Name']) . '?\');">
                                    <i class="fas fa-trash-alt"></i>
                                  </a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                        mysqli_free_result($result_products);
                    } else {
                        // กรณีไม่พบข้อมูลสินค้า (ปรับ colspan เป็น 8)
                        echo '<tr><td colspan="8" class="text-center">ไม่พบข้อมูลสินค้า</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
        // ปิดการเชื่อมต่อฐานข้อมูล
        mysqli_close($conn);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
