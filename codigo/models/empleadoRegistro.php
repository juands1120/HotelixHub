<?php
require_once __DIR__ . '/../config/conexionbd.php';

class empleadoRegistro {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($usu_idrol, $nombre, $apellido, $tipoDocumento, $numeroDocumento, $numeroTelefono, $direccion, $email, $estado, $password, $reset_token = null, $token_expires = null) {
        $sql = "CALL sp_registrar_empleado(:usu_idrol, :nombre, :apellido, :tipoDocumento, :numeroDocumento, :numeroTelefono, :direccion, :email, :estado, :password, :reset_token, :token_expires)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usu_idrol', $usu_idrol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':tipoDocumento', $tipoDocumento);
        $stmt->bindParam(':numeroDocumento', $numeroDocumento);
        $stmt->bindParam(':numeroTelefono', $numeroTelefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':reset_token', $reset_token);
        $stmt->bindParam(':token_expires', $token_expires);
        return $stmt->execute();
    }
    //metodo para buscar solo empleados
    public function obtenerEmpleados() {
    $stmt = $this->pdo->query("CALL sp_obtener_empleados()");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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