<?php
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
            header('Location: dashOtro.php');
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HotelixHub - Inicio de Sesión</title>
  <link rel="stylesheet" href="../assets/css/login.css">
</head>
<?php if (isset($_GET['error'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      mostrarModal("<?= htmlspecialchars($_GET['error']) ?>");
    });
  </script>
<?php endif; ?>

<body>
  <img src="../asets/img/imgLoginRegistro/logoPrincipal.png" alt="HotelixHub" width="500" height="400" class="logo">

  <div class="container">
    <div class="login-box">
      <h3 class="text-center">Inicio de Sesión</h3>
      <form id="loginForm" method="POST" action="../controller/iniciarLogin.php">
        <div class="form-group">
          <label>Correo Electrónico</label>
          <input type="email" name="email" id="email" placeholder="Ingrese su email">
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input type="password" name="password" id="password" placeholder="Ingrese su contraseña">
          <div class="text-end"><a href="verificarCorreoToken.php">¿Olvidó su contraseña?</a></div>
        </div>
        <button type="submit" class="btn" name="login">Iniciar Sesión</button>
      </form>
    </div>
    <div class="welcome-box">
      <h3>Bienvenido!</h3>
      <p>Ingresa tus datos y vive una experiencia diferente con nosotros</p>
      <a href="registrar.php" class="btn btn-light">Registrarse</a>

    </div>
  </div>

  <!-- Modal -->
  <div id="customModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage"></p>
    </div>
  </div>

<script>
  function mostrarModal(mensaje) {
    const modal = document.getElementById('customModal');
    const mensajeElemento = document.getElementById('modalMessage');
    mensajeElemento.textContent = mensaje;
    modal.style.display = 'block';

    setTimeout(() => {
      modal.style.display = 'none';
    }, 3000);
  }

  document.querySelector('.close').addEventListener('click', function () {
    document.getElementById('customModal').style.display = 'none';
  });

  window.addEventListener('click', function (event) {
    const modal = document.getElementById('customModal');
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });

  // Validación simple del formulario (solo para asegurarse que no esté vacío)
  document.getElementById('loginForm').addEventListener('submit', function (event) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    // Expresión regular estricta para validar correo electrónico
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

