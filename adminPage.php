<?php
    include 'connection.php'; 
    
    session_start(); 
    if (!isset($_SESSION['admin_id'])) {

        header("Location: login.php"); // Redirect to login if not admin
        exit();
    }

    // --- Get Total Users ---
    $sql_users = "SELECT COUNT(*) AS total_users FROM users WHERE user_type = 'user'"; // นับเฉพาะ user ทั่วไป
    $result_users = mysqli_query($conn, $sql_users);
    $user_count_data = mysqli_fetch_assoc($result_users);
    $total_users = $user_count_data['total_users'] ?? 0; // ใช้ ?? 0 เพื่อป้องกัน error ถ้า query ไม่สำเร็จ

    // --- Get Total Product Posts ---
    $sql_products = "SELECT COUNT(*) AS total_products FROM product";
    $result_products = mysqli_query($conn, $sql_products);
    $product_count_data = mysqli_fetch_assoc($result_products);
    $total_products = $product_count_data['total_products'] ?? 0; // ใช้ ?? 0 เพื่อป้องกัน error

    // Close the connection
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .dashboard-container {
        padding: 30px;
    }

    .dashboard-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-align: center;
        height: 100%;
        flex-direction: column;
        justify-content: center;
    }

    .dashboard-card i {
        font-size: 3em;
        margin-bottom: 15px;
        color: #007bff;
    }

    .dashboard-card h3 {
        font-size: 2.5em;
        margin-bottom: 5px;
    }

    .dashboard-card p {
        font-size: 1.1em;
        color: #6c757d;
        margin-bottom: 0;
    }
        /* --- Admin Navbar Styles (คัดลอกจาก adminPage.php) --- */
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
</style>
</head>
<body>

    <!-- Admin Navbar -->
    <div class="admin-navbar">
        <div class="navbar-brand">Admin Dashboard</div>
        <div class="navbar-nav">
            <a href="adminPage.php" class="nav-link active">Dashboard</a> <!-- ลิงก์หน้า Dashboard (Active) -->
            <a href="admin_users.php" class="nav-link">Users</a> <!-- ลิงก์ไปหน้าจัดการ Users -->
            <a href="admin_product.php" class="nav-link">Products</a> <!-- ลิงก์ไปหน้าจัดการ Products -->
            <a href="admin_review.php" class="nav-link">Reviews</a>
            <a href="logout.php" class="nav-link logout-link">ออกจากระบบ</a> <!-- ลิงก์ Logout -->
        </div>
    </div>

    <div class="container dashboard-container">
        <div class="row">
            <!-- User Count Card -->
            <div class="col"> <!-- ใช้ class="col" -->
                <div class="dashboard-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>จำนวนผู้ใช้ทั้งหมด</p>
                </div>
            </div>

            <!-- Product Post Count Card -->
            <div class="col"> <!-- ใช้ class="col" -->
                <div class="dashboard-card">
                    <i class="fas fa-box-open"></i>
                    <h3><?php echo $total_products; ?></h3>
                    <p>จำนวนโพสต์สินค้าทั้งหมด</p>
                </div>
            </div>

            <!-- Add more cards for other stats if needed -->
            <!-- ตัวอย่าง: ถ้าเพิ่มการ์ดอีกอัน ก็ใส่ใน <div class="col">...</div> -->
            <!--
            <div class="col">
                <div class="dashboard-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>...</h3>
                    <p>สถิติอื่นๆ</p>
                </div>
            </div>
             -->

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
