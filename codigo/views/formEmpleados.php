<?php
require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../models/empleadoRegistro.php';
require_once __DIR__ . '/../config/conexionbd.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$empleado = new empleadoRegistro($pdo);
$empleados = $empleado->obtenerEmpleados();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HotelixHub - Empleados</title>
  <link rel="stylesheet" href="../assets/css/formEmpleados.css">
</head>
<body>

<div class="barra-lateral">

    <div class="logo">
      <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub"  class="logo">
    </div>
    <br><br>
            
    <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
    <a href="habitacion.html"><div class="menu-item">Habitaciones</div></a>

    <div class="usu">
      <button id="usuario">Usuarios</button>
      <div class="usu-contenido">
        <a href="formEmpleados.php">Empleados   </a>
        <a href="formClientes.php">Clientes</a>
      </div>
    </div>
    <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
    <a href="../controller/logout.php"><div class="logout">Cerrar Sesión</div></a>
  </div> 

  <main class="main">
    <header class="header">
      <div class="search-bar">
        <input type="text" placeholder="Buscar" />
        <button>🔍</button>
        <button>🔽</button>
      </div>
      <div class="profile">
        <span class="profile-name">Jose Cuervo</span>
        <div class="profile-img">👤</div>
      </div>
    </header>

    <h1 class="page-title">Empleados</h1>
    <h2 class="page-subtitle">Detalles</h2>

    <section id="agregar-empleado">
      <button type="button" onclick="abrirModal()" class="btn btn-primary">
        Agregar Empleado
      </button>
    </section>

    <!-- Modal -->
    <div id="modalEmpleado" class="modal" style="display: none;">
      <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h2>Registrar nuevo empleado</h2>
        <form id="formEmpleado" method="POST" action="../controller/guardarEmpleado.php">
          <div class="form-columnas">
            <div class="columna">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" required>

              <label for="apellido">Apellido:</label>
              <input type="text" id="apellido" name="apellido" required>

              <label for="tipoDocumento">Tipo de Documento:</label>
              <select id="tipoDocumento" name="tipoDocumento" required>
                <option value="">Seleccione un tipo de documento</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="PPP">Pasaporte</option>
              </select>

              <label for="numeroDocumento">Número de Documento:</label>
              <input type="text" id="numeroDocumento" name="numeroDocumento" required>

              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" required>
            </div>

            <div class="columna">
              <label for="email">Correo:</label>
              <input type="email" id="email" name="email" required>

              <label for="numeroTelefono">Teléfono:</label>
              <input type="tel" id="numeroTelefono" name="numeroTelefono" required>

              <label for="rol">Rol:</label>
              <select id="rol" name="usu_rol" required>
                <option value="">Seleccione un rol</option>
                <option value="3">Recepcionista</option>
                <option value="4">Cocinero</option>
                <option value="5">Camarero</option>
              </select>

              <label for="estado">Estado:</label>
              <select id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                <option value="en turno">En turno</option>
                <option value="vacaciones">Vacaciones</option>
                <option value="fuera de turno">Fuera de turno</option>
                <option value="capacitacion">Capacitación</option>
              </select>

              <label for="password">Contraseña:</label>
              <input type="password" id="password" name="password" required>
            </div>
          </div>

          <button type="submit" class="btn btn-success" name="guardarEmpleado">Guardar</button>
        </form>
      </div>
    </div>

    <section class="employee-details">
      <div class="employee-detail-header">
        <div>Información del Empleado</div>
      </div>
      <div class="employee-detail-content">
        <div class="employee-info">
          <div class="employee-avatar">👤</div>
          <div class="employee-data">
            <div class="name">Juan Mauricio Perez Florez</div>
            <div class="details">C.C. 32546223</div>
            <div class="details">Nacionalidad: Colombia</div>
          </div>
        </div>
        <div class="contact-details">
          <div>Correo: juanma@gmail.com</div>
          <div>Rol: Camarero (Pasarela)</div>
        </div>
        <div class="contact-details">
          <div>Cel: 3003265920</div>
          <div>Dirección: calle 56 #30-65</div>
          <div class="action-buttons">
            <button class="action-button">✏</button>
          </div>
        </div>
      </div>
    </section>

    <section class="employee-table">
  <div class="employee-table-header">
    <div>Rol</div>
    <div>Nombre</div>
    <div>Estado</div>
  </div>

  <?php foreach ($empleados as $emp): ?>
    <div class="employee-table-row">
      <div class="role-cell">
        <div><?= htmlspecialchars($emp['rol_nombre']) ?></div>
      </div>
      <div class="employee-cell">
        <div class="employee-icon">👤</div>
        <div class="employee-name"><?= htmlspecialchars($emp['nombre']) . ' ' . htmlspecialchars($emp['apellido']) ?></div>
      </div>
      <div class="status-cell">
        <div>
          <div class="status-indicator status-<?= strtolower(str_replace(' ', '-', $emp['estado'])) ?>"></div>
          <span><?= htmlspecialchars($emp['estado']) ?></span>
        </div>
        <div class="action-cell">
          <button>🔍</button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</section>

