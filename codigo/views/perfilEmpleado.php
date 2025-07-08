<?php
require_once __DIR__ . '/../services/sessionManager.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Verificar que el rol sea cliente
if ($_SESSION['usuario']['usu_idrol'] != 2) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="../assets/css/perfilEmpleado.css"/>
</head>
<body>
  <!-- BARRA LATERAL -->
  <div class="barra-lateral">
    <div class="logo">
      <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo"></a>
    </div><br><br>
    <a href="dashEmpleado.html"><div class="menu-item">Inicio</div></a>
    <a href="formClientes.php"><div class="menu-item">Clientes</div></a>     
    <a href="ProductosAdmin.php"><div class="menu-item">Perfil</div></a>
    <a href="../controller/logout.php"><div class="logout">Cerrar Sesión</div></a>
  </div>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main">
    <div class="main-header">
      <h1>Perfil</h1>
      <div class="btn-group">
        <button id="editBtn" class="btn-edit">Editar perfil</button>
        <button id="saveBtn" class="btn-save" disabled>Guardar</button>
      </div>
    </div>

    <!-- DATOS PERSONALES -->
<!-- Dentro de la sección "Datos Personales" ya modificado -->
<section class="card">
  <h2>Datos Personales</h2>
  <div class="group">
    <div class="col display">
      <p><strong>Nombre:</strong> <span id="dispNombre">Luis Suarez</span></p>
      <p><strong>Tipo Doc.:</strong> <span id="dispTipo">Pasaporte</span></p>
      <p><strong>Número Doc.:</strong> <span id="dispNum">A6258792</span></p>
      <p><strong>País:</strong> <span id="dispPais">México</span></p>
    </div>
    <div class="col edit">
      <label for="inpNombre">Nombre completo</label>
      <input id="inpNombre" type="text" value="Luis Suarez" disabled/>
      <label for="inpTipo">Tipo de documento</label>
      <input id="inpTipo" type="text" value="Pasaporte" disabled/>
      <label for="inpNum">Número de documento</label>
      <input id="inpNum" type="text" value="A6258792" disabled/>
      <label for="inpPais">País de procedencia</label>
      <input id="inpPais" type="text" value="México" disabled/>
    </div>
  </div>
  <div style="text-align: right; margin-top: 20px;">
    <!-- Cambiado el ID y estilo para coherencia total -->
    <button id="btnEditPass" class="btn-save">Editar Contraseña</button>
  </div>
</section>

<!-- Modal de Cambio de Contraseña corregido -->
<div class="modal" id="modalPass">
  <div class="modal-content">
    <button class="close-button" id="closeModalPass">&times;</button>
    <h3>Actualizar Contraseña</h3>
    <input type="password" id="claveActual" placeholder="Contraseña actual">
    <input type="password" id="claveNueva" placeholder="Nueva contraseña">
    <input type="password" id="claveConfirmar" placeholder="Confirmar nueva contraseña">
    <button class="btn-save" onclick="guardarClave()">Guardar</button>
  </div>
