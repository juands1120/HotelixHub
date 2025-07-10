<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
echo json_encode(isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null);
?>
