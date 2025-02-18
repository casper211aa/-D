<?php
include "db_connect.php";
session_start();

if (!isset($_SESSION["member_id"])) {
    die("กรุณาเข้าสู่ระบบก่อน!");
}

$member_id = $_SESSION["member_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST["book_id"];

    // ตรวจสอบว่าหนังสือยังว่างหรือไม่
    $check_sql = "SELECT status FROM books WHERE book_id = $book_id";
    $check_result = $conn->query($check_sql);
    $book = $check_result->fetch_assoc();

    if ($book["status"] == "available") {
        // เพิ่มข้อมูลการยืม และเปลี่ยนสถานะหนังสือเป็น "borrowed"
        $borrow_sql = "INSERT INTO borrowings (book_id, member_id, borrow_date) VALUES ($book_id, $member_id, CURDATE())";
        $update_sql = "UPDATE books SET status = 'borrowed' WHERE book_id = $book_id";

        if ($conn->query($borrow_sql) === TRUE && $conn->query($update_sql) === TRUE) {
            echo "ยืมหนังสือสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    } else {
        echo "หนังสือถูกยืมไปแล้ว!";
    }
}
?>

<h2>ยืมหนังสือ</h2>
<form method="post">
    รหัสหนังสือ: <input type="number" name="book_id" required><br>
    <button type="submit">ยืม</button>
</form>