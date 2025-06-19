<?php
session_start();
session_unset();
session_destroy();

// Redirige al login con un mensaje opcional
header("Location: ../views/login.php");
exit();

