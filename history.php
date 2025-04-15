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
    <title>ประวัติการแลกเปลี่ยน</title>
    <link rel="stylesheet" href="style.css"> <!-- หรือ CSS ที่เกี่ยวข้อง -->
    <style>
        /* (CSS เดิมจาก history.php) */
        .history-list {
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
        }
        .history-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .history-item h5 {
            margin-bottom: 15px;
            color: #333;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .product-exchange-info {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 10px;
        }
        .product-info-box {
            text-align: center;
            width: 45%;
        }
        .product-info-box img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            object-position: center;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #eee;
        }
         .product-info-box p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #555;
         }
         .exchange-icon {
            font-size: 2em;
            color: #007bff;
         }
         /* Navbar styles */
        .navbar {
            display: flex;
            align-items: center;
            background-color: #333;
            justify-content: space-between; /* จัดให้เนื้อหาทั้งหมดอยู่ตรงข้ามกัน */
            position: relative;
            margin: 0 auto;

        }

        .navbar-right {
            margin-right: 1.5rem;
            font-size: 2.5rem;
            color: #ffffff;
        }
        .navbar-right a {
            color: #ffffff; /* ใช้สีของพาเรนต์ */
            text-decoration: none; /* เอาขีดเส้นใต้ของลิงก์ออก (ถ้ามี) */
        }
        .navbar-center a:hover {
            background-color: #007bff; /* Blue color on hover */
            color: #ffffff; /* Keep text white */
            transform: translateY(-3px); /* Slight movement on hover */
        }
        .navbar-center {
            position: relative;
            font-size: 20px;
            padding: 25px 20px;
            letter-spacing: 0.10em;
            display: flex;
            gap: 5s0px; /* เพิ่มระยะห่างระหว่างลิงก์ */
        }

        .navbar-center a {
            font-family: 'Roboto', sans-serif;
            color: #ffffff; /* สีตัวอักษร */
            text-decoration: none; /* ลบขีดเส้นใต้ */
            padding: 10px 20px;
            border-radius: 5px;
        }

        /* Buttons and Review Specific Styles */
        .history-buttons {
            display: flex; /* ใช้ flexbox */
            justify-content: space-between; /* จัดให้อยู่คนละฝั่ง */
            align-items: center; /* จัดให้อยู่ตรงกลางแนวตั้ง */
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee; /* เส้นคั่น */
        }

        .view-review-btn { /* ปุ่ม/ลิงก์ ดูรีวิว */
            cursor: pointer;
            color: #007bff;
            background: none;
            border: none;
            padding: 0;
            text-decoration: none;
            font-size: 0.9em;
        }
        .view-review-btn:hover { color: #0056b3; }

        .text-muted { /* ข้อความ "ยังไม่มีรีวิว" */
             color: #6c757d !important;
             font-size: 0.9em;
        }

        .review-right-btn { /* ปุ่ม รีวิว */
             /* ใช้ class ของ Bootstrap หรือ custom */
             width: 50px; /* หรือปรับเป็นขนาดที่ต้องการ เช่น 120px */
             text-align: center;
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

<div class="history-list">
    <h1 style="text-align: center; margin-bottom: 30px;">ประวัติการแลกเปลี่ยน</h1>
    <?php
    // ดึงข้อมูลประวัติ (รวม ID เพื่อใช้ตรวจสอบ)
    $sql = "SELECT
                h.history_id, h.exchange_date,
                h.product_owner_id, h.interested_user_id, -- เพิ่ม ID
                po.firstname AS product_owner_name,
                iu.firstname AS interested_user_name,
                pop.product_Name AS product_owner_product_name, pop.Image AS product_owner_product_image,
                pop.product_detail AS product_owner_product_detail, pop.product_category AS product_owner_product_category,
                iup.product_Name AS interested_user_product_name, iup.Image AS interested_user_product_image,
                iup.product_detail AS interested_user_product_detail, iup.product_category AS interested_user_product_category
            FROM history h
            JOIN users po ON h.product_owner_id = po.user_id
            JOIN users iu ON h.interested_user_id = iu.user_id
            JOIN product pop ON h.product_owner_product_id = pop.product_Id
            JOIN product iup ON h.interested_user_product_id = iup.product_Id
            WHERE h.product_owner_id = ? OR h.interested_user_id = ?
            ORDER BY h.exchange_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $history_id = $row['history_id'];
            $owner_id = $row['product_owner_id'];
            $interested_id = $row['interested_user_id'];

            // หา ID และชื่อของ "ผู้ใช้คนอื่น"
            $other_user_id = ($user_id == $owner_id) ? $interested_id : $owner_id;
            $other_user_name = ($user_id == $owner_id) ? $row['interested_user_name'] : $row['product_owner_name'];

            // --- ตรวจสอบรีวิวขาออก (เรารีวิวเขาหรือยัง) ---
            $has_reviewed = false;
            $sql_check_out = "SELECT review_id FROM reviews WHERE history_id = ? AND reviewer_id = ?";
            $stmt_check_out = $conn->prepare($sql_check_out);
            $stmt_check_out->bind_param("ii", $history_id, $user_id);
            $stmt_check_out->execute();
            if ($stmt_check_out->get_result()->num_rows > 0) {
                $has_reviewed = true;
            }
            $stmt_check_out->close();

            // --- ตรวจสอบรีวิวขาเข้า (เขารีวิวเราหรือยัง) ---
            $incoming_review = null;
            // ดึงเฉพาะ comment
            $sql_check_in = "SELECT review_id, comment FROM reviews WHERE history_id = ? AND reviewed_user_id = ?";
            $stmt_check_in = $conn->prepare($sql_check_in);
            $stmt_check_in->bind_param("ii", $history_id, $user_id);
            $stmt_check_in->execute();
            $result_check_in = $stmt_check_in->get_result();
            if ($result_check_in->num_rows > 0) {
                $incoming_review = $result_check_in->fetch_assoc();
            }
            $stmt_check_in->close();


            // --- แสดงผลรายการประวัติ ---
            echo "<div class='history-item'>";
            echo "<h5>แลกเปลี่ยนเมื่อ: " . date("d/m/Y H:i", strtotime($row['exchange_date'])) . "</h5>";
            echo "<div class='product-exchange-info'>";
                // กล่องข้อมูลสินค้าเจ้าของเดิม
                echo "<div class='product-info-box'>";
                echo "<img src='uploads/" . htmlspecialchars($row['product_owner_product_image']) . "' alt='" . htmlspecialchars($row['product_owner_product_name']) . "'>";
                echo "<p><strong>สินค้า:</strong> " . htmlspecialchars($row['product_owner_product_name']) . "</p>";
                echo "<p><strong>ประเภท:</strong> " . htmlspecialchars($row['product_owner_product_category']) . "</p>";
                echo "<p><strong>รายละเอียด:</strong> " . nl2br(htmlspecialchars($row['product_owner_product_detail'])) . "</p>";
                echo "<p><strong>เจ้าของเดิม:</strong> " . htmlspecialchars($row['product_owner_name']) . "</p>";
                echo "</div>";

                // ไอคอนแลกเปลี่ยน
                echo "<div class='exchange-icon'><i class='fas fa-exchange-alt'></i></div>";

                // กล่องข้อมูลสินค้าผู้สนใจเดิม
                echo "<div class='product-info-box'>";
                echo "<img src='uploads/" . htmlspecialchars($row['interested_user_product_image']) . "' alt='" . htmlspecialchars($row['interested_user_product_name']) . "'>";
                echo "<p><strong>สินค้า:</strong> " . htmlspecialchars($row['interested_user_product_name']) . "</p>";
                echo "<p><strong>ประเภท:</strong> " . htmlspecialchars($row['interested_user_product_category']) . "</p>";
                echo "<p><strong>รายละเอียด:</strong> " . nl2br(htmlspecialchars($row['interested_user_product_detail'])) . "</p>";
                echo "<p><strong>ผู้สนใจเดิม:</strong> " . htmlspecialchars($row['interested_user_name']) . "</p>";
                echo "</div>";
            echo "</div>"; // ปิด product-exchange-info

            // --- ส่วนปุ่มรีวิว ---
            echo "<div class='history-buttons'>";

            // ปุ่ม/ลิงก์ "ดูรีวิว" (ที่เขาให้เรา)
            if ($incoming_review) {
                // เอา data-rating ออก
                echo "<button type='button' class='view-review-btn'
                        data-bs-toggle='modal' data-bs-target='#viewReviewModal'
                        data-reviewer-name='" . htmlspecialchars($other_user_name) . "'
                        data-comment='" . nl2br(htmlspecialchars($incoming_review['comment'] ?? 'ไม่มีความคิดเห็น')) . "'>
                        ดูรีวิวที่ได้รับ
                      </button>";
            } else {
                echo "<span class='text-muted'>ยังไม่มีรีวิว</span>";
            }

            // ปุ่ม "รีวิว" (ที่เราจะให้เขา)
            if ($has_reviewed) {
                echo "<button class='btn btn-secondary btn-sm review-right-btn' disabled>รีวิว</button>";
            } else {
                echo "<a href='review_form.php?history_id={$history_id}&reviewed_user_id={$other_user_id}' class='btn btn-primary btn-sm review-right-btn'>รีวิว</a>";
            }

            echo "</div>"; // ปิด history-buttons
            echo "</div>"; // ปิด history-item
        }
    } else {
        echo "<p style='text-align: center;'>ไม่มีประวัติการแลกเปลี่ยน</p>";
    }
    $stmt->close();
    $conn->close();
    ?>
</div>

<!-- Modal สำหรับแสดงรีวิวที่ได้รับ -->
<div class="modal fade" id="viewReviewModal" tabindex="-1" aria-labelledby="viewReviewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewReviewModalLabel">รีวิวที่ได้รับจาก <span id="modal-reviewer-name"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- เอาส่วนแสดงคะแนนออก -->
        <p><strong>ความคิดเห็น:</strong></p>
        <div id="modal-comment"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    // เมื่อ Modal แสดงรีวิวถูกเปิด
    $('#viewReviewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // ปุ่มที่กดเปิด Modal
        var reviewerName = button.data('reviewer-name');
        // เอา rating ออก
        var comment = button.data('comment');

        var modal = $(this);
        modal.find('#modal-reviewer-name').text(reviewerName);
        // เอาโค้ดแสดงดาวออก
        modal.find('#modal-comment').html(comment); // ใช้ .html() เผื่อ comment มี <br>
    });
});
</script>
</body>
</html>
