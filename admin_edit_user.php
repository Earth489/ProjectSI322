<?php
    session_start();
    require "connection.php";

    // --- ตรวจสอบการล็อกอินของ Admin ---
    if (!isset($_SESSION['admin_id'])) {
        header("location: Login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];
    $user_id_to_edit = null;
    $user_data = null;
    $error_message = '';
    $success_message = '';

    // --- รับ user_id จาก GET parameter ---
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $user_id_to_edit = intval($_GET['user_id']);

        // --- ดึงข้อมูลผู้ใช้ปัจจุบันเพื่อแสดงในฟอร์ม ---
        $sql_select = "SELECT email, firstname, lastname, gender, tel, birth_date, address
                       FROM users
                       WHERE user_id = ? AND user_type = 'user'"; // ดึงเฉพาะ user
        $stmt_select = mysqli_prepare($conn, $sql_select);
        mysqli_stmt_bind_param($stmt_select, "i", $user_id_to_edit);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);

        if ($result_select && mysqli_num_rows($result_select) > 0) {
            $user_data = mysqli_fetch_assoc($result_select);
        } else {
            // ไม่พบผู้ใช้ หรือผู้ใช้ไม่ใช่ user type 'user'
            header("location: admin_users.php?error=not_found"); // Redirect กลับพร้อม error
            exit();
        }
        mysqli_stmt_close($stmt_select);

    } else {
        // ถ้าไม่มี user_id หรือไม่ใช่ตัวเลข
        header("location: admin_users.php");
        exit();
    }

    // --- จัดการการส่งข้อมูลฟอร์ม (POST Request) ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // ตรวจสอบว่า user_id ที่ส่งมาตรงกับที่กำลังแก้ไข
        if (isset($_POST['user_id']) && intval($_POST['user_id']) === $user_id_to_edit) {

            // รับข้อมูลจากฟอร์มและ Sanitize
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $email = trim($_POST['email']);
            $gender = $_POST['gender'];
            $tel = trim($_POST['tel']);
            $birth_date = $_POST['birth_date'];
            $address = trim($_POST['address']);

            // --- การตรวจสอบข้อมูล (Validation) ---
            if (empty($firstname) || empty($lastname) || empty($email) || empty($gender) || empty($tel) || empty($birth_date) || empty($address)) {
                $error_message = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "รูปแบบอีเมลไม่ถูกต้อง";
            } else {
                // --- ตรวจสอบว่า Email ซ้ำกับคนอื่นหรือไม่ (ยกเว้นตัวเอง) ---
                $sql_check_email = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
                $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
                mysqli_stmt_bind_param($stmt_check_email, "si", $email, $user_id_to_edit);
                mysqli_stmt_execute($stmt_check_email);
                $result_check_email = mysqli_stmt_get_result($stmt_check_email);

                if (mysqli_num_rows($result_check_email) > 0) {
                    $error_message = "อีเมลนี้ถูกใช้งานโดยผู้ใช้อื่นแล้ว";
                } else {
                    // --- อัปเดตข้อมูลลงฐานข้อมูล ---
                    $sql_update = "UPDATE users SET
                                    firstname = ?,
                                    lastname = ?,
                                    email = ?,
                                    gender = ?,
                                    tel = ?,
                                    birth_date = ?,
                                    address = ?
                                   WHERE user_id = ? AND user_type = 'user'"; // อัปเดตเฉพาะ user

                    $stmt_update = mysqli_prepare($conn, $sql_update);
                    mysqli_stmt_bind_param($stmt_update, "sssssssi",
                        $firstname,
                        $lastname,
                        $email,
                        $gender,
                        $tel,
                        $birth_date,
                        $address,
                        $user_id_to_edit
                    );

                    if (mysqli_stmt_execute($stmt_update)) {
                        // อัปเดตสำเร็จ, redirect กลับไปหน้า admin_users พร้อม success message
                        header("Location: admin_users.php?edit_success=1");
                        exit();
                    } else {
                        $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt_update);
                }
                mysqli_stmt_close($stmt_check_email);
            }
            // หากมี error หรือไม่ได้ redirect, ให้โหลดข้อมูลที่ผู้ใช้กรอกล่าสุดใส่ $user_data เพื่อแสดงในฟอร์ม
             if ($error_message) {
                 $user_data = $_POST; // ใช้ข้อมูลจาก POST ถ้ามี error
             }

        } else {
             $error_message = "ข้อมูลที่ส่งมาไม่ถูกต้อง";
        }
    }

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - แก้ไขข้อมูลผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 700px; margin-top: 30px; }
        .card { padding: 30px; }
        /* --- Admin Navbar Styles (เหมือนเดิม) --- */
        .admin-navbar { background-color: #343a40; padding: 15px 30px; color: white; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); position: sticky; top: 0; z-index: 1000; }
        .admin-navbar .navbar-brand { color: white; font-size: 1.7em; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .admin-navbar .navbar-nav { display: flex; flex-direction: row; gap: 18px; align-items: center; }
        .admin-navbar .nav-link { color: rgba(255, 255, 255, 0.7); text-decoration: none; font-weight: 500; padding: 8px 14px; border-radius: 8px; transition: all 0.3s ease; }
        .admin-navbar .nav-link:hover { color: white; background-color: rgba(255, 255, 255, 0.1); text-decoration: none; }
        .admin-navbar .nav-link.active { color: white; font-weight: bold; background-color: rgba(255, 255, 255, 0.2); }
        .admin-navbar .logout-link { color: #dc3545; font-weight: bold; }
        .admin-navbar .logout-link:hover { color: #f8d7da; background-color: rgba(220, 53, 69, 0.1); text-decoration: none; }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <div class="admin-navbar">
        <div class="navbar-brand">Admin Dashboard</div>
        <div class="navbar-nav">
            <a href="adminPage.php" class="nav-link">Dashboard</a>
            <a href="admin_users.php" class="nav-link active">Users</a>
            <a href="admin_product.php" class="nav-link">Products</a>
            <a href="logout.php" class="nav-link logout-link">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <div class="card shadow-sm">
            <h2 class="mb-4 text-center">แก้ไขข้อมูลผู้ใช้ ID: <?php echo $user_id_to_edit; ?></h2>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if ($success_message): // อาจจะไม่จำเป็นถ้า redirect ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($user_data): ?>
            <form method="POST" action="admin_edit_user.php?user_id=<?php echo $user_id_to_edit; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id_to_edit; ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user_data['firstname']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user_data['lastname']); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gender" class="form-label">เพศ</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="ชาย" <?php echo ($user_data['gender'] === 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                            <option value="หญิง" <?php echo ($user_data['gender'] === 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                            <option value="อื่นๆ" <?php echo ($user_data['gender'] === 'อื่นๆ') ? 'selected' : ''; ?>>อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tel" class="form-label">เบอร์โทร</label>
                        <input type="tel" class="form-control" id="tel" name="tel" value="<?php echo htmlspecialchars($user_data['tel']); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="birth_date" class="form-label">วันเกิด</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user_data['birth_date']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">ที่อยู่</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="admin_users.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">ไม่พบข้อมูลผู้ใช้ที่ต้องการแก้ไข</div>
                <a href="admin_users.php" class="btn btn-secondary">กลับไปหน้าจัดการผู้ใช้</a>
            <?php endif; ?>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
