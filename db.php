<?php
// db.php
$servername = "localhost";
$username   = "root";
$password   = "root";
$dbname     = "mihn"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
