<?php
$conexion = new mysqli("localhost", "root", "", "hotelixhub");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>