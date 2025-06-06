<?php
require_once __DIR__ . '/../conexion/conexionbd.php';
require_once __DIR__ . '/../modelo/usuarioRegistro.php';
require_once __DIR__ . '/servicioEmail.php';

class PasswordController {
    private $userModel;
    private $emailService;

    public function __construct($userModel, $emailService) {
        $this->userModel = $userModel;
        $this->emailService = $emailService;
    }

    public function forgotPasswordForm() {
        include_once __DIR__. '/../vista/dash/verificarCorreoToken.php';
    }

    public function sendResetLink() {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $error = "El correo ingresado no es válido.";
            include __DIR__.'/../vista/dash/verificarCorreoToken.php';
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if ($user) {
            $token   = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $this->userModel->updateResetToken($user['id_usuario'], $token, $expires);
            $resetLink = "http://localhost/Codigo/vista/dash/nuevaContraseña.php?token=" . $token;

            $enviado = $this->emailService->enviarCorreoRecuperacion($email, $user['nombre'], $resetLink);

            if ($enviado === true) {
                $message = "Se ha enviado un enlace para recuperar tu contraseña al correo indicado.";
                include __DIR__. '/../vista/dash/mensajeCorreo.php';
            } else {
                $error = "No se pudo enviar el correo. Error: $enviado";
                include __DIR__.'/../vista/dash/verificarCorreoToken.php';
            }
        } else {
            $error = "El correo no está registrado en el sistema.";
            include __DIR__.'/../vista/dash/verificarCorreoToken.php';
        }
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userModel = new UsuarioRegistro($pdo);
$emailService = new ServicioEmail();
$controller = new PasswordController($userModel, $emailService);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->sendResetLink();
} else {
    $controller->forgotPasswordForm();
}
?>



