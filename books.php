<?php
include "db_connect.php";

$sql = "SELECT * FROM books";
$result = $conn->query($sql);

echo "<h2>รายการหนังสือ</h2>";
echo "<table border='1'>";
echo "<tr><th>รหัส</th><th>ชื่อหนังสือ</th><th>ผู้แต่ง</th><th>หมวดหมู่</th><th>สถานะ</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["book_id"] . "</td>";
    echo "<td>" . $row["title"] . "</td>";
    echo "<td>" . $row["author"] . "</td>";
    echo "<td>" . $row["category"] . "</td>";
    echo "<td>" . ($row["status"] == "available" ? "ว่าง" : "ถูกยืม") . "</td>";
    echo "</tr>";
}
echo "</table>";
?>