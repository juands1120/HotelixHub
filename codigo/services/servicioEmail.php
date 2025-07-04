<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../librerias/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../librerias/PHPMailer/SMTP.php';
require_once __DIR__ . '/../librerias/PHPMailer/Exception.php';

class ServicioEmail {

    public function enviarCorreoRecuperacion($correoDestino, $nombreUsuario, $linkRecuperacion) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hotelixhub@gmail.com';
            $mail->Password = 'wpsq fael ebls zqyy';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hotelixhub@gmail.com', 'HotelixHub');
            $mail->addAddress($correoDestino, $nombreUsuario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperación de contraseña - HotelixHub';
            $mail->Body = "Hola $nombreUsuario,<br><br>"
                . "Haz clic en el siguiente enlace para restablecer tu contraseña:<br>"
                . "<a href='$linkRecuperacion'>$linkRecuperacion</a><br><br>"
                . "Si no solicitaste este cambio, ignora este correo.<br><br>"
                . "Saludos,<br>HotelixHub";

            $mail->send();
            return true;

        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }

    //  NUEVA FUNCIÓN: Correo de bienvenida
    public function enviarCorreoBienvenida($correoDestino, $nombreUsuario) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hotelixhub@gmail.com';
            $mail->Password = 'wpsq fael ebls zqyy';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hotelixhub@gmail.com', 'HotelixHub');
            $mail->addAddress($correoDestino, $nombreUsuario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = '¡Bienvenido a HotelixHub!';
            $mail->Body = "Hola <strong>$nombreUsuario</strong>,<br><br>"
                . "¡Gracias por registrarte en <strong>HotelixHub</strong>!<br>"
                . "Tu cuenta ha sido creada exitosamente.<br><br>"
                . "Si tienes alguna duda, no dudes en escribirnos.<br><br>"
                . "Saludos cordiales,<br>"
                . "El equipo de HotelixHub";

            $mail->send();
            return true;

        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }
}
?>


