<?php
session_start();
include 'db_connect.php'; // เชื่อมต่อกับฐานข้อมูล

$error = ''; // ตัวแปรสำหรับแสดงข้อความข้อผิดพลาด

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ใช้ prepared statement เพื่อป้องกัน SQL injection
    $stmt = $conn->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->bind_param("s", $email); // ผูกค่าของตัวแปร $email กับ placeholder
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // ตรวจสอบรหัสผ่านที่กรอกกับรหัสผ่านที่เก็บในฐานข้อมูล (ใช้ password_verify)
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $email; // ตั้ง session
            header("Location: index.php"); // เปลี่ยนไปหน้า index.php เมื่อเข้าสู่ระบบสำเร็จ
            exit();
        } else {
            $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง"; // ถ้ารหัสผิด
        }
    } else {
        $error = "ไม่พบอีเมลนี้ในระบบ"; // ถ้าไม่พบอีเมล
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
        /* CSS ของฟอร์ม */
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        video.background-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 15px;
            width: 320px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease-in-out;
        }

        .login-container:hover {
            transform: scale(1.05);
        }

        h2 {
            margin-bottom: 15px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #ff5733;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #e74c3c;
        }

        .register-link {
            display: block;
            margin-top: 10px;
            color: #3498db;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<video class="background-video" autoplay loop muted>
    <source src="images/dd.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="login-container">
    <h2>เข้าสู่ระบบ</h2>
    <?php if (!empty($error)) { echo "<p class='error-message'>$error</p>"; } ?>
    <form method="post">
        อีเมล: <input type="email" name="email" required><br>
        รหัสผ่าน: <input type="password" name="password" required><br><br>
        <button type="submit">เข้าสู่ระบบ</button>
    </form>
    <a class="register-link" href="register.php">ยังไม่มีบัญชีใช่ไหม? ลงทะเบียนที่นี่</a>
</div>

</body>
</html>
