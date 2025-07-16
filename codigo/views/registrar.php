<?php

require_once __DIR__ . '/../services/sessionManager.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario'])) {
    // Ya hay sesión activa, redirigir al dashboard correspondiente
    switch ($_SESSION['usuario']['usu_idrol']) {
        case 1:
            header('Location: dashAdmin.php');
            break;
        case 2:
            header('Location: dashCliente.php');
            break;
        case 3:
            header('Location: dashAdmin.php');
            break;
        case 4:
            header('Location: dashAdmin.php');
            break;
        case 5:
            header('Location: dashAdmin.php');
            break;
        default:
            header('Location: ../login.php');
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - HotelixHub</title>
    <link rel="stylesheet" href="../assets/css/registrar.css">

</head>
<body>
    <div class="header">
        <a href="home.php"><img src="../assets/img/imghome/Logo Principal.png" alt="HotelixHub"  class="logo"></a>
    </div>
    <div class="container">
        <div class="login-section">
            <div class="login-text">
                <h1>Bienvenido</h1>
                <p>Únete a nuestra comunidad y disfruta de nuestros servicios exclusivos.</p>
                <button id="iniciarSesionBtn" class="btn1">Iniciar sesión </button>
            </div>
        </div>
        <div class="register-section">
            <h2>Registro</h2>
            <form id="registro" method="POST" action="../controller/guardarRegistro.php">
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese su Nombre Completo" maxlength="30" required>  
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su Apellido Completo" maxlength="30" required> 
                <select id="tipodocumento" name="tipodocumento" required>
                    <option value="">Seleccione Tipo de Documento</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="PA">Pasaporte</option> 
                </select>
                <input type="text" id="numeroDocumento" name="numeroDocumento" placeholder="Ingrese su Número de Documento" required>
                <input type="tel" id="numeroTelefono" name="numeroTelefono" placeholder="Ingrese su Número de Celular" required>
                <input id="paisProcedencia" name="paisProcedencia" placeholder="Ingrese su País de Procedencia" required>
                <input type="email" id="email" name="email" placeholder="Ingrese su email" required>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                <button type="submit" name="registrarse" class="btn">Registrarse</button>
            </form>
        </div>
    </div>

<!-- Modal -->
<div id="myModal" class="modal" tabindex="-1">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <div class="modal-header" id="modalHeader"></div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<div id="modalDuplicado" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalDuplicado').style.display='none'">&times;</span>
        <div class="modal-header">¡Error!</div>
        <div class="modal-body">
            El correo, número de documento o teléfono ya están registrados.
        </div>
    </div>
</div>




<script>
// Redirigir al inicio de sesión
document.getElementById('iniciarSesionBtn').addEventListener('click', function () {
    window.location.href = 'login.php';
});

document.addEventListener('DOMContentLoaded', function () {

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['registro_error'])) {
    $mensaje = $_SESSION['registro_error'];
    echo "showModal('Error de Registro', '" . addslashes($mensaje) . "');";
    unset($_SESSION['registro_error']);
}
?>

    const form = document.getElementById('registrarse');

    // Validación de formato en tiempo real

    // Validación de solo letras en nombre (máx 30)
    document.getElementById('nombre').addEventListener('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').slice(0, 30);
    });

    // Validación de solo letras en apellido (máx 30)
    document.getElementById('apellido').addEventListener('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').slice(0, 30);
    });

    // Validación para número de documento
    document.getElementById('numeroDocumento').addEventListener('input', function () {
        const tipoDoc = document.getElementById('tipodocumento').value;
        if (tipoDoc === 'PA') {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 12);
        } else {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
        }
    });

    // Validación para número de teléfono
    document.getElementById('numeroTelefono').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9+\s]/g, '').slice(0, 15);
    });

    // Validación de solo letras en país
    document.getElementById('paisProcedencia').addEventListener('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            const nombre = document.getElementById('nombre').value.trim();
            const apellido = document.getElementById('apellido').value.trim();
            const tipodocumento = document.getElementById('tipodocumento').value.trim();
            const numeroDocumento = document.getElementById('numeroDocumento').value.trim();
            const numeroTelefono = document.getElementById('numeroTelefono').value.trim();
            const email = document.getElementById('email').value.trim();
            const paisProcedencia = document.getElementById('paisProcedencia').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validación de campos vacíos
            if (
                nombre === "" ||
                apellido === "" ||
                tipodocumento === "" ||
                numeroDocumento === "" ||
                numeroTelefono === "" ||
                email === "" ||
                paisProcedencia === "" ||
                password === ""
            ) {
                e.preventDefault();
                showModal("Error de Registro", "Por favor completa todos los campos requeridos.");
                return;
            }

            // Validación de longitud de nombre y apellido
            if (nombre.length > 30 || apellido.length > 30) {
                e.preventDefault();
                showModal("Error de Registro", "Nombre y Apellido no pueden tener más de 30 caracteres.");
                return;
            }

            // Validación de correo
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showModal("Error de Registro", "Por favor, ingrese un correo válido con extensión, por ejemplo: juan@gmail.com");
                return;
            }

            // Validación de contraseña
            if (password.length < 8) {
                e.preventDefault();
                showModal("Error de Registro", "La contraseña debe tener al menos 8 caracteres.");
                return;
            }

            // Si pasa todas las validaciones, el formulario se envía
        });
    }

    function showModal(titulo, mensaje) {
        const modal = document.getElementById("myModal");
        const modalHeader = document.getElementById("modalHeader");
        const modalBody = document.getElementById("modalBody");
        const closeModal = document.getElementById("closeModal");

        modalHeader.textContent = titulo;
        modalBody.textContent = mensaje;
        modal.style.display = "block";
        modal.focus();

        // Cierra con la X
        closeModal.onclick = function () {
            modal.style.display = "none";
        };

        // Cierra al hacer clic fuera del modal
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        // Cierra con la tecla ESC
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                modal.style.display = "none";
            }
        });
    }
});
</script>




</body>
</html>
