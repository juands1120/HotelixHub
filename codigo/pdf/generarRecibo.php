<?php
require_once __DIR__ . '/../librerias/dompdf/autoload.inc.php';
require_once __DIR__ . '/../config/conexionbd.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
    die("Falta el ID de la reserva");
}

$idReserva = (int) $_GET['id'];

// ====== CONSULTA RESERVA ======
$stmt = $pdo->prepare("
    SELECT r.*, u.nombre AS cliente_nombre, u.apellido, u.email, u.numeroTelefono,
           h.nombre AS habitacion_nombre, h.tipoHabitacion, h.piso, h.precio, h.serviciosIncluidos
    FROM reserva r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    JOIN habitacion h ON r.id_habitacion = h.id_habitacion
    WHERE r.id_reserva = :id
");
$stmt->execute([':id' => $idReserva]);
$reserva = $stmt->fetch();

if (!$reserva) {
    die("Reserva no encontrada");
}

// ====== CÁLCULOS ======
$subtotal = $reserva['precio_total'] / 1.19;
$iva = $reserva['precio_total'] - $subtotal;
$fechaActual = date('Y-m-d');
$fechaHoraReserva = date('Y-m-d H:i', strtotime($reserva['fecha_reserva']));
$reciboNumero = 'R-' . date('Y') . '-' . str_pad($idReserva, 4, '0', STR_PAD_LEFT);

// ====== LOGO DEL HOTEL ======
$logoPath = realpath(__DIR__ . '/../assets/img/imgHome/Logo principal.png');
$logoURL = $logoPath ? 'file:///' . str_replace('\\', '/', $logoPath) : '';

// ====== SERVICIOS ======
$servicios = $reserva['servicios_adicionales'] ? json_decode($reserva['servicios_adicionales'], true) : [];
$serviciosIncluidos = explode(',', $reserva['serviciosIncluidos']);

// ====== INICIAR DOMPDF ======
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// ====== HTML PDF SIN IMAGEN DE HABITACIÓN ======
$html = '
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; margin: 0; }
    .contenedor { padding: 40px; max-width: 800px; margin: auto; border: 1px solid #ccc; }
    .encabezado { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .logo { width: 160px; }
    .titulo { text-align: right; }
    .titulo h1 { color: #0066cc; font-size: 22px; margin: 0; }
    .seccion { margin-bottom: 25px; }
    .seccion h3 { color: #0066cc; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 10px; }
    .dato { margin-bottom: 8px; }
    .dato strong { display: inline-block; width: 160px; }
    .habitacion-info p { margin: 4px 0; }
    .pie { text-align: center; font-size: 11px; color: #777; border-top: 1px solid #ccc; padding-top: 15px; margin-top: 40px; }
  </style>
</head>
<body>
  <div class="contenedor">

    <div class="encabezado" style="justify-content: flex-end;">
      <div class="titulo">
        <h1>Recibo de Reserva</h1>
        <p>Número: <strong>' . $reciboNumero . '</strong></p>
        <p>Emitido el: ' . $fechaActual . '</p>
      </div>
    </div>


    <div class="seccion">
      <h3>Datos del Cliente</h3>
      <p class="dato"><strong>Nombre:</strong> ' . htmlspecialchars($reserva['cliente_nombre']) . ' ' . htmlspecialchars($reserva['apellido']) . '</p>
      <p class="dato"><strong>Email:</strong> ' . htmlspecialchars($reserva['email']) . '</p>
      <p class="dato"><strong>Teléfono:</strong> ' . htmlspecialchars($reserva['numeroTelefono']) . '</p>
    </div>

    <div class="seccion">
      <h3>Habitación</h3>
      <div class="habitacion-info">
        <p><strong>Nombre:</strong> ' . htmlspecialchars($reserva['habitacion_nombre']) . '</p>
        <p><strong>Tipo:</strong> ' . htmlspecialchars($reserva['tipoHabitacion']) . '</p>
        <p><strong>Piso:</strong> ' . htmlspecialchars($reserva['piso']) . '</p>
        <p><strong>Precio base:</strong> $' . number_format($reserva['precio'], 0, ',', '.') . ' COP</p>
        <p><strong>Incluye:</strong> ' . implode(', ', array_map('htmlspecialchars', $serviciosIncluidos)) . '</p>
      </div>
    </div>

    <div class="seccion">
      <h3>Información de la Reserva</h3>
      <p class="dato"><strong>Entrada:</strong> ' . htmlspecialchars($reserva['fecha_entrada']) . '</p>
      <p class="dato"><strong>Salida:</strong> ' . htmlspecialchars($reserva['fecha_salida']) . '</p>
      <p class="dato"><strong>Fecha de Reserva:</strong> ' . $fechaHoraReserva . '</p>
      <p class="dato"><strong>Subtotal:</strong> $' . number_format($subtotal, 0, ',', '.') . ' COP</p>
      <p class="dato"><strong>IVA (19%):</strong> $' . number_format($iva, 0, ',', '.') . ' COP</p>
      <p class="dato"><strong>Total a Pagar:</strong> $' . number_format($reserva['precio_total'], 0, ',', '.') . ' COP</p>
    </div>

    <div class="seccion">
      <h3>Servicios Adicionales</h3>
      <ul>';
foreach ($servicios as $servicio) {
    $html .= '<li>' . htmlspecialchars($servicio) . '</li>';
}
$html .= '</ul>
    </div>

    <div class="pie">
      Este recibo fue generado automáticamente por HotelixHub. ¡Gracias por preferirnos!
    </div>

  </div>
</body>
</html>';

// ========== RENDERIZAR PDF ==========
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("recibo_reserva_" . $idReserva . ".pdf", ["Attachment" => true]);
exit;
