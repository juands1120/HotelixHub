<?php
require_once __DIR__ . '/../config/conexionbd.php';

class UsuarioRegistro {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($usu_idrol, $nombre, $apellido, $tipodocumento, $numeroDocumento, $numeroTelefono, $paisProcedencia, $email, $password, $reset_token = null, $token_expires = null) {
        $sql = "CALL sp_registrar_usuario(:usu_idrol, :nombre, :apellido, :tipodocumento, :numeroDocumento, :numeroTelefono, :paisProcedencia, :email, :password, :reset_token, :token_expires)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usu_idrol', $usu_idrol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':tipodocumento', $tipodocumento);
        $stmt->bindParam(':numeroDocumento', $numeroDocumento);
        $stmt->bindParam(':numeroTelefono', $numeroTelefono);
        $stmt->bindParam(':paisProcedencia', $paisProcedencia);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':reset_token', $reset_token);
        $stmt->bindParam(':token_expires', $token_expires);
        return $stmt->execute();
    }

        // Método para buscar usuario por correo
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();  // devuelve el usuario o false si no existe
    }

    // Método para actualizar el token de reseteo
    public function updateResetToken($userId, $token, $expires) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET reset_token = :token, token_expires = :expires WHERE id_usuario = :id");
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'id' => $userId
        ]);
    }

    public function findByToken($token) {
    $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE reset_token = :token AND token_expires > NOW()");
    $stmt->execute(['token' => $token]);
    return $stmt->fetch();
    }

    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE usuarios SET password = :password WHERE id_usuario = :id");
        return $stmt->execute(['password' => $hashed, 'id' => $userId]);
    }

    public function clearResetToken($userId) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET reset_token = NULL, token_expires = NULL WHERE id_usuario = :id");
        return $stmt->execute(['id' => $userId]);
    }

}