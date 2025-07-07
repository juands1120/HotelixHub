<?php
require_once __DIR__ . '/../config/conexionbd.php';

class empleadoRegistro {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($rol, $nombre, $apellido, $tipoDocumento, $numeroDocumento,
                            $numeroTelefono, $paisProcedencia, $email, $password,
                            $reset_token, $token_expires, $estado, $direccion)
    {
        $stmt = $this->pdo->prepare("CALL sp_registrar_empleado(
            :rol, :nombre, :apellido, :tipoDocumento, :numeroDocumento,
            :numeroTelefono, :paisProcedencia, :email, :password,
            :reset_token, :token_expires, :estado, :direccion
        )");

        return $stmt->execute([
            ':rol' => $rol,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':tipoDocumento' => $tipoDocumento,
            ':numeroDocumento' => $numeroDocumento,
            ':numeroTelefono' => $numeroTelefono,
            ':paisProcedencia' => $paisProcedencia, 
            ':email' => $email,
            ':password' => $password,
            ':reset_token' => $reset_token,
            ':token_expires' => $token_expires,
            ':estado' => $estado,
            ':direccion' => $direccion
        ]);
    }

    public function obtenerEmpleadosPorRol($rol) {
    $stmt = $this->pdo->prepare("CALL sp_obtener_empleados_por_rol(:rol)");
    $stmt->bindParam(':rol', $rol);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Agrega estos métodos a tu clase empleadoRegistro
public function actualizarEmpleado($data) {
    $stmt = $this->pdo->prepare("CALL sp_actualizar_empleado(
        :id, :nombre, :apellido, :tipoDocumento, :numeroDocumento, 
        :numeroTelefono, :email, :direccion, :usu_idrol, :estado
    )");
    
    return $stmt->execute($data);
}

public function eliminarEmpleado($idEmpleado, $idUsuarioEliminador) {
    $stmt = $this->pdo->prepare("CALL sp_eliminar_empleado(:idEmpleado, :idEliminador)");
    return $stmt->execute([
        'idEmpleado' => $idEmpleado,
        'idEliminador' => $idUsuarioEliminador
    ]);
}



public function findByDocumento($numeroDocumento) {
    $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE numeroDocumento = :doc");
    $stmt->execute(['doc' => $numeroDocumento]);
    return $stmt->fetch();
}

public function findByTelefono($numeroTelefono) {
    $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE numeroTelefono = :tel");
    $stmt->execute(['tel' => $numeroTelefono]);
    return $stmt->fetch();
}

public function findByDireccion($direccion) {
    $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE direccion = :dir");
    $stmt->execute(['dir' => $direccion]);
    return $stmt->fetch();
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