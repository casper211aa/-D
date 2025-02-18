<?php
include "db_connect.php"; // เชื่อมต่อกับฐานข้อมูล

// ดึงข้อมูลหนังสือทั้งหมดรวมถึง image_url
$sql = "SELECT * FROM books WHERE status = 'available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - ระบบห้องสมุด</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #444;
            padding: 10px 0;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #f4f4f4;
        }

        .book-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .book-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 200px;
            margin: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .book-item:hover {
            transform: scale(1.05);
        }

        .book-item img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .book-item h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .book-item p {
            color: #666;
            font-size: 14px;
        }

        .book-item a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .book-item a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <h1>ระบบห้องสมุด</h1>
    <p>ยินดีต้อนรับสู่ห้องสมุดออนไลน์ของเรา</p>
</header>

<!-- เพิ่มแถบเมนูที่นี่ -->
<nav>
    <a href="login.php">เข้าสู่ระบบ</a>
    <a href="login.php">ออกจากระบบ</a>
    <a href="register.php">สมัครสมาชิก</a>
</nav>

<div class="book-list">
    <?php
    // ตรวจสอบว่ามีข้อมูลหนังสือหรือไม่
    if ($result->num_rows > 0) {
        // แสดงข้อมูลหนังสือ
        while ($row = $result->fetch_assoc()) {
            // ตรวจสอบว่า image_url มีค่า
            $image_url = $row['image_url'] ? $row['image_url'] : 'images/book-placeholder.jpg';
            
            echo "
            <div class='book-item'>
                <img src='$image_url' alt='Book Cover'>
                <h3>" . $row['title'] . "</h3>
                <p>โดย: " . $row['author'] . "</p>
                <p>หมวดหมู่: " . $row['category'] . "</p>
                <a href='book_details.php?book_id=" . $row['book_id'] . "'>ดูรายละเอียด</a>
            </div>
            ";
        }
    } else {
        echo "<p>ไม่มีหนังสือในขณะนี้</p>";
    }
    ?>
</div>

</body>
</html>
