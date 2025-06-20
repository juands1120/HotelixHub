<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña - HotelixHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/nuevaContraseña.css">
</head>
<body>

  <!-- Logo con sombra -->
  <div class="logo-container">
    <img src="../assets/img/imgHome/Logo principal.png" alt="HotelixHub"  class="logo">
  </div>

  <!-- Contenedor principal -->
  <div class="container">
    <div class="form-container">
      <h2>Restablecer <span style="color:#4c318f;">Contraseña</span></h2>
      <form id="resetForm" method="POST" action="../controller/guardarNuevaContraseña.php?token=<?= htmlspecialchars($_GET['token']) ?>">
        <div class="input-group">
          <label for="newPassword">Nueva Contraseña</label>
          <input type="password" id="newPassword" name="nueva_contrasena" placeholder="Nueva Contraseña">
        </div>
        <div class="input-group">
          <label for="confirmPassword">Confirmar Contraseña</label>
          <input type="password" id="confirmPassword" name="confirmar_contrasena" placeholder="Confirmar Contraseña">
        </div>
        <button type="submit" id="submitBtn">
          Confirmar
          <span class="spinner" id="spinner"></span>
        </button>
      </form>
    </div>

    <div class="image-panel">
      <div class="image-text" id="imageText">¡Gestiona con eficiencia y estilo!</div>
    </div>
  </div>

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage">Mensaje del sistema</p>
    </div>
  </div>

  <script>
    const form = document.getElementById('resetForm');
    const modal = document.getElementById('modal');
    const modalContent = document.querySelector('.modal-content');
    const modalMessage = document.getElementById('modalMessage');
    const closeBtn = document.querySelector('.close');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', function (e) {
      //e.preventDefault();

      const password = document.getElementById('newPassword').value.trim();
      const confirm = document.getElementById('confirmPassword').value.trim();

      submitBtn.classList.add('loading');

      if (!password || !confirm) {
        showModal("Por favor, completa ambos campos.");
        stopLoading();
        return;
      }

      if (password.length < 6) {
        showModal("La contraseña debe tener al menos 6 caracteres.");
        stopLoading();
        return;
      }

      if (password !== confirm) {
        showModal("Las contraseñas no coinciden.");
        stopLoading();
        return;
      }

      showModal("¡Contraseña restablecida con éxito!");

      setTimeout(() => {
        window.location.href = "login.php";
      }, 2000);
    });

    function showModal(message) {
      modalMessage.textContent = message;
      modal.style.display = 'block';
      setTimeout(() => {
        modal.classList.add('show');
      }, 10);
    }

    function stopLoading() {
      submitBtn.classList.remove('loading');
    }

    closeBtn.addEventListener('click', () => {
      modal.classList.remove('show');
      setTimeout(() => {
        modal.style.display = 'none';
        stopLoading();
      }, 300);
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('show');
        setTimeout(() => {
          modal.style.display = 'none';
          stopLoading();
        }, 300);
      }
    });

    // Frases rotativas
    const frases = [
      "¡Gestiona con eficiencia y estilo!",

      "Tu hotel, siempre bajo control.",
      "Seguridad y agilidad en cada clic."
    ];

    let fraseIndex = 0;
    const textoImagen = document.getElementById("imageText");

    setInterval(() => {
      fraseIndex = (fraseIndex + 1) % frases.length;
      textoImagen.style.opacity = 0;
      setTimeout(() => {
        textoImagen.textContent = frases[fraseIndex];
        textoImagen.style.opacity = 1;
      }, 300);
    }, 4000);
  </script>
<?php if (!empty($mostrar_modal) && !empty($message)): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modal");
    const modalMessage = document.getElementById("modalMessage");
    modalMessage.textContent = <?php echo json_encode($message); ?>;
    modal.style.display = 'block';
    setTimeout(() => {
      modal.classList.add('show');
    }, 10);
  });
</script>
<?php endif; ?>

</body>
</html>

