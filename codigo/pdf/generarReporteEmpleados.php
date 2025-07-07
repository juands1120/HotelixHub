<?php
date_default_timezone_set('America/Bogota');


require_once __DIR__ . '/../librerias/dompdf/autoload.inc.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/sessionManager.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener filtro y datos del usuario
$rolFiltro = $_POST['rolFiltro'] ?? '';
$nombreUsuario = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
$fechaReporte = date('d/m/Y H:i');

// Consulta SQL
$sql = "SELECT u.nombre, u.apellido, u.tipoDocumento, u.numeroDocumento, u.email,
               r.rol_nombre AS nombreRol, u.numeroTelefono, u.direccion, u.estado
        FROM usuarios u
        JOIN rol r ON u.usu_idrol = r.id_rol";


if (!empty($rolFiltro)) {
    $sql .= " WHERE r.rol_nombre = :rolFiltro";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rolFiltro', $rolFiltro);
} else {
    // Mostrar todos los empleados con roles conocidos
    $sql .= " WHERE r.rol_nombre IN ('Recepcionista', 'Cocinero', 'Camarero')";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$empleados = $stmt->fetchAll();

if (!$empleados) {
    die("No se encontraron empleados para ese rol.");
}

// Iniciar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// HTML para el PDF
$html = '
<html><head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { text-align: center; }
    p { margin: 5px 0; text-align: right; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background-color: #f2f2f2; }
  </style>
</head><body>
  <h2>Reporte de Empleados ' . ($rolFiltro ? ' - ' . htmlspecialchars($rolFiltro) : '') . '</h2>
  <p>Generado por: <strong>' . htmlspecialchars($nombreUsuario) . '</strong></p>
  <p>Fecha: <strong>' . $fechaReporte . '</strong></p>
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Tipo Documento</th>
        <th>Número Documento</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Teléfono</th>
        <th>Dirección</th>
        <th>Estado</th>
      </tr>
    </thead><tbody>';

foreach ($empleados as $emp) {
    $html .= '<tr>
      <td>' . htmlspecialchars($emp['nombre']) . '</td>
      <td>' . htmlspecialchars($emp['apellido']) . '</td>
      <td>' . htmlspecialchars($emp['tipoDocumento']) . '</td>
      <td>' . htmlspecialchars($emp['numeroDocumento']) . '</td>
      <td>' . htmlspecialchars($emp['email']) . '</td>
      <td>' . htmlspecialchars($emp['nombreRol']) . '</td>
      <td>' . htmlspecialchars($emp['numeroTelefono']) . '</td>
      <td>' . htmlspecialchars($emp['direccion']) . '</td>
      <td>' . htmlspecialchars($emp['estado']) . '</td>
    </tr>';
}

$html .= '</tbody></table>
<p style="text-align:center; margin-top:30px;">Generado automáticamente por HotelixHub</p>
</body></html>';

// Cargar el HTML en Dompdf
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Horizontal
$dompdf->render();

// Descargar el PDF directamente
$dompdf->stream('reporte_empleados_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
exit;

