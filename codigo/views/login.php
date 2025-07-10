<?php
// Inicio de sesión y redirección si ya está autenticado
require_once __DIR__ . '/../services/sessionManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario'])) {
    // Redirigir según el rol del usuario
    switch ($_SESSION['usuario']['usu_idrol']) {
        case 1: case 3: case 4: case 5:
            header('Location: dashAdmin.php');
            break;
        case 2:
            header('Location: dashCliente.php');
            break;
        default:
            header('Location: ../login.php');
            break;
    }
    exit();
}

// Mostrar error de email duplicado si existe
$error_email = '';
if (isset($_SESSION['error_email'])) {
    $error_email = $_SESSION['error_email'];
    unset($_SESSION['error_email']);
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
        <img src="../assets/img/imghome/Logo Principal.png" alt="HotelixHub" class="logo">
    </div>
    
    <div class="container">
        <!-- Sección de bienvenida -->
        <div class="login-section">
            <div class="login-text">
                <h1>Bienvenido</h1>
                <p>Únete a nuestra comunidad y disfruta de nuestros servicios exclusivos.</p>
                <button id="iniciarSesionBtn" class="btn1">Iniciar sesión</button>
            </div>
        </div>
        
        <!-- Sección de registro -->
        <div class="register-section">
            <h2>Registro</h2>
            <?php if (!empty($error_email)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_email); ?></div>
            <?php endif; ?>
            <form id="registro" method="POST" action="../controller/guardarRegistro.php">
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese su Nombre Completo" required>  
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su Apellido Completo" required> 
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
                <small class="password-requirements">La contraseña debe tener al menos 8 caracteres y una letra.</small>
                <button type="submit" name="registrarse" id="registroBtn" class="btn" disabled>Registrarse</button>
            </form>
        </div>
    </div>

    <!-- Modal para mostrar mensajes -->
    <div id="myModal" class="modal" tabindex="-1">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-header" id="modalHeader"></div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Redirección al login
        document.getElementById('iniciarSesionBtn').addEventListener('click', function() {
            window.location.href = 'login.php';
        });

        // 2. Validaciones en tiempo real para cada campo
        // Nombre - solo letras y espacios
        document.getElementById('nombre').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            validarCampos();
        });
        
        // Apellido - solo letras y espacios
        document.getElementById('apellido').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            validarCampos();
        });
        
        // Número de documento - depende del tipo de documento
        document.getElementById('numeroDocumento').addEventListener('input', function() {
            const tipoDoc = document.getElementById('tipodocumento').value;
            if (tipoDoc === 'PA') {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 12);
            } else {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
            }
            validarCampos();
        });
        
        // Tipo de documento
        document.getElementById('tipodocumento').addEventListener('change', function() {
            validarCampos();
        });
        
        // Teléfono - solo números, + y espacios
        document.getElementById('numeroTelefono').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\s]/g, '').slice(0, 15);
            validarCampos();
        });
        
        // País - solo letras y espacios
        document.getElementById('paisProcedencia').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            validarCampos();
        });
        
        // Email
        document.getElementById('email').addEventListener('input', function() {
            validarCampos();
        });
        
        // 3. Validación de contraseña en tiempo real
        const passwordInput = document.getElementById('password');
        const registroBtn = document.getElementById('registroBtn');
        const passwordError = document.createElement('div');
        passwordError.id = 'password-error';
        passwordError.style.color = 'red';
        passwordError.style.fontSize = '0.8rem';
        passwordError.style.marginTop = '5px';
        passwordInput.parentNode.insertBefore(passwordError, passwordInput.nextSibling.nextSibling);

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let isValid = true;
            let errorMessage = '';

            if (password.length < 8) {
                errorMessage = 'La contraseña debe tener al menos 8 caracteres.';
                isValid = false;
            } else if (!/[a-zA-Z]/.test(password)) {
                errorMessage = 'La contraseña debe contener al menos una letra.';
                isValid = false;
            }

            passwordError.textContent = errorMessage;
            validarCampos();
        });

        // Función para validar todos los campos
        function validarCampos() {
            const campos = ['nombre', 'apellido', 'tipodocumento', 'numeroDocumento', 
                          'numeroTelefono', 'email', 'paisProcedencia', 'password'];
            
            let todosLlenos = true;
            
            for (let campo of campos) {
                if (document.getElementById(campo).value.trim() === "") {
                    todosLlenos = false;
                    break;
                }
            }
            
            // Validar contraseña
            const password = document.getElementById('password').value;
            const passwordValida = password.length >= 8 && /[a-zA-Z]/.test(password);
            
            // Habilitar/deshabilitar botón
            registroBtn.disabled = !(todosLlenos && passwordValida);
        }

        // 4. Validación al enviar el formulario
        document.getElementById('registro').addEventListener('submit', function(e) {
            const campos = ['nombre', 'apellido', 'tipodocumento', 'numeroDocumento', 
                          'numeroTelefono', 'email', 'paisProcedencia', 'password'];
            
            // Validar campos vacíos
            for (let campo of campos) {
                if (document.getElementById(campo).value.trim() === "") {
                    e.preventDefault();
                    showModal("Error de Registro", "Por favor completa todos los campos requeridos.");
                    return;
                }
            }

            // Validar formato de email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showModal("Error de Registro", "Por favor, ingrese un correo válido con extensión, por ejemplo: juan@gmail.com");
                return;
            }
        });

        // 5. Función para mostrar modal
        function showModal(titulo, mensaje) {
            const modal = document.getElementById("myModal");
            const modalHeader = document.getElementById("modalHeader");
            const modalBody = document.getElementById("modalBody");
            const closeModal = document.getElementById("closeModal");

            modalHeader.textContent = titulo;
            modalBody.textContent = mensaje;
            modal.style.display = "block";

            // Cerrar modal
            closeModal.onclick = function() { modal.style.display = "none"; };
            window.onclick = function(event) { if (event.target === modal) modal.style.display = "none"; };
            document.addEventListener('keydown', function(event) {
                if (event.key === "Escape") modal.style.display = "none";
            });
        }
    });
    </script>
</body>
</html>