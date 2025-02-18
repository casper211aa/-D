<?php
include "db_connect.php"; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $category = $_POST["category"];
    $status = $_POST["status"];
    $image_url = $_POST["image_url"]; // รับค่าของ URL รูปภาพ

    // เพิ่มข้อมูลหนังสือลงในฐานข้อมูล
    $sql = "INSERT INTO books (title, author, category, status, image_url) 
            VALUES ('$title', '$author', '$category', '$status', '$image_url')"; // เพิ่ม image_url ลงในคำสั่ง SQL

    if ($conn->query($sql) === TRUE) {
        // ถ้าสำเร็จจะกลับไปที่หน้าแรก
        header("Location: index.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการเพิ่มหนังสือ: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหนังสือ - ระบบห้องสมุด</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        form {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            width: 320px;
            margin: 100px auto;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        h2 {
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
        input, select {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<form method="post">
    <h2>เพิ่มหนังสือใหม่</h2>
    
    <?php if (!empty($error)) { echo "<p class='error-message'>$error</p>"; } ?>
    
    ชื่อหนังสือ: <input type="text" name="title" required><br><br>
    ผู้เขียน: <input type="text" name="author" required><br><br>
    หมวดหมู่: <input type="text" name="category"><br><br>
    
    สถานะ:
    <select name="status" required>
        <option value="available">พร้อมให้ยืม</option>
        <option value="borrowed">ถูกยืมแล้ว</option>
    </select><br><br>
    
    URL รูปภาพ: <input type="text" name="image_url"><br><br> <!-- ฟิลด์ URL สำหรับรูปภาพ -->
    
    <button type="submit">เพิ่มหนังสือ</button>
</form>

</body>
</html>
