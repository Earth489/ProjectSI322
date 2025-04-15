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
    <link rel="stylesheet" href="style.css"> <!-- ตรวจสอบว่าไฟล์ CSS นี้มีอยู่จริง -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* (คง CSS เดิมไว้ หรือปรับปรุงตามต้องการ) */
        .matchslist {
            padding: 20px;
            max-width: 900px; /* หรือความกว้างที่เหมาะสม */
            margin: 20px auto; /* จัดกลาง */
        }
        .match-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px; /* เพิ่ม padding */
            margin-bottom: 15px; /* เพิ่มระยะห่าง */
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .match-item .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px; /* เพิ่มระยะห่างระหว่างข้อมูลสินค้า */
        }
        .match-item img {
            width: 80px; /* ปรับขนาดรูปภาพ */
            height: 80px;
            object-fit: cover;
            object-position: center;
            margin-right: 15px; /* เพิ่มระยะห่าง */
            border-radius: 5px;
            border: 1px solid #eee;
        }
        .match-item p {
            margin-bottom: 5px; /* ลดระยะห่างของ p */
            font-size: 0.95em; /* ปรับขนาดฟอนต์ */
        }
        .match-item .actions {
            margin-top: 15px; /* เพิ่มระยะห่างด้านบนของปุ่ม */
            display: flex; /* จัดปุ่มแนวนอน */
            gap: 10px; /* ระยะห่างระหว่างปุ่ม */
            flex-wrap: wrap; /* ให้ปุ่มขึ้นบรรทัดใหม่ได้ */
        }
        .match-item .actions button,
        .match-item .actions a {
            padding: 8px 15px; /* ปรับขนาด padding */
            font-size: 0.9em; /* ปรับขนาดฟอนต์ */
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            color: white;
            text-align: center;
        }
        .chat-btn { background-color: #007bff; } /* สีน้ำเงิน */
        .confirm-exchange-btn { background-color: #28a745; } /* สีเขียว */
        .cancel-match-btn { background-color: #dc3545; } /* สีแดง */
        .confirm-exchange-btn:disabled {
            background-color: #6c757d; /* สีเทาเมื่อ disable */
            cursor: not-allowed;
        }
        .status-text {
            font-style: italic;
            color: #6c757d;
            margin-left: 10px;
        }
         /* Navbar styles (อาจจะนำมาจากไฟล์อื่น) */
         .navbar {
            display: flex;
            align-items: center;
            background-color: #333;
            justify-content: space-between;
            position: relative;
            margin: 0 auto;
            padding: 10px 0; /* Add some padding */
        }
        .navbar a {
            text-decoration: none;
        }
        .navbar .text {
            color: #ffffff;
            margin-left: 1.5rem;
            display: block;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            line-height: 1.2; /* Adjust line height */
        }
        .navbar-center {
            display: flex;
            gap: 20px; /* Space between links */
            justify-content: center; /* Center the links */
            flex-grow: 1; /* Allow center to take up space */
        }
        .navbar-center a {
            font-family: 'Roboto', sans-serif;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 20px;
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition */
        }
        .navbar-center a:hover {
            background-color: #007bff;
            color: #ffffff;
            transform: translateY(-3px);
        }
        .navbar-right {
            margin-right: 1.5rem;
        }
        .navbar-right a {
            color: #ffffff;
            font-size: 2.5rem;
            text-decoration: none;
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
        <a href="myproduct.php">สินค้าของฉัน</a>
        <a href="notification.php">แจ้งเตือน</a>
        <a href="matchslist.php">รายการจับคู่</a>
        <a href="history.php">ดูประวัติการแลกเปลี่ยน</a>
    </div>
    <div class="navbar-right">
        <a href="profile.php"><span><i class="fa-regular fa-user"></i></span></a>
    </div>
</div>

<div class="matchslist">
    <h1 style="text-align: center; margin-bottom: 30px;">รายการจับคู่</h1>
    <?php
    // ดึงข้อมูลการจับคู่ทั้งหมด โดยกรองเฉพาะการจับคู่ที่เกี่ยวข้องกับผู้ใช้ปัจจุบัน และมีสถานะเป็น active
    // เพิ่ม owner_confirmed และ interested_confirmed ใน SELECT
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
    if ($stmt === false) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='match-item' id='match-item-" . $row['matchs_id'] . "'>"; // เพิ่ม ID ให้ div

            // แสดงข้อมูลสินค้า
            echo "<div class='product-info'>";
            echo "<img src='uploads/" . htmlspecialchars($row['product_owner_product_image']) . "' alt='" . htmlspecialchars($row['product_owner_product_name']) . "'>";
            echo "<div>"; // Div ครอบข้อความ
            echo "<p><strong>สินค้าของ:</strong> " . htmlspecialchars($row['product_owner_name']) . "</p>";
            echo "<p>" . htmlspecialchars($row['product_owner_product_name']) . "</p>";
            echo "</div>";
            echo "</div>";

            echo "<div style='text-align: center; margin: 5px 0;'><i class='fas fa-exchange-alt'></i></div>"; // ไอคอนแลกเปลี่ยน

            echo "<div class='product-info'>";
            echo "<img src='uploads/" . htmlspecialchars($row['interested_user_product_image']) . "' alt='" . htmlspecialchars($row['interested_user_product_name']) . "'>";
            echo "<div>"; // Div ครอบข้อความ
            echo "<p><strong>แลกกับสินค้าของ:</strong> " . htmlspecialchars($row['interested_user_name']) . "</p>";
            echo "<p>" . htmlspecialchars($row['interested_user_product_name']) . "</p>";
            echo "</div>";
            echo "</div>";

            echo "<p style='font-size: 0.85em; color: #6c757d;'><strong>วันที่จับคู่:</strong> " . date("d/m/Y H:i", strtotime($row['match_date'])) . "</p>";

            // ส่วนของปุ่ม Actions
            echo "<div class='actions'>";
            // ปุ่มแชท
            echo '<a href="chat.php?matchs_id=' . $row['matchs_id'] . '" class="chat-btn">แชท</a>';

            // ปุ่มยืนยันการแลกเปลี่ยน
            $button_text = "ยืนยันการแลกเปลี่ยน";
            $button_disabled = "";
            $status_text = "";

            // ตรวจสอบว่าเป็นเจ้าของหรือผู้สนใจ
            if ($user_id == $row['product_owner_id']) {
                if ($row['owner_confirmed'] == 1) {
                    $button_disabled = "disabled";
                    $button_text = "ยืนยันแล้ว";
                    if ($row['interested_confirmed'] == 0) {
                        $status_text = "<span class='status-text'>(รอผู้สนใจยืนยัน)</span>";
                    }
                }
            } elseif ($user_id == $row['interested_user_id']) {
                if ($row['interested_confirmed'] == 1) {
                    $button_disabled = "disabled";
                    $button_text = "ยืนยันแล้ว";
                     if ($row['owner_confirmed'] == 0) {
                        $status_text = "<span class='status-text'>(รอเจ้าของยืนยัน)</span>";
                    }
                }
            }

            echo '<button class="confirm-exchange-btn" data-matchs-id="' . $row['matchs_id'] . '" ' . $button_disabled . '>' . $button_text . '</button>';
            echo $status_text; // แสดงสถานะรอ

            // ปุ่มยกเลิกการจับคู่
            echo '<button class="cancel-match-btn" data-matchs-id="' . $row['matchs_id'] . '">ยกเลิกการจับคู่</button>';

            echo "</div>"; // ปิด actions
            echo "</div>"; // ปิด match-item
        }
    } else {
        echo "<p style='text-align: center;'>ไม่มีรายการจับคู่</p>";
    }
    $stmt->close();
    $conn->close();
    ?>
</div>

<script>
$(document).ready(function() {
    // --- โค้ดสำหรับปุ่มยกเลิก (เหมือนเดิม) ---
    $(".cancel-match-btn").click(function() {
        const matchId = $(this).data("matchs-id");
        const matchItemDiv = $("#match-item-" + matchId); // อ้างอิง div ของรายการ

        if (confirm("คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจับคู่นี้?")) {
            $.ajax({
                url: "cancel_match.php", // ตรวจสอบว่าไฟล์นี้ทำงานถูกต้อง
                type: "POST",
                dataType: "json", // คาดหวัง JSON response
                data: { matchs_id: matchId },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        matchItemDiv.fadeOut(500, function() { $(this).remove(); }); // ลบรายการออกจากหน้าเว็บ
                        // ตรวจสอบว่ามีรายการเหลืออยู่หรือไม่
                        if ($(".match-item").length === 1) { // ถ้าลบอันสุดท้ายไปแล้ว
                             $(".matchslist").append("<p style='text-align: center;'>ไม่มีรายการจับคู่</p>");
                        }
                    } else {
                        alert("เกิดข้อผิดพลาด: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                     // แสดงข้อผิดพลาดที่ละเอียดขึ้น (สำหรับ debug)
                     console.error("AJAX Error:", status, error);
                     console.error("Response Text:", xhr.responseText);
                     alert("เกิดข้อผิดพลาดในการสื่อสารกับเซิร์ฟเวอร์ โปรดลองอีกครั้ง");
                }
            });
        }
    });

    // --- โค้ดใหม่สำหรับปุ่มยืนยันการแลกเปลี่ยน ---
    $(".confirm-exchange-btn").click(function() {
        const matchId = $(this).data("matchs-id");
        const button = $(this); // เก็บ element ของปุ่มที่ถูกคลิก
        const matchItemDiv = $("#match-item-" + matchId); // อ้างอิง div ของรายการ

        // ไม่ต้องมี confirm() เพราะการกดยืนยันควรทำได้เลย
        button.prop('disabled', true).text('กำลังดำเนินการ...'); // ป้องกันการกดซ้ำ

        $.ajax({
            url: "confirm_exchange.php", // ไฟล์ PHP ที่จะสร้างใหม่
            type: "POST",
            dataType: "json", // คาดหวัง JSON response
            data: { matchs_id: matchId },
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message); // แสดงข้อความจาก server
                    if (response.action === 'completed') {
                        // ถ้าแลกเปลี่ยนสำเร็จ ให้ลบรายการนี้ออกจากหน้า
                        matchItemDiv.fadeOut(500, function() { $(this).remove(); });
                         // ตรวจสอบว่ามีรายการเหลืออยู่หรือไม่
                        if ($(".match-item").length === 1) {
                             $(".matchslist").append("<p style='text-align: center;'>ไม่มีรายการจับคู่</p>");
                        }
                    } else if (response.action === 'confirmed') {
                        // ถ้ายืนยันฝ่ายเดียวสำเร็จ
                        button.text('ยืนยันแล้ว'); // เปลี่ยนข้อความปุ่ม
                        // อาจจะเพิ่มข้อความ "(รออีกฝ่ายยืนยัน)" ต่อท้ายปุ่ม
                        button.after("<span class='status-text'>(รออีกฝ่ายยืนยัน)</span>");
                    } else {
                         // กรณีอื่นๆ ที่อาจเกิดขึ้น (ไม่น่ามีถ้า logic ถูก)
                         button.prop('disabled', false).text('ยืนยันการแลกเปลี่ยน'); // คืนค่าปุ่ม
                    }
                } else {
                    // แสดงข้อผิดพลาดจาก server
                    alert("เกิดข้อผิดพลาด: " + response.message);
                    button.prop('disabled', false).text('ยืนยันการแลกเปลี่ยน'); // คืนค่าปุ่มให้กดใหม่ได้
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.error("Response Text:", xhr.responseText);
                alert("เกิดข้อผิดพลาดในการสื่อสารกับเซิร์ฟเวอร์ โปรดลองอีกครั้ง");
                button.prop('disabled', false).text('ยืนยันการแลกเปลี่ยน'); // คืนค่าปุ่ม
            }
        });
    });
});
</script>
</body>
</html>
