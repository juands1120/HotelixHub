<?php
session_start(); // Activa la sesión

require_once '../librerias/dompdf/autoload.inc.php'; // Carga Dompdf

use Dompdf\Dompdf;

// Verificar si se recibió la tabla HTML
if (!isset($_POST['datos'])) {
    die("No se recibió ninguna tabla.");
}

// Recuperar la tabla enviada
$tablaHTML = $_POST['datos'];

// Obtener nombre del administrador (puede ser string o array)
$usuario = $_SESSION['usuario'] ?? 'Administrador';
$nombreAdmin = is_array($usuario) && isset($usuario['nombre'])
    ? $usuario['nombre']
    : (is_string($usuario) ? $usuario : 'Administrador');

// Fecha actual
$fechaGeneracion = date('d/m/Y H:i');

// Crear instancia Dompdf
$dompdf = new Dompdf();

// Convertir ruta relativa del logo a ruta absoluta (recomendado por Dompdf)
$logoPath = realpath('../assets/img/imgHome/Logo Positivo.png');

// Si no existe el logo, omitir la imagen
$logoHTML = file_exists($logoPath)
    ? '<img src="file:///' . str_replace('\\', '/', $logoPath) . '" class="logo">'
    : '<div style="color:red;">[Logo no disponible]</div>';

// Plantilla HTML para el PDF
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
    ' . $logoHTML . '
    <h2>HotelixHub</h2>
    <p class="subtitulo">Informe de empleados registrados</p>
  </header>

  ' . $tablaHTML . '

  <p class="footer">
    Generado por: ' . htmlspecialchars($nombreAdmin) . '<br>
    Fecha: ' . htmlspecialchars($fechaGeneracion) . '
  </p>
';

// Generar el PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Mostrar en el navegador sin descargar
$dompdf->stream("informe_empleados.pdf", ["Attachment" => false]);
?>