<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matchs_id = $_POST['matchs_id'];
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // ป้องกัน XSS
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // ดึงข้อมูล firstname และ lastname ของผู้ส่ง
    $user_sql = "SELECT firstname, lastname FROM users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $sender_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $sender_firstname = $user_row['firstname'];
    $sender_lastname = $user_row['lastname'];

    // บันทึกข้อมูลลงในตาราง chats พร้อมกับ firstname และ lastname
    $sql = "INSERT INTO chats (matchs_id, sender_id, receiver_id, message, sender_firstname, sender_lastname) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisss", $matchs_id, $sender_id, $receiver_id, $message, $sender_firstname, $sender_lastname);
    $stmt->execute();
}
?>