<!-- Modal de errores -->
<div id="modalErrores" class="modal" style="display: none;">
  <div class="modal-contenido">
    <span class="cerrar" onclick="cerrarModalErrores()">&times;</span>
    <h3>Error de registro</h3>
    <ul id="listaErrores"></ul>
  </div>
</div>

   <!--Formulario para generar PDF -->
    <form id="formPDF" action="generarPdf.php" method="post" target="_blank">
  <input type="hidden" name="datos" id="datosPDF">
  <button type="submit">Generar informe PDF</button>
</form>


<!-- Tabla oculta solo para generar el PDF -->
<table id="tablaEmpleadosPDF" style="display: none;">
  <thead>
    <tr>
      <th>Rol</th>
      <th>Nombre</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($empleados as $emp): ?>
      <tr>
        <td><?= htmlspecialchars($emp['rol_nombre']) ?></td>
        <td><?= htmlspecialchars($emp['nombre']) . ' ' . htmlspecialchars($emp['apellido']) ?></td>
        <td><?= htmlspecialchars($emp['estado']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


  </main>
<script>
  document.getElementById("formEmpleado").addEventListener("submit", function (e) {
    let errores = [];

    const nombre = document.getElementById("nombre").value.trim();
    const apellido = document.getElementById("apellido").value.trim();
    const tipoDocumento = document.getElementById("tipoDocumento").value;
    const numeroDocumento = document.getElementById("numeroDocumento").value.trim();
    const direccion = document.getElementById("direccion").value.trim();
    const email = document.getElementById("email").value.trim();
    const telefono = document.getElementById("numeroTelefono").value.trim();
    const rol = document.getElementById("rol").value;
    const estado = document.getElementById("estado").value;
    const password = document.getElementById("password").value;

    // Validaciones
    if (nombre === "") errores.push("El nombre es obligatorio.");
    if (apellido === "") errores.push("El apellido es obligatorio.");
    if (tipoDocumento === "") errores.push("Debe seleccionar un tipo de documento.");
    if (!/^\d{7,15}$/.test(numeroDocumento)) errores.push("El número de documento debe contener solo números.");
    if (direccion === "") errores.push("La dirección es obligatoria.");
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) errores.push("El correo no es válido.");
    if (!/^\d{7,15}$/.test(telefono)) errores.push("El teléfono debe contener entre 7 y 15 dígitos.");
    if (rol === "") errores.push("Debe seleccionar un rol.");
    if (estado === "") errores.push("Debe seleccionar un estado.");
    if (password.length < 6) errores.push("La contraseña debe tener al menos 6 caracteres.");

    if (errores.length > 0) {
      e.preventDefault(); // Detener envío

      // Mostrar errores en el modal
      const listaErrores = document.getElementById("listaErrores");
      listaErrores.innerHTML = ""; // Limpiar errores anteriores

      errores.forEach(function(error) {
        const li = document.createElement("li");
        li.textContent = error;
        listaErrores.appendChild(li);
      });

      document.getElementById("modalErrores").style.display = "block";
    }
  });

  // Validación en tiempo real: solo letras
function soloLetras(input) {
  input.addEventListener('input', function () {
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
  });
}

// Validación en tiempo real: solo números
function soloNumeros(input) {
  input.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '');
  });
}

// Validación en tiempo real: teléfono con máximo 15 dígitos
function validarTelefono(input) {
  input.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').substring(0, 15);
  });
}
function validarnumeroDocumento(input) {
  input.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').substring(0, 15);
  });
}


// Aplicar validaciones en tiempo real
document.addEventListener('DOMContentLoaded', function () {
  soloLetras(document.getElementById('nombre'));
  soloLetras(document.getElementById('apellido'));
  soloNumeros(document.getElementById('numeroDocumento'));
  validarTelefono(document.getElementById('numeroTelefono'));
  validarnumeroDocumento(document.getElementById('numeroDocumento'));
});


  function abrirModal() {
    document.getElementById('modalEmpleado').style.display = 'block';
  }

  function cerrarModal() {
    document.getElementById('modalEmpleado').style.display = 'none';
  }

  function cerrarModalErrores() {
    document.getElementById("modalErrores").style.display = "none";
  }

  // Cerrar modales al hacer clic fuera
  window.onclick = function(event) {
    const modalEmpleado = document.getElementById('modalEmpleado');
    const modalErrores = document.getElementById('modalErrores');

    if (event.target === modalEmpleado) {
      modalEmpleado.style.display = "none";
    }
    if (event.target === modalErrores) {
      modalErrores.style.display = "none";
    }
  };


  // Generar PDF con los datos de la tabla
  // Al enviar el formulario, se captura la tabla y se convierte a HTML
  document.getElementById("formPDF").addEventListener("submit", function(e) {
    const tabla = document.getElementById("tablaEmpleadosPDF");
    const html = tabla.outerHTML;
    document.getElementById("datosPDF").value = html;
  });




</body>
</html>