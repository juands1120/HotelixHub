<?php
require_once __DIR__ . '/../models/ContactoModel.php';

class ContactoController {
    private $model;

    public function __construct($pdo) {
        $this->model = new ContactoModel($pdo);
    }

    public function manejarSolicitud() {
        $json = file_get_contents("php://input");
        $datos = json_decode($json, true);

        if (!$datos) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            return;
        }

        $idUsuario = $this->model->obtenerIdUsuarioPorEmail($datos['email']);
        $datos['id_usuario'] = $idUsuario ?: null;

        $guardado = $this->model->guardarMensaje($datos);

        if ($guardado) {
            // Enviar notificación por correo
            $this->enviarCorreoAdmin($datos);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo guardar']);
        }
    }

    private function enviarCorreoAdmin($datos) {
        $to = "tu_correo_admin@ejemplo.com";  // CAMBIA ESTE
        $subject = "Nuevo mensaje de contacto - " . $datos['motivo'];
        $message = "
            Nombre: {$datos['nombre']}\n
            Teléfono: {$datos['telefono']}\n
            Email: {$datos['email']}\n
            Ciudad: {$datos['ciudad']}\n
            Motivo: {$datos['motivo']}\n
            Mensaje:\n
            {$datos['mensaje']}
        ";

        $headers = "From: contacto@hotelix.com";
        mail($to, $subject, $message, $headers);
    }
}
