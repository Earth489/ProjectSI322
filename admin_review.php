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

    // --- ดึงข้อมูลประวัติการแลกเปลี่ยนที่เสร็จสมบูรณ์ พร้อมข้อมูลสินค้า, ผู้ใช้, และรีวิว ---
    $sql_history = "SELECT
                        h.history_id, h.exchange_date,
                        h.product_owner_id, h.interested_user_id,
                        po.firstname AS owner_firstname, po.lastname AS owner_lastname,
                        iu.firstname AS interested_firstname, iu.lastname AS interested_lastname,
                        pop.product_Name AS owner_product_name, pop.Image AS owner_product_image,
                        iup.product_Name AS interested_product_name, iup.Image AS interested_product_image,
                        -- รีวิวจากเจ้าของสินค้า ถึง ผู้สนใจ
                        r_owner.comment AS owner_review_comment,
                        -- รีวิวจากผู้สนใจ ถึง เจ้าของสินค้า
                        r_interested.comment AS interested_review_comment
                    FROM history h
                    JOIN users po ON h.product_owner_id = po.user_id
                    JOIN users iu ON h.interested_user_id = iu.user_id
                    JOIN product pop ON h.product_owner_product_id = pop.product_Id
                    JOIN product iup ON h.interested_user_product_id = iup.product_Id
                    -- LEFT JOIN เพื่อดึงรีวิวที่เจ้าของเขียนถึงผู้สนใจ (ถ้ามี)
                    LEFT JOIN reviews r_owner ON h.history_id = r_owner.history_id
                                            AND r_owner.reviewer_id = h.product_owner_id
                                            AND r_owner.reviewed_user_id = h.interested_user_id
                    -- LEFT JOIN เพื่อดึงรีวิวที่ผู้สนใจเขียนถึงเจ้าของ (ถ้ามี)
                    LEFT JOIN reviews r_interested ON h.history_id = r_interested.history_id
                                                AND r_interested.reviewer_id = h.interested_user_id
                                                AND r_interested.reviewed_user_id = h.product_owner_id
                    ORDER BY h.exchange_date DESC"; // เรียงตามวันที่แลกเปลี่ยนล่าสุด

    $result_history = mysqli_query($conn, $sql_history);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - จัดการรีวิว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        /* --- Admin Navbar Styles (เหมือนเดิม) --- */
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
        /* --- Review Item Styles --- */
        .review-item {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .exchange-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .product-box {
            text-align: center;
            width: 40%; /* ปรับความกว้างตามต้องการ */
        }
        .product-box img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin-bottom: 0.5rem;
        }
        .product-box p {
            margin-bottom: 0.25rem;
            font-size: 0.9em;
        }
        .exchange-icon {
            font-size: 1.5em;
            color: #6c757d;
        }
        .review-details h6 {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #495057;
        }
        .review-comment {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.95em;
            margin-bottom: 1rem;
        }
        .no-review {
            color: #6c757d;
            font-style: italic;
            font-size: 0.9em;
        }
        .exchange-date {
            font-size: 0.85em;
            color: #6c757d;
            text-align: right;
            margin-bottom: 1rem;
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
            <a href="admin_product.php" class="nav-link">Products</a>
            <a href="admin_reviews.php" class="nav-link active">Reviews</a> <!-- ทำให้ลิงก์ Reviews เป็น Active -->
            <a href="logout.php" class="nav-link logout-link">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4">ประวัติการแลกเปลี่ยนและรีวิว</h2>

        <?php
        if ($result_history && mysqli_num_rows($result_history) > 0) {
            while ($item = mysqli_fetch_assoc($result_history)) {
        ?>
                <div class="review-item">
                    <p class="exchange-date">แลกเปลี่ยนเมื่อ: <?php echo date("d/m/Y H:i", strtotime($item['exchange_date'])); ?></p>
                    <div class="exchange-info">
                        <!-- สินค้าของเจ้าของ -->
                        <div class="product-box">
                            <?php if (!empty($item['owner_product_image']) && file_exists("uploads/" . $item['owner_product_image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($item['owner_product_image']); ?>" alt="<?php echo htmlspecialchars($item['owner_product_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-image fa-3x text-muted mb-2"></i><br>
                            <?php endif; ?>
                            <p><strong><?php echo htmlspecialchars($item['owner_product_name']); ?></strong></p>
                            <p>เจ้าของ: <?php echo htmlspecialchars($item['owner_firstname'] . ' ' . $item['owner_lastname']); ?></p>
                            <p>(ID: <?php echo $item['product_owner_id']; ?>)</p>
                        </div>

                        <div class="exchange-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>

                        <!-- สินค้าของผู้สนใจ -->
                        <div class="product-box">
                             <?php if (!empty($item['interested_product_image']) && file_exists("uploads/" . $item['interested_product_image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($item['interested_product_image']); ?>" alt="<?php echo htmlspecialchars($item['interested_product_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-image fa-3x text-muted mb-2"></i><br>
                            <?php endif; ?>
                            <p><strong><?php echo htmlspecialchars($item['interested_product_name']); ?></strong></p>
                            <p>ผู้สนใจ: <?php echo htmlspecialchars($item['interested_firstname'] . ' ' . $item['interested_lastname']); ?></p>
                             <p>(ID: <?php echo $item['interested_user_id']; ?>)</p>
                        </div>
                    </div>

                    <div class="review-details">
                        <!-- รีวิวจากเจ้าของ ถึง ผู้สนใจ -->
                        <h6>รีวิวจาก <?php echo htmlspecialchars($item['owner_firstname']); ?> ถึง <?php echo htmlspecialchars($item['interested_firstname']); ?>:</h6>
                        <?php if (!empty($item['owner_review_comment'])): ?>
                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($item['owner_review_comment'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="no-review">ยังไม่มีรีวิว</p>
                        <?php endif; ?>

                        <!-- รีวิวจากผู้สนใจ ถึง เจ้าของ -->
                        <h6>รีวิวจาก <?php echo htmlspecialchars($item['interested_firstname']); ?> ถึง <?php echo htmlspecialchars($item['owner_firstname']); ?>:</h6>
                         <?php if (!empty($item['interested_review_comment'])): ?>
                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($item['interested_review_comment'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="no-review">ยังไม่มีรีวิว</p>
                        <?php endif; ?>
                    </div>
                </div><!-- /.review-item -->
        <?php
            } // end while
            mysqli_free_result($result_history); // คืนค่าหน่วยความจำ
        } else {
            // กรณีไม่พบข้อมูลประวัติหรือรีวิว
            echo '<div class="alert alert-info text-center">ไม่พบข้อมูลประวัติการแลกเปลี่ยนหรือรีวิว</div>';
        }
        ?>
    </div><!-- /.container -->

    <?php
        // ปิดการเชื่อมต่อฐานข้อมูล
        mysqli_close($conn);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
