<?php
include("con_db.php");

if (isset($_POST["registro"])) {
    if (
        strlen($_POST["nombre"]) >= 1 &&
        strlen($_POST["apellido"]) >= 1 &&
        strlen($_POST["tipodocumento"]) >= 1 &&
        strlen($_POST["numeroDocumento"]) >= 1 &&
        strlen($_POST["numeroTelefono"]) >= 1 &&
        strlen($_POST["correo"]) >= 1 &&
        strlen($_POST["paisProcedencia"]) >= 1 &&
        strlen($_POST["contraseña"]) >= 1
    ) {
        $nombre = trim($_POST["nombre"]);
        $apellido = trim($_POST["apellido"]);
        $tipodocumento = trim($_POST["tipodocumento"]);
        $numeroDocumento = trim($_POST["numeroDocumento"]);
        $numeroTelefono = trim($_POST["numeroTelefono"]);
        $correo = trim($_POST["correo"]);
        $paisProcedencia = trim($_POST["paisProcedencia"]);
        $contraseña = password_hash(trim($_POST["contraseña"]), PASSWORD_BCRYPT);

        // Validar si el correo ya existe
        $verificarCorreo = $conexion->prepare("SELECT correo FROM usuario WHERE correo = ?");
        $verificarCorreo->bind_param("s", $correo);
        $verificarCorreo->execute();
        $verificarCorreo->store_result();

        if ($verificarCorreo->num_rows > 0) {
            echo '<h3 class="bad">Este correo ya está registrado. Intenta con otro.</h3>';
        } else {
            // Insertar nuevo usuario
            $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, tipodocumento, numeroDocumento, numeroTelefono, correo, paisProcedencia, contraseña) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $nombre, $apellido, $tipodocumento, $numeroDocumento, $numeroTelefono, $correo, $paisProcedencia, $contraseña);

            if ($stmt->execute()) {
                echo '<h3 class="ok">Usuario registrado correctamente</h3>';
            } else {
                echo '<h3 class="bad">Error al registrar el usuario</h3>';
            }

            $stmt->close();
        }

        $verificarCorreo->close();
    } else {
        echo '<h3 class="bad">Error: Por favor, completa todos los campos</h3>';
    }
}
?>
