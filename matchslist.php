<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>รายการจับคู่</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* (คง CSS เดิมไว้) */
        .matchslist {
            padding: 20px;
        }
        .match-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .match-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            object-position: center;
            margin-right: 10px;
            border-radius: 5px;
        }
        .match-item .product-info {
            display: flex;
            align-items: center;
        }
    </style>
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
        <a href="notification.php">แจ้งเตือน</a>
        <a href="matchslist.php">รายการจับคู่</a>
        <a href="history.php">ดูประวัติการแลกเปลี่ยน</a>
    </div>
    <div class="navbar-right">
        <a href="profile.php"><span><i class="fa-regular fa-user"></i></span></a>
    </div>
</div>

    <div class="matchslist">
        <?php
        // ดึงข้อมูลการจับคู่ทั้งหมด โดยกรองเฉพาะการจับคู่ที่เกี่ยวข้องกับผู้ใช้ปัจจุบัน และมีสถานะเป็น active
        $sql = "SELECT m.*, 
                       po.firstname AS product_owner_name, 
                       iu.firstname AS interested_user_name,
                       pop.product_Name AS product_owner_product_name,
                       pop.Image AS product_owner_product_image,
                       iup.product_Name AS interested_user_product_name,
                       iup.Image AS interested_user_product_image
                FROM matchs m
                JOIN users po ON m.product_owner_id = po.user_id
                JOIN users iu ON m.interested_user_id = iu.user_id
                JOIN product pop ON m.product_owner_product_id = pop.product_Id
                JOIN product iup ON m.interested_user_product_id = iup.product_Id
                WHERE (m.product_owner_id = ? OR m.interested_user_id = ?) AND m.status = 'active'
                ORDER BY m.match_date DESC"; // เรียงลำดับตามวันที่จับคู่ล่าสุด

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='match-item'>";
                echo "<div class='product-info'>";
                echo "<img src='uploads/" . $row['product_owner_product_image'] . "' alt='" . $row['product_owner_product_name'] . "'>";
                echo "<p><strong>สินค้าของคุณ:</strong> " . $row['product_owner_product_name'] . " (เจ้าของ: " . $row['product_owner_name'] . ")</p>";
                echo "</div>";
                echo "<div class='product-info'>";
                echo "<img src='uploads/" . $row['interested_user_product_image'] . "' alt='" . $row['interested_user_product_name'] . "'>";
                echo "<p><strong>สินค้าที่สนใจ:</strong> " . $row['interested_user_product_name'] . " (ผู้สนใจ: " . $row['interested_user_name'] . ")</p>";
                echo "</div>";
                echo "<p><strong>วันที่จับคู่:</strong> " . $row['match_date'] . "</p>";
                echo '<a href="chat.php?matchs_id=' . $row['matchs_id'] . '" style="padding: 10px 20px; font-size: 16px; border: none; background: blue; color: white; border-radius: 5px; cursor: pointer; text-decoration: none;"><strong>แชท</strong></a>';
                // เพิ่มปุ่มยกเลิกการจับคู่
                echo '<button class="cancel-match-btn" data-matchs-id="' . $row['matchs_id'] . '" style="padding: 10px 20px; font-size: 16px; border: none; background: red; color: white; border-radius: 5px; cursor: pointer; margin-left: 10px;"><strong>ยกเลิกการจับคู่</strong></button>';

                // ตรวจสอบว่าผู้ใช้เป็นเจ้าของสินค้าหรือผู้สนใจ
                $is_owner = ($user_id == $row['product_owner_id']);
                $is_interested = ($user_id == $row['interested_user_id']);

                // แสดงปุ่ม "แลกเปลี่ยนสำเร็จ" เฉพาะเมื่อผู้ใช้เกี่ยวข้องกับรายการนี้
                if ($is_owner || $is_interested) {
                    $button_text = "ยืนยันแลกเปลี่ยนสำเร็จ";
                    $button_disabled = "";

                    if ($is_owner && $row['product_owner_confirm'] == 1) {
                        $button_text = "รออีกฝ่ายยืนยัน";
                        $button_disabled = "disabled";
                    } elseif ($is_interested && $row['interested_user_confirm'] == 1) {
                        $button_text = "รออีกฝ่ายยืนยัน";
                        $button_disabled = "disabled";
                    }
                    
                    if($row['product_owner_confirm'] == 1 && $row['interested_user_confirm'] == 1){
                        $button_text = "แลกเปลี่ยนสำเร็จ";
                        $button_disabled = "disabled";
                    }

                    echo '<button class="complete-match-btn" data-matchs-id="' . $row['matchs_id'] . '" data-user-type="' . ($is_owner ? 'owner' : 'interested') . '" style="padding: 10px 20px; font-size: 16px; border: none; background: green; color: white; border-radius: 5px; cursor: pointer; margin-left: 10px;" ' . $button_disabled . '><strong>' . $button_text . '</strong></button>';
                }
                echo "</div>";
            }
        } else {
            echo "<p>ไม่มีรายการจับคู่</p>";
        }
        ?>
    </div>
    <script>
    $(document).ready(function() {
        $('.cancel-match-btn').click(function() {
            var matchsId = $(this).data('matchs-id');
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจับคู่นี้?")) {
                $.ajax({
                    url: 'cancel_match.php',
                    type: 'POST',
                    data: { matchs_id: matchsId },
                    success: function(response) {
                        if (response === 'success') {
                            alert('ยกเลิกการจับคู่สำเร็จ');
                            location.reload(); // รีโหลดหน้าเพื่ออัปเดตรายการ
                        } else {
                            alert('เกิดข้อผิดพลาดในการยกเลิกการจับคู่');
                        }
                    }
                });
            }
        });
        $('.complete-match-btn').click(function() {
            var matchsId = $(this).data('matchs-id');
            var userType = $(this).data('user-type');
            if (confirm("คุณแน่ใจหรือไม่ว่าการแลกเปลี่ยนนี้สำเร็จแล้ว?")) {
                $.ajax({
                    url: 'complete_match.php',
                    type: 'POST',
                    data: { matchs_id: matchsId, user_type: userType },
                    success: function(response) {
                        if (response === 'success') {
                            alert('บันทึกการแลกเปลี่ยนสำเร็จ');
                            // ไม่ต้องรีโหลดหน้าแล้ว เพราะรายการจะหายไปเอง
                            //location.reload();
                        } else if (response === 'waiting') {
                            alert('รออีกฝ่ายยืนยัน');
                            location.reload();
                        } else {
                            alert('เกิดข้อผิดพลาดในการบันทึกการแลกเปลี่ยน');
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>
