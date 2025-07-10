<?php
require_once __DIR__ . '/../config/conexionbd.php';
session_start();

// Obtener notificaciones de compras
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'notificaciones') {
    try {
        $stmt = $pdo->query("CALL sp_obtener_notificaciones_compras()");
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notificaciones);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// Marcar compra como leída
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['accion']) && $_GET['accion'] === 'marcarLeida') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idCompra = $input['id'];

    try {
        $stmt = $pdo->prepare("CALL sp_marcar_compra_leida(:id)");
        $stmt->execute([':id' => $idCompra]);
        echo json_encode(['mensaje' => 'Compra marcada como leída']);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// Registrar compra (POST sin ?accion)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validaciones básicas
    $nombre = $input['nombre'] ?? '';
    $email = $input['email'] ?? '';
    $metodo = $input['metodo'] ?? '';
    $tarjeta = $input['tarjeta'] ?? null;
    $items = $input['items'] ?? [];

    if (!in_array($metodo, ['credito', 'debito', 'efectivo'])) {
        echo json_encode(['mensaje' => 'Método de pago inválido.']);
        exit();
    }

    if ($metodo !== 'efectivo' && (!preg_match('/^\d{13,16}$/', $tarjeta))) {
        echo json_encode(['mensaje' => 'Número de tarjeta inválido.']);
        exit();
    }

    if (empty($items)) {
        echo json_encode(['mensaje' => 'El carrito está vacío.']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Insertar en compras
        $stmt = $pdo->prepare("INSERT INTO compras (id_usuario, nombre, email, metodo_pago, numero_tarjeta, fecha)
                               VALUES (:id_usuario, :nombre, :email, :metodo_pago, :numero_tarjeta, NOW())");
        $stmt->execute([
            ':id_usuario' => $_SESSION['usuario']['id_usuario'],
            ':nombre' => $nombre,
            ':email' => $email,
            ':metodo_pago' => $metodo,
            ':numero_tarjeta' => ($metodo === 'efectivo' ? null : $tarjeta)
        ]);

        $idCompra = $pdo->lastInsertId();

        // Insertar detalles
        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_compras (id_compra, nombre_producto, cantidad, precio)
                                      VALUES (:id_compra, :nombre_producto, :cantidad, :precio)");
        foreach ($items as $item) {
            $stmtDetalle->execute([
                ':id_compra' => $idCompra,
                ':nombre_producto' => $item['nombre'],
                ':cantidad' => $item['cantidad'],
                ':precio' => $item['precio']
            ]);
        }

        $pdo->commit();

        echo json_encode(['mensaje' => 'Compra realizada con éxito.']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['mensaje' => 'Error al registrar la compra: ' . $e->getMessage()]);
    }
    exit();
}
