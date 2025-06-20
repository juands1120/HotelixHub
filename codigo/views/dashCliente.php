<?php
require_once __DIR__ . '/../services/sessionManager.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Verificar que el rol sea cliente
if ($_SESSION['usuario']['usu_idrol'] != 2) {
    header('Location: ../login.php'); // O a una página de error
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
  <link rel="stylesheet" href="../css/dashCliente.css">

</head>
<body>

  <!-- BARRA LATERAL -->
  <aside class="barra-lateral">
    <div class="logo">
      <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub"  class="logo">
    </div>
    <nav>
      <a href="dashCliente.php"><i class="fa fa-home"></i>Inicio</a>
      <a href="#"><i class="fa fa-bed"></i>Habitaciones</a>
      <a href="#"><i class="fa fa-box"></i>Productos</a>
      <a href="#"><i class="fa fa-cog"></i>Ajustes</a>
      <a href="../controller/logout.php"><i class="sesion"></i>Cerrar Sesion</a>
    </nav>
  </aside>

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
    </section>

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

    <!-- HISTORIAL -->
    <section class="history">
      <h2>Historial de Reservas</h2>
      <table>
        <thead>
          <tr>
            <th>Hotel</th>
            <th>Fecha de Reserva</th>
            <th>Fecha de Entrada</th>
            <th>Fecha de Salida</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Hotel El Campín</td>
            <td>01/01/2024</td>
            <td>10/01/2024</td>
            <td>15/01/2024</td>
            <td>Confirmada</td>
          </tr>
          <tr>
            <td>Hotel Central</td>
            <td>15/02/2024</td>
            <td>20/02/2024</td>
            <td>25/02/2024</td>
            <td>Cancelada</td>
          </tr>
          <!-- Más filas -->
        </tbody>
      </table>
    </section>
  </main>

  <!-- MODAL -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle"></h3>
      <p id="modalMessage"></p>
      <button id="modalCloseBtn">Cerrar</button>
    </div>
  </div>

  <script>
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');

    // Campos
    const inpNombre = document.getElementById('inpNombre');
    const inpTipo = document.getElementById('inpTipo');
    const inpNum = document.getElementById('inpNum');
    const inpPais = document.getElementById('inpPais');
    const inpEmail = document.getElementById('inpEmail');
    const inpTel = document.getElementById('inpTel');

    // Display
    const dispNombre = document.getElementById('dispNombre');
    const dispTipo = document.getElementById('dispTipo');
    const dispNum = document.getElementById('dispNum');
    const dispPais = document.getElementById('dispPais');
    const dispEmail = document.getElementById('dispEmail');
    const dispTel = document.getElementById('dispTel');

    // Modal
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    // Abrir modal con mensaje
    function openModal(title, message) {
      modalTitle.textContent = title;
      modalMessage.textContent = message;
      modal.classList.add('active');
    }

    modalCloseBtn.addEventListener('click', () => {
      modal.classList.remove('active');
    });

    // Validar solo letras y espacios
    function validarSoloLetras(text) {
      return /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(text.trim());
    }

    // Validar números (solo dígitos)
    function validarSoloNumeros(text) {
      return /^[0-9]+$/.test(text.trim());
    }

    // Validar email
    function validarEmail(email) {
      // Regexp simple para validar email
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
    }

    // Validar teléfono (números, +, espacios)
    function validarTelefono(tel) {
      return /^[\d+\s]+$/.test(tel.trim());
    }

    editBtn.addEventListener('click', () => {
      document.querySelectorAll('.col.edit input').forEach(input => input.disabled = false);
      editBtn.disabled = true;
      saveBtn.disabled = false;
      // Limpiar estilos de error
      document.querySelectorAll('.col.edit input').forEach(input => input.classList.remove('invalid'));
    });

    saveBtn.addEventListener('click', () => {
      let errores = [];

      // Validar nombre
      if (!validarSoloLetras(inpNombre.value)) {
        errores.push("El nombre solo debe contener letras y espacios.");
        inpNombre.classList.add('invalid');
      } else {
        inpNombre.classList.remove('invalid');
      }

      // Validar tipo doc (solo letras y espacios)
      if (!validarSoloLetras(inpTipo.value)) {
        errores.push("El tipo de documento solo debe contener letras y espacios.");
        inpTipo.classList.add('invalid');
      } else {
        inpTipo.classList.remove('invalid');
      }

      // Número doc puede tener letras y números, permitimos alfanumérico básico
      // pero no vacío
      if (inpNum.value.trim() === "") {
        errores.push("El número de documento no puede estar vacío.");
        inpNum.classList.add('invalid');
      } else {
        inpNum.classList.remove('invalid');
      }

      // País solo letras y espacios
      if (!validarSoloLetras(inpPais.value)) {
        errores.push("El país solo debe contener letras y espacios.");
        inpPais.classList.add('invalid');
      } else {
        inpPais.classList.remove('invalid');
      }

      // Email
      if (!validarEmail(inpEmail.value)) {
        errores.push("El email no tiene un formato válido.");
        inpEmail.classList.add('invalid');
      } else {
        inpEmail.classList.remove('invalid');
      }

      // Teléfono
      if (!validarTelefono(inpTel.value)) {
        errores.push("El teléfono solo debe contener números, espacios o el símbolo '+'.");
        inpTel.classList.add('invalid');
      } else {
        inpTel.classList.remove('invalid');
      }

      if (errores.length > 0) {
        openModal("Errores de Validación", errores.join('\n'));
        return; // No continuar
      }

      // Si pasa validación, actualizar display
      dispNombre.textContent = inpNombre.value.trim();
      dispTipo.textContent = inpTipo.value.trim();
      dispNum.textContent = inpNum.value.trim();
      dispPais.textContent = inpPais.value.trim();
      dispEmail.textContent = inpEmail.value.trim();
      dispTel.textContent = inpTel.value.trim();

      document.querySelectorAll('.col.edit input').forEach(input => input.disabled = true);
      editBtn.disabled = false;
      saveBtn.disabled = true;

      openModal("Éxito", "Datos guardados con éxito.");
    });
  </script>
</body>
</html>