</div>


    <!-- DATOS DE CONTACTO -->
    <section class="card">
      <h2>Datos de Contacto</h2>
      <div class="group">
        <div class="col display">
          <p><strong>Email:</strong> <span id="dispEmail">luiscorreo@gmail.com</span></p>
          <p><strong>Teléfono:</strong> <span id="dispTel">+52 6263050460</span></p>
        </div>
        <div class="col edit">
          <label for="inpEmail">Email</label>
          <input id="inpEmail" type="email" value="luiscorreo@gmail.com" disabled/>
          <label for="inpTel">Teléfono</label>
          <input id="inpTel" type="tel" value="+526263050460" disabled/>
        </div>
      </div>
    </section>
  </main>

  <!-- MODAL GENERAL -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle"></h3>
      <p id="modalMessage"></p>
      <button id="modalCloseBtn">Cerrar</button>
    </div>
  </div>



  <!-- SCRIPT -->
  <script>
    // Botones y campos de perfil
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const inpNombre = document.getElementById('inpNombre');
    const inpTipo = document.getElementById('inpTipo');
    const inpNum = document.getElementById('inpNum');
    const inpPais = document.getElementById('inpPais');
    const inpEmail = document.getElementById('inpEmail');
    const inpTel = document.getElementById('inpTel');
    const dispNombre = document.getElementById('dispNombre');
    const dispTipo = document.getElementById('dispTipo');
    const dispNum = document.getElementById('dispNum');
    const dispPais = document.getElementById('dispPais');
    const dispEmail = document.getElementById('dispEmail');
    const dispTel = document.getElementById('dispTel');

    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    function openModal(title, message) {
      modalTitle.textContent = title;
      modalMessage.textContent = message;
      modal.classList.add('active');
    }

    modalCloseBtn.addEventListener('click', () => {
      modal.classList.remove('active');
    });

    function validarSoloLetras(text) {
      return /^[A-Za-zÁÉÍÓÚñáéíóú\s]+$/.test(text.trim());
    }
    function validarEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
    }
    function validarTelefono(tel) {
      return /^[\d+\s]+$/.test(tel.trim());
    }

    editBtn.addEventListener('click', () => {
      document.querySelectorAll('.col.edit input').forEach(input => input.disabled = false);
      editBtn.disabled = true;
      saveBtn.disabled = false;
    });

    saveBtn.addEventListener('click', () => {
      let errores = [];
      if (!validarSoloLetras(inpNombre.value)) errores.push("Nombre inválido");
      if (!validarSoloLetras(inpTipo.value)) errores.push("Tipo de documento inválido");
      if (inpNum.value.trim() === "") errores.push("Número de documento vacío");
      if (!validarSoloLetras(inpPais.value)) errores.push("País inválido");
      if (!validarEmail(inpEmail.value)) errores.push("Email inválido");
      if (!validarTelefono(inpTel.value)) errores.push("Teléfono inválido");

      if (errores.length > 0) return openModal("Errores", errores.join('\n'));

      dispNombre.textContent = inpNombre.value.trim();
      dispTipo.textContent = inpTipo.value.trim();
      dispNum.textContent = inpNum.value.trim();
      dispPais.textContent = inpPais.value.trim();
      dispEmail.textContent = inpEmail.value.trim();
      dispTel.textContent = inpTel.value.trim();

      document.querySelectorAll('.col.edit input').forEach(input => input.disabled = true);
      editBtn.disabled = false;
      saveBtn.disabled = true;

      openModal("Éxito", "Datos actualizados correctamente.");
    });

    // Modal de contraseña
    const btnEditPass = document.getElementById('btnEditPass');
    const modalPass = document.getElementById('modalPass');
    const closeModalPass = document.getElementById('closeModalPass');

    btnEditPass.addEventListener('click', () => {
      modalPass.classList.add('active');
    });

    closeModalPass.addEventListener('click', () => {
      modalPass.classList.remove('active');
    });

    window.addEventListener('click', (e) => {
      if (e.target === modalPass) {
        modalPass.classList.remove('active');
      }
    });

    function guardarClave() {
      const actual = document.getElementById('claveActual').value.trim();
      const nueva = document.getElementById('claveNueva').value.trim();
      const confirmar = document.getElementById('claveConfirmar').value.trim();
      let errores = [];

      if (!actual || !nueva || !confirmar) {
        errores.push("Todos los campos son obligatorios.");
      }
      if (nueva.length < 6) {
        errores.push("La nueva contraseña debe tener al menos 6 caracteres.");
      }
      if (nueva !== confirmar) {
        errores.push("Las contraseñas no coinciden.");
      }

      if (errores.length > 0) {
        openModal("Error", errores.join("\n"));
        return;
      }

      // Aquí podrías hacer AJAX para actualizar en PHP
      openModal("Éxito", "Tu contraseña ha sido actualizada.");
      modalPass.classList.remove("active");
    }
  </script>
</body>
</html>
