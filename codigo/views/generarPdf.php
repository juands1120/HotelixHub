<?php
session_start(); // Asegura acceso a la sesión

require_once '../librerias/dompdf/autoload.inc.php'; // Asegura que Dompdf esté cargado

use Dompdf\Dompdf;

if (!isset($_POST['datos'])) {
  die("No se recibió ninguna tabla.");
}

// Obtener la tabla HTML y el nombre del admin
$tablaHTML = $_POST['datos'];
$nombreAdmin = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Administrador';

// Instancia Dompdf
$dompdf = new Dompdf();

// HTML con diseño personalizado
$html = '
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    header {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo {
      width: 150px;
      margin-bottom: 10px;
    }
    h2 {
      margin: 5px 0;
      color: #003366;
    }
    p.subtitulo {
      margin: 0;
      font-size: 14px;
      color: #444;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #999;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
      color: #003366;
    }
    .footer {
      text-align: right;
      font-size: 10px;
      margin-top: 30px;
      color: #666;
    }
  </style>

  <header>
    <img src="../assets/img/imgHome/Logo Positivo.png" class="logo">
    <h2>HotelixHub</h2>
    <p class="subtitulo">Informe de empleados registrados</p>
  </header>

  ' . $tablaHTML . '

  <p class="footer">
    Generado por: ' . htmlspecialchars($nombreAdmin) . '<br>
    Fecha: ' . date('d/m/Y H:i') . '
  </p>
';

// Renderizar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("informe_empleados.pdf", ["Attachment" => false]);
?>
