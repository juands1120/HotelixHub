<?php
require_once __DIR__ . '/../services/sessionManager.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Contraseña - HotelixHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/verificarCorreToken.css">
</head>
<body>

  <!-- Logo -->
  <div class="logo-container">
    <a href="home.php"><img src="../assets/img/imghome/Logo Principal.png" alt="HotelixHub"  class="logo"></a>
  </div>

  <!-- Contenedor principal -->
  <div class="container">
    <!-- Formulario -->
    <div class="form-container">
      <h2>Recuperación de<br><a style="color:#4c318f;">Contraseña</a></h2>
      <div class="form-icon">
  <i class="fas fa-lock"></i>
    </div>

      <form id="resetForm" method="post" action="../models/generarToken.php">
        <div class="input-group">
          <input type="email" id="email" name="email" placeholder="📧 Ingrese su email" required>
        </div>
        <button type="submit">Verificar</button>
    </div>

    <!-- Imagen con texto superpuesto -->
    <div class="image-panel">
      <div class="overlay-text">
        <h1>¿Olvidaste tu contraseña?</h1>
        <p>Ingresa tu correo electrónico y te ayudaremos<br>a recuperar el acceso a tu cuenta.</p>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="cerrarModal()">&times;</span>
      <p id="modalMessage">Mensaje del sistema</p>
    </div>
  </div>

  <script>
    const form = document.getElementById("resetForm");
    const modal = document.getElementById("modal");
    const modalMessage = document.getElementById("modalMessage");

    form.addEventListener("submit", function(e) {
      //e.preventDefault();
      const email = document.getElementById("email").value.trim();

      if (!email) {
        mostrarModal("El campo de correo es obligatorio.");
        return;
      }

      const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
      if (!emailRegex.test(email)) {
        mostrarModal("Por favor ingresa un correo válido.");
        return;
      }

      mostrarModal("Correo verificado correctamente.");
    });

    function mostrarModal(mensaje) {
      modalMessage.textContent = mensaje;
      modal.style.display = "block";
    }

    function cerrarModal() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target === modal) {
        cerrarModal();
      }
    }
  </script>

</body>
</html>


