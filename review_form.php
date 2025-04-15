<?php
include 'connection.php';
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามี history_id และ reviewed_user_id ส่งมาหรือไม่
if (!isset($_GET['history_id']) || !isset($_GET['reviewed_user_id'])) {
    echo "ข้อมูลไม่ครบถ้วน";
    exit();
}

$user_id = $_SESSION['user_id']; // ID ของคนเขียนรีวิว
$history_id = intval($_GET['history_id']);
$reviewed_user_id = intval($_GET['reviewed_user_id']); // ID ของคนที่จะถูกรีวิว

// --- ตรวจสอบสิทธิ์ (เหมือนเดิม) ---
$sql_check_permission = "SELECT history_id FROM history
                         WHERE history_id = ?
                         AND ((product_owner_id = ? AND interested_user_id = ?) OR (product_owner_id = ? AND interested_user_id = ?))";
$stmt_check_permission = $conn->prepare($sql_check_permission);
$stmt_check_permission->bind_param("iiiii", $history_id, $user_id, $reviewed_user_id, $reviewed_user_id, $user_id);
$stmt_check_permission->execute();
if ($stmt_check_permission->get_result()->num_rows == 0) {
    echo "คุณไม่มีสิทธิ์รีวิวรายการนี้";
    $stmt_check_permission->close();
    $conn->close();
    exit();
}
$stmt_check_permission->close();

// --- ตรวจสอบการรีวิวซ้ำ (เหมือนเดิม) ---
$sql_check_exist = "SELECT review_id FROM reviews WHERE history_id = ? AND reviewer_id = ?";
$stmt_check_exist = $conn->prepare($sql_check_exist);
$stmt_check_exist->bind_param("ii", $history_id, $user_id);
$stmt_check_exist->execute();
if ($stmt_check_exist->get_result()->num_rows > 0) {
    echo "<script>alert('คุณได้รีวิวรายการนี้ไปแล้ว'); window.location.href='history.php';</script>";
    $stmt_check_exist->close();
    $conn->close();
    exit();
}
$stmt_check_exist->close();

// --- ดึงชื่อผู้ที่จะถูกรีวิว (เหมือนเดิม) ---
$sql_get_name = "SELECT firstname FROM users WHERE user_id = ?";
$stmt_get_name = $conn->prepare($sql_get_name);
$stmt_get_name->bind_param("i", $reviewed_user_id);
$stmt_get_name->execute();
$result_name = $stmt_get_name->get_result();
$reviewed_user_name = ($result_name->num_rows > 0) ? $result_name->fetch_assoc()['firstname'] : 'ผู้ใช้';
$stmt_get_name->close();

// --- ประมวลผลเมื่อกด Submit ---
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เช็ค comment
    if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
        $comment = trim($_POST['comment']);

        // --- บันทึกข้อมูลลงฐานข้อมูล (เอา rating ออก) ---
        $sql_insert = "INSERT INTO reviews (history_id, reviewer_id, reviewed_user_id, comment) VALUES (?, ?, ?, ?)"; // ถ้าไม่มี rating
        // $sql_insert = "INSERT INTO reviews (history_id, reviewer_id, reviewed_user_id, rating, comment) VALUES (?, ?, ?, NULL, ?)"; // ถ้ามี rating แต่ไม่ใช้
        $stmt_insert = $conn->prepare($sql_insert);

        if ($stmt_insert === false) {
            $error_message = "เกิดข้อผิดพลาดในการเตรียมข้อมูล: " . $conn->error;
        } else {
            // แก้ bind_param ให้เหลือ 4 ตัว (เอา i ตัวที่ 4 ออก)
            $stmt_insert->bind_param("iiis", $history_id, $user_id, $reviewed_user_id, $comment); // ถ้าไม่มี rating
            // $stmt_insert->bind_param("iiis", $history_id, $user_id, $reviewed_user_id, $comment); // ถ้ามี rating แต่ไม่ใช้ (อันนี้ผิด ต้องเป็น iiiis)
       
           if ($stmt_insert->execute()) {
               echo "<script>alert('ส่งรีวิวเรียบร้อยแล้ว'); window.location.href='history.php';</script>";
               exit();
           } else {
               $error_message = "เกิดข้อผิดพลาดในการบันทึกรีวิว: " . $stmt_insert->error;
           }
           $stmt_insert->close();
        }
    } else {
        $error_message = "กรุณากรอกความคิดเห็น";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เขียนรีวิว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container bg-white p-4 rounded shadow">
        <h2 class="text-center mb-4">รีวิวการแลกเปลี่ยนกับ <?php echo htmlspecialchars($reviewed_user_name); ?></h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- เอาส่วน Rating ออก -->
            <div class="mb-3">
                <label for="comment" class="form-label">ความคิดเห็น:</label>
                <textarea class="form-control" id="comment" name="comment" rows="5" required><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : ''; ?></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="history.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">ส่งรีวิว</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
