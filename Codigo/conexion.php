<?php
$host = "localhost";
$user = "root";
$pass = "123456";
$db = "hotelixhub";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
