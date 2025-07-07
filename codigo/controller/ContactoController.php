<?php
require_once __DIR__ . '/../models/ContactoModel.php';
require_once __DIR__ . '/../librerias/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../librerias/PHPMailer/SMTP.php';
require_once __DIR__ . '/../librerias/PHPMailer/Exception.php';

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
            $enviado = $this->enviarCorreoAdmin($datos);
            if ($enviado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo enviar el correo']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo guardar el mensaje en BDD']);
        }
    }

    private function enviarCorreoAdmin($datos) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuración segura SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hotelixhub@gmail.com'; // TU CORREO GMAIL
            $mail->Password = 'wpsq fael ebls zqyy';  // TU APP PASSWORD
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Opciones SSL - Producción (verifica certificado real del servidor Gmail)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false
                ]
            ];

            // Datos del correo
            $mail->setFrom('hotelixhub@gmail.com', 'Hotelix');
            $mail->addAddress('hotelixhub@gmail.com'); // puede ser tuyo para pruebas

            $mail->Subject = "Nuevo mensaje de contacto - " . $datos['motivo'];
            $mail->isHTML(false);
            $mail->Body = 
                "Nombre: {$datos['nombre']}\n".
                "Teléfono: {$datos['telefono']}\n".
                "Email: {$datos['email']}\n".
                "Ciudad: {$datos['ciudad']}\n".
                "Motivo: {$datos['motivo']}\n\n".
                "Mensaje:\n{$datos['mensaje']}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Guarda el error en log para producción
            file_put_contents(
                __DIR__ . '/../log_mail_error.txt',
                date('Y-m-d H:i:s') . " - Error: " . $mail->ErrorInfo . "\n",
                FILE_APPEND
            );
            return false;
        }
    }
}
