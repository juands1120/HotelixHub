<?php
require_once __DIR__ . '/../librerias/dompdf/autoload.inc.php';
require_once __DIR__ . '/../config/conexionbd.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ====== CONSULTAR PRODUCTOS ======
$stmt = $pdo->query("
    SELECT p.*, c.nombre_categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ====== INICIAR DOMPDF ======
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

// ====== HTML DEL PDF ======
$html = '
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; }
    .contenedor { padding: 30px; max-width: 900px; margin: auto; border: 1px solid #ccc; }
    .encabezado { text-align: center; margin-bottom: 25px; }
    .encabezado h1 { color: #0066cc; font-size: 22px; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #0066cc; color: #fff; }
    .pie { text-align: center; font-size: 11px; color: #777; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 30px; }
  </style>
</head>
<body>
  <div class="contenedor">
    <div class="encabezado">
      <h1>Reporte de Productos</h1>
      <p>Emitido el: ' . date('Y-m-d') . '</p>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Descripción</th>
          <th>Stock</th>
          <th>Categoría</th>
        </tr>
      </thead>
      <tbody>';
foreach($productos as $p) {
    $html .= '<tr>
                <td>' . htmlspecialchars($p['id']) . '</td>
                <td>' . htmlspecialchars($p['nombre']) . '</td>
                <td>$' . number_format($p['precio'], 0, ',', '.') . '</td>
                <td>' . htmlspecialchars($p['descripcion']) . '</td>
                <td>' . htmlspecialchars($p['stock']) . '</td>
                <td>' . htmlspecialchars($p['nombre_categoria'] ?? 'Sin categoría') . '</td>
              </tr>';
}
$html .= '</tbody>
    </table>

    <div class="pie">
      Este reporte fue generado automáticamente por HotelixHub. ¡Gracias por confiar en nosotros!
    </div>
  </div>
</body>
</html>';

// ====== RENDERIZAR Y DESCARGAR PDF ======
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_productos_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
exit;
?>
