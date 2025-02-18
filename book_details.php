<?php
session_start(); // เริ่มต้น session เพื่อใช้งาน $_SESSION

include "db_connect.php"; // เชื่อมต่อกับฐานข้อมูล

// สมมุติว่า $book_id ได้รับค่ามาจาก URL query string
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id']; // รับ book_id จาก URL
} else {
    die("ไม่พบข้อมูลหนังสือ");
}

// ดึงข้อมูลหนังสือ
$sql = "SELECT * FROM books WHERE book_id = '$book_id'";
$result = $conn->query($sql);
$book = $result->fetch_assoc();

if (!$book) {
    die("ไม่พบข้อมูลหนังสือ");
}

// ตรวจสอบสถานะของหนังสือ
$status_message = "";
if ($book['status'] == 'borrowed') {
    $status_message = "หนังสือถูกยืมแล้ว";
} else {
    $status_message = "หนังสือพร้อมให้ยืม";
}

// ตรวจสอบว่า session มีค่าของ member_id หรือไม่
if (isset($_SESSION['member_id'])) {
    $member_id = $_SESSION['member_id']; // เก็บ member_id จาก session
    // ดึงชื่อผู้ยืมจากตาราง members
    $member_sql = "SELECT name FROM members WHERE member_id = '$member_id'";
    $member_result = $conn->query($member_sql);
    $member = $member_result->fetch_assoc();
    $borrower_name = $member['name']; // เก็บชื่อผู้ยืม
} else {
    // กรณีที่ไม่ได้ล็อกอิน ให้แสดงข้อความ
    $status_message = "กรุณาล็อกอินก่อนทำการยืมหนังสือ";
}

// การยืมหนังสือ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow']) && $book['status'] == 'available' && isset($member_id)) {
    // วันที่ยืม
    $borrow_date = date('Y-m-d');

    // เพิ่มข้อมูลการยืมในตาราง borrowings
    $borrow_sql = "INSERT INTO borrowings (book_id, member_id, borrower_name, borrow_date) 
                   VALUES ('$book_id', '$member_id', '$borrower_name', '$borrow_date')";
    
    if ($conn->query($borrow_sql) === TRUE) {
        // อัปเดตสถานะของหนังสือเป็น 'borrowed'
        $update_status = "UPDATE books SET status = 'borrowed' WHERE book_id = '$book_id'";
        $conn->query($update_status);
        $status_message = "คุณได้ทำการยืมหนังสือเรียบร้อยแล้ว";
        // รีเฟรชหน้าหลังจากยืมเสร็จ
        header("Location: book_details.php?book_id=$book_id");
        exit(); // หยุดการทำงานของสคริปต์เพื่อไม่ให้โค้ดดำเนินต่อไป
    } else {
        $status_message = "เกิดข้อผิดพลาดในการยืมหนังสือ: " . $conn->error;
    }
}

// การคืนหนังสือ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return']) && $book['status'] == 'borrowed' && isset($member_id)) {
    // ดึง borrow_id ที่เกี่ยวข้องกับการยืมหนังสือ
    $borrow_sql = "SELECT borrow_id FROM borrowings WHERE book_id = '$book_id' AND member_id = '$member_id' AND return_date IS NULL";
    $borrow_result = $conn->query($borrow_sql);
    $borrow = $borrow_result->fetch_assoc();

    if ($borrow) {
        $borrow_id = $borrow['borrow_id'];
        // อัปเดตวันที่คืนในตาราง borrowings
        $return_date = date('Y-m-d'); // วันที่คืน
        $return_sql = "UPDATE borrowings SET return_date = '$return_date' WHERE borrow_id = '$borrow_id'";
        
        if ($conn->query($return_sql) === TRUE) {
            // อัปเดตสถานะของหนังสือเป็น 'available'
            $update_status = "UPDATE books SET status = 'available' WHERE book_id = '$book_id'";
            $conn->query($update_status);

            $status_message = "คุณได้ทำการคืนหนังสือเรียบร้อยแล้ว";
        } else {
            $status_message = "เกิดข้อผิดพลาดในการคืนหนังสือ: " . $conn->error;
        }
    } else {
        $status_message = "ไม่พบข้อมูลการยืมหนังสือที่ต้องการคืน";
    }
}

// ดึงข้อมูลการยืมล่าสุดจากตาราง borrowings
$borrowing_sql = "SELECT borrow_date, return_date FROM borrowings WHERE book_id = '$book_id' AND member_id = '$member_id' ORDER BY borrow_date DESC LIMIT 1";
$borrowing_result = $conn->query($borrowing_sql);
$borrowing = $borrowing_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมื่อกดยืมเเละคืนเเล้วกรุณากดรีหน้าเว็บ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .status-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        .book-details {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin: 20px auto;
            border-radius: 8px;
            width: 75%;
            max-width: 500px;
        }

        .book-details img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book-details h2 {
            font-size: 20px;
            color: #333;
        }

        .book-details p {
            font-size: 14px;
            color: #666;
            margin: 8px 0;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .button-container button {
            padding: 8px 16px;
            margin: 0 8px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .alert {
            background-color: #ffc107;
            color: black;
            padding: 10px;
            text-align: center;
            margin: 10px auto;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
        }

        .back-button {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>รายละเอียดหนังสือ</h1>

<?php if (isset($status_message)) { echo "<p>$status_message</p>"; } ?>

<div class="book-details">
    <img src="<?php echo isset($book['image_url']) ? $book['image_url'] : 'images/book-placeholder.jpg'; ?>" alt="Book Cover">
    <h2><?php echo isset($book['title']) ? $book['title'] : 'ไม่พบชื่อหนังสือ'; ?></h2>
    <p>โดย: <?php echo isset($book['author']) ? $book['author'] : 'ไม่พบข้อมูลผู้เขียน'; ?></p>
    <p>หมวดหมู่: <?php echo isset($book['category']) ? $book['category'] : 'ไม่พบข้อมูลหมวดหมู่'; ?></p>
    <p><?php echo isset($book['description']) ? $book['description'] : 'ไม่พบคำอธิบาย'; ?></p>
</div>

<!-- แสดงข้อมูลการยืม -->
<?php if (isset($borrowing)) { ?>
    <p>วันที่ยืม: <?php echo isset($borrowing['borrow_date']) ? $borrowing['borrow_date'] : 'ไม่พบข้อมูล'; ?></p>
    <p>วันที่คืน: <?php echo isset($borrowing['return_date']) ? $borrowing['return_date'] : "ยังไม่ได้คืน"; ?></p>
<?php } ?>

<!-- ฟอร์มสำหรับยืมหนังสือ -->
<?php if (isset($_SESSION['member_id'])) { ?>
    <?php if (isset($book['status']) && $book['status'] == 'available') { ?>
        <form method="POST">
            <div class="button-container">
                <button type="submit" name="borrow">ยืมหนังสือ</button>
            </div>
        </form>
    <?php } elseif (isset($book['status']) && $book['status'] == 'borrowed') { ?>
        <p>คุณได้ยืมหนังสือเล่มนี้แล้ว</p>
        <form method="POST">
            <div class="button-container">
                <button type="submit" name="return">คืนหนังสือ</button>
            </div>
        </form>
    <?php } ?>
<?php } ?>

<div class="back-button">
    <a href="index.php">
        <button>กลับไปหน้าแรก</button>
    </a>
</div>

</body>
</html>
