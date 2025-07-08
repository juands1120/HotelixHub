<?php
date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../librerias/dompdf/autoload.inc.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../services/sessionManager.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener filtro de estado
$estadoFiltro = $_POST['estadoFiltro'] ?? '';
$nombreUsuario = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
$fechaReporte = date('d/m/Y H:i');

// Consulta SQL
$sql = "
SELECT u.nombre, u.apellido, u.tipoDocumento, u.numeroDocumento, u.numeroTelefono,
       u.paisProcedencia, u.email, COALESCE(r.estado, 'Sin reserva') AS estadoReserva
FROM usuarios u
LEFT JOIN reserva r ON u.id_usuario = r.id_usuario
WHERE u.usu_idrol = 2
";

// Agregar filtro por estado si se seleccionó
if (!empty($estadoFiltro)) {
    $sql .= " AND COALESCE(r.estado, 'Sin reserva') = :estadoFiltro";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':estadoFiltro', $estadoFiltro);
} else {
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$clientes = $stmt->fetchAll();

if (!$clientes) {
    die("No se encontraron clientes para ese estado.");
}

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// HTML del PDF
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
  <h2>Reporte de Clientes ' . ($estadoFiltro ? ' - Estado: ' . htmlspecialchars($estadoFiltro) : '') . '</h2>
  <p>Generado por: <strong>' . htmlspecialchars($nombreUsuario) . '</strong></p>
  <p>Fecha: <strong>' . $fechaReporte . '</strong></p>
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Tipo Documento</th>
        <th>Número Documento</th>
        <th>Teléfono</th>
        <th>País</th>
        <th>Email</th>
        <th>Estado Reserva</th>
      </tr>
    </thead><tbody>';

foreach ($clientes as $cliente) {
    $html .= '<tr>
      <td>' . htmlspecialchars($cliente['nombre']) . '</td>
      <td>' . htmlspecialchars($cliente['apellido']) . '</td>
      <td>' . htmlspecialchars($cliente['tipoDocumento']) . '</td>
      <td>' . htmlspecialchars($cliente['numeroDocumento']) . '</td>
      <td>' . htmlspecialchars($cliente['numeroTelefono']) . '</td>
      <td>' . htmlspecialchars($cliente['paisProcedencia']) . '</td>
      <td>' . htmlspecialchars($cliente['email']) . '</td>
      <td>' . htmlspecialchars($cliente['estadoReserva']) . '</td>
    </tr>';
}

$html .= '</tbody></table>
<p style="text-align:center; margin-top:30px;">Generado automáticamente por HotelixHub</p>
</body></html>';

// Generar y descargar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('reporte_clientes_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
exit;
