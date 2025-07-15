<?php
// Inicio de sesión y redirección si ya está autenticado
require_once __DIR__ . '/../services/sessionManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirección según el rol del usuario si ya tiene sesión activa
if (isset($_SESSION['usuario'])) {
    switch ($_SESSION['usuario']['usu_idrol']) {
        case 1: header('Location: dashAdmin.php'); break;
        case 2: header('Location: dashCliente.php'); break;
        case 3: case 4: case 5: header('Location: dashEmpleado.php'); break;
        default: header('Location: ../login.php'); break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HotelixHub - Inicio de Sesión</title>
  <link rel="stylesheet" href="../assets/css/login.css">
</head>

<!-- Script para mostrar modal de error si existe -->
<?php if (isset($_GET['error'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      mostrarModal("<?= htmlspecialchars($_GET['error']) ?>");
    });
  </script>
<?php endif; ?>

<body>
  <!-- Logo principal -->
  <a href="home.php"><img src="../assets/img/imghome/Logo Principal.png" alt="HotelixHub"  class="logo"></a>

  <!-- Contenedor principal -->
  <div class="container">
    <!-- Caja de login -->
    <div class="login-box">
      <h3 class="text-center">Inicio de Sesión</h3>
      <form id="loginForm" method="POST" action="../controller/iniciarLogin.php">
        <div class="form-group">
          <label>Correo Electrónico</label>
          <input type="email" name="email" id="email" placeholder="Ingrese su email" required>
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
          <div class="text-end"><a href="verificarCorreoToken.php" target="_blank">¿Olvidó su contraseña?</a></div>
        </div>
        <button type="submit" class="btn" name="login">Iniciar Sesión</button>
      </form>
    </div>
    
    <!-- Caja de bienvenida -->
    <div class="welcome-box">
      <h3>Bienvenido!</h3>
      <p>Ingresa tus datos y vive una experiencia diferente con nosotros</p>
      <a href="registrar.php" class="btn btn-light">Registrarse</a>
    </div>
  </div>

  <!-- Modal para mensajes -->
  <div id="customModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage"></p>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Función para mostrar modal con mensaje
    function mostrarModal(mensaje) {
      const modal = document.getElementById('customModal');
      const mensajeElemento = document.getElementById('modalMessage');
      mensajeElemento.textContent = mensaje;
      modal.style.display = 'block';

      // Cierra automáticamente después de 3 segundos
      setTimeout(() => {
        modal.style.display = 'none';
      }, 3000);
    }

    // Event listeners para cerrar modal
    document.querySelector('.close').addEventListener('click', function () {
      document.getElementById('customModal').style.display = 'none';
    });

    window.addEventListener('click', function (event) {
      const modal = document.getElementById('customModal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Validación del formulario
    document.getElementById('loginForm').addEventListener('submit', function (event) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value.trim();
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (email === '' || password === '') {
        event.preventDefault();
        mostrarModal('Por favor, complete todos los campos.');
        return;
      }

      if (!emailRegex.test(email)) {
        event.preventDefault();
        mostrarModal('Por favor, ingrese un correo electrónico válido.');
        return;
      }
    });
  </script>
</body>
</html>