<?php
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // ตรวจสอบว่าอีเมลมีอยู่แล้วหรือไม่
    $check_email = "SELECT * FROM members WHERE email = '$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        // ถ้าอีเมลซ้ำ แจ้งเตือน
        $error = "อีเมลนี้ถูกใช้ไปแล้ว กรุณาใช้เมลอื่น!";
    } else {
        // ถ้าอีเมลไม่ซ้ำ ให้เพิ่มเข้าไปในฐานข้อมูล
        $sql = "INSERT INTO members (name, email, password) VALUES ('$name', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $error = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <style>
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

        form {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            width: 320px;
            margin: 100px auto;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease-in-out;
        }

        form:hover {
            transform: scale(1.05);
        }

        .error-message {
            color: red;
            font-weight: bold;
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
    <source src="images/ddd.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<form method="post">
    <h2>สมัครสมาชิก</h2>
    <?php if (!empty($error)) { echo "<p class='error-message'>$error</p>"; } ?>
    ชื่อ: <input type="text" name="name" required><br><br>
    อีเมล: <input type="email" name="email" required><br><br>
    รหัสผ่าน: <input type="password" name="password" required><br><br>
    <button type="submit">สมัครสมาชิก</button>
    <a class="register-link" href="login.php">มีบัญชีแล้วใช่ไหม? เข้าสู่ระบบที่นี่</a>
</form>

</body>
</html>
