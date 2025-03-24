<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit();
}

$matchs_id = $_GET['matchs_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM chats WHERE matchs_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $matchs_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
while ($row = $result->fetch_assoc()) {
    $message_class = ($row['sender_id'] == $user_id) ? 'sent' : 'received';
    // ลบโค้ดแสดงชื่อผู้ส่งออกไป
    $output .= '<div class="message ' . $message_class . '">';
    // $output .= '<p><strong>' . $sender_name . ':</strong></p>'; // ลบออก
    $output .= '<p>' . $row['message'] . '</p>'; // แสดงข้อความ
    $output .= '</div>';
}

echo $output;
?>
