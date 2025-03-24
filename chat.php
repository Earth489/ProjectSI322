<?php
include 'connection.php';
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามี matchs_id หรือไม่
if (!isset($_GET['matchs_id'])) {
    header("Location: matchslist.php");
    exit();
}

$matchs_id = $_GET['matchs_id'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลการจับคู่
$sql = "SELECT m.*, po.firstname AS product_owner_name, iu.firstname AS interested_user_name
        FROM matchs m
        JOIN users po ON m.product_owner_id = po.user_id
        JOIN users iu ON m.interested_user_id = iu.user_id
        WHERE m.matchs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $matchs_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "ไม่พบข้อมูลการจับคู่";
    exit;
}

$row = $result->fetch_assoc();
$receiver_id = ($user_id == $row['product_owner_id']) ? $row['interested_user_id'] : $row['product_owner_id'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แชท</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .chat-container {
            width: 90%;
            max-width: 600px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
        }

        .chat-messages {
            height: 350px;
            overflow-y: auto;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .message {
            display: flex;
            align-items: flex-start; /* ปรับให้ข้อความชิดด้านบน */
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
        }

        .sent {
            background-color: #4CAF50;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .received {
            background-color: #e0e0e0;
            color: black;
            align-self: flex-start;
        }
        .message p{
            margin: 0;
        }

        form {
            display: flex;
            margin-top: 15px;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        /* ปุ่มย้อนกลับ */
        .back-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <div class="chat-container">
        <!-- เพิ่มปุ่มย้อนกลับ -->
        <a href="matchslist.php" class="back-btn">ย้อนกลับ</a>
        <h2>แชทกับ <?php echo ($user_id == $row['product_owner_id']) ? $row['interested_user_name'] : $row['product_owner_name']; ?></h2>
        <div class="chat-messages" id="chat-messages">
            <!-- ข้อความจะถูกโหลดที่นี่ -->
        </div>
        <form id="chat-form">
            <input type="hidden" name="matchs_id" value="<?php echo $matchs_id; ?>">
            <input type="hidden" name="sender_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <input type="text" name="message" id="message" placeholder="พิมพ์ข้อความ..." required>
            <button type="submit">ส่ง</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // โหลดข้อความเมื่อหน้าเว็บโหลด
            loadMessages();

            // ฟังก์ชันโหลดข้อความ
            function loadMessages() {
                var matchs_id = <?php echo $matchs_id; ?>;
                $.ajax({
                    url: 'get_messages.php',
                    type: 'GET',
                    data: { matchs_id: matchs_id },
                    success: function(data) {
                        $('#chat-messages').html(data);
                        // เลื่อน scrollbar ไปที่ด้านล่างสุด
                        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                    }
                });
            }

            // ส่งข้อความ
            $('#chat-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: formData,
                    success: function() {
                        $('#message').val('');
                        loadMessages();
                    }
                });
            });

            // โหลดข้อความทุกๆ 3 วินาที
            setInterval(loadMessages, 3000);
        });
    </script>
</body>
</html>
