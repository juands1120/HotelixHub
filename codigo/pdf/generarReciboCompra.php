<?php
require_once __DIR__ . '/../librerias/dompdf/autoload.inc.php';
require_once '../config/conexionbd.php';
require_once '../services/sessionManager.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// =============================
// VALIDAR SESIÓN
// =============================
if (!isset($_SESSION['usuario'])) {
    die('Acceso no autorizado');
}
$id_usuario = $_SESSION['usuario']['id_usuario'];

// =============================
// CONSULTAR ÚLTIMA COMPRA
// =============================
$stmt = $pdo->prepare("SELECT * FROM compras WHERE id_usuario = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$id_usuario]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    die('No hay compras registradas.');
}

// =============================
// CONSULTAR DETALLES
// =============================
$stmtDetalle = $pdo->prepare("SELECT * FROM detalle_compras WHERE id_compra = ?");
$stmtDetalle->execute([$compra['id']]);
$detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

$detalleHtml = "";
$total = 0;
foreach ($detalles as $det) {
    $subtotal = $det['precio'] * $det['cantidad'];
    $total += $subtotal;
    $detalleHtml .= "<tr>
        <td>{$det['nombre_producto']}</td>
        <td>{$det['cantidad']}</td>
        <td>$ " . number_format($det['precio'], 0, ',', '.') . "</td>
        <td>$ " . number_format($subtotal, 0, ',', '.') . "</td>
    </tr>";
}

$iva = $total * 0.19;
$totalFinal = $total + $iva;

// =============================
// HTML DEL PDF
// =============================
$html = "
<style>
    body { font-family: DejaVu Sans, sans-serif; }
    h2 { color: #344584; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px;}
    th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
    th { background: #344584; color: #fff; }
    .totales { text-align: right; margin-top: 20px; }
</style>

<h2>Hotelix Hub - Recibo de Compra</h2>
<p><strong>Nombre:</strong> {$compra['nombre']}</p>
<p><strong>Email:</strong> {$compra['email']}</p>
<p><strong>Método de pago:</strong> {$compra['metodo_pago']}</p>
<p><strong>Fecha:</strong> {$compra['fecha']}</p>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        $detalleHtml
    </tbody>
</table>

<div class='totales'>
    <p><strong>Subtotal:</strong> $ " . number_format($total, 0, ',', '.') . "</p>
    <p><strong>IVA (19%):</strong> $ " . number_format($iva, 0, ',', '.') . "</p>
    <p><strong>Total:</strong> $ " . number_format($totalFinal, 0, ',', '.') . "</p>
</div>
";

// =============================
// GENERAR PDF
// =============================
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// FORZAR DESCARGA
$dompdf->stream("ReciboCompra.pdf", ["Attachment" => true]);
