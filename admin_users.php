<?php
    session_start();
    require "connection.php"; // ใช้ require เพื่อให้แน่ใจว่าไฟล์ connection ถูกโหลด

    // --- ตรวจสอบการล็อกอินของ Admin ---
    if (!isset($_SESSION['admin_id'])) {
        header("location: Login.php");
        exit(); // หยุดการทำงานทันทีหลังจาก redirect
    }

    $admin_id = $_SESSION['admin_id']; // เก็บ admin_id ไว้เผื่อใช้งาน

    // --- ดึงข้อมูลผู้ใช้ที่เป็น 'user' ---
    $sql_users = "SELECT user_id, email, firstname, lastname, gender, tel, birth_date, address
                  FROM users
                  WHERE user_type = 'user'
                  ORDER BY user_id ASC"; // เรียงตาม ID หรือตามชื่อก็ได้ (เช่น firstname)

    $result_users = mysqli_query($conn, $sql_users);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - จัดการผู้ใช้งาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
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
        /* --- Table Styles --- */
        .table th {
            background-color: #e9ecef; /* สีพื้นหลังหัวตาราง */
        }
        .table td, .table th {
            vertical-align: middle; /* จัดเนื้อหาตรงกลางแนวตั้ง */
        }
        .action-buttons a, .action-buttons button {
            margin-right: 5px; /* ระยะห่างระหว่างปุ่ม */
        }
        .container {
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <div class="admin-navbar">
        <div class="navbar-brand">Admin Dashboard</div>
        <div class="navbar-nav">
            <a href="adminPage.php" class="nav-link">Dashboard</a>
            <a href="admin_users.php" class="nav-link active">Users</a> <!-- ทำให้ลิงก์ Users เป็น Active -->
            <a href="admin_product.php" class="nav-link">Products</a>
            <a href="admin_reviews.php" class="nav-link">Reviews</a>
            <a href="logout.php" class="nav-link logout-link">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4">จัดการผู้ใช้งาน</h2>

        <?php
            // แสดงข้อความแจ้งเตือน (ถ้ามี)
            if (isset($_GET['delete_success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ลบผู้ใช้เรียบร้อยแล้ว
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
            if (isset($_GET['edit_success'])) {
                 echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
        ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>เพศ</th>
                        <th>เบอร์โทร</th>
                        <th>วันเกิด</th>
                        <th>ที่อยู่</th>
                        <th style="width: 150px;">การดำเนินการ</th> <!-- กำหนดความกว้างให้คอลัมน์สุดท้าย -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_users && mysqli_num_rows($result_users) > 0) {
                        while ($user = mysqli_fetch_assoc($result_users)) {
                            echo "<tr>";
                            echo "<td>" . $user['user_id'] . "</td>";
                            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['firstname']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['lastname']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['gender']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['tel']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['birth_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['address']) . "</td>";
                            echo "<td class='action-buttons'>";
                            // ปุ่มแก้ไข (ลิงก์ไปยังหน้า admin_edit_user.php ที่ต้องสร้างเพิ่ม)
                            echo '<a href="admin_edit_user.php?user_id=' . $user['user_id'] . '" class="btn btn-warning btn-sm" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                  </a>';
                            // ปุ่มลบ (ลิงก์ไปยังหน้า admin_delete_user.php ที่ต้องสร้างเพิ่ม พร้อม confirmation)
                            echo '<a href="admin_delete_user.php?user_id=' . $user['user_id'] . '" class="btn btn-danger btn-sm" title="ลบ" onclick="return confirm(\'คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้ ' . htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) . '?\');">
                                    <i class="fas fa-trash-alt"></i>
                                  </a>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // กรณีไม่พบข้อมูลผู้ใช้
                        echo '<tr><td colspan="9" class="text-center">ไม่พบข้อมูลผู้ใช้งาน</td></tr>';
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
