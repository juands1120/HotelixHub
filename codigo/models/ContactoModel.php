<?php
class ContactoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerIdUsuarioPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn(); // puede devolver null
    }

    public function guardarMensaje($datos) {
        $stmt = $this->pdo->prepare("INSERT INTO contacto 
            (id_usuario, nombre, telefono, email, ciudad, motivo, mensaje) 
            VALUES (:id_usuario, :nombre, :telefono, :email, :ciudad, :motivo, :mensaje)");

        return $stmt->execute([
            ':id_usuario' => $datos['id_usuario'],
            ':nombre' => $datos['nombre'],
            ':telefono' => $datos['telefono'],
            ':email' => $datos['email'],
            ':ciudad' => $datos['ciudad'],
            ':motivo' => $datos['motivo'],
            ':mensaje' => $datos['mensaje']
        ]);
    }
}
