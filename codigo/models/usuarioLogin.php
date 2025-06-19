<?php
require_once __DIR__ . '/../config/conexionbd.php';

class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($email, $password) {
        // Llamar al procedimiento almacenado
        $stmt = $this->pdo->prepare("CALL sp_login_usuario(:email)");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Obtener el resultado
        $user = $stmt->fetch();

        // Verificar contraseña
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
