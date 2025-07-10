<?php
require_once __DIR__ . '/../services/sessionManager.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

if (!in_array($_SESSION['usuario']['usu_idrol'], [3, 4, 5])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil de Empleado</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="../assets/css/perfilEmpleado.css"/>
</head>
<body>
  <!-- BARRA LATERAL -->
  <div class="barra-lateral">
    <div class="logo">
      <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo"></a>
    </div><br><br>
    <a href="dashEmpleado.php"><div class="menu-item">Inicio</div></a>
    <a href="formClientes.php"><div class="menu-item">Clientes</div></a>     
    <a href="perfilEmpleado.php"><div class="menu-item">Perfil</div></a>
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
    <section class="card">
      <h2>Datos Personales</h2>
      <div class="group">
        <div class="col display">
          <p><strong>Nombre:</strong> <span id="dispNombre">Cargando...</span></p>
          <p><strong>Tipo Doc.:</strong> <span id="dispTipo"></span></p>
          <p><strong>Número Doc.:</strong> <span id="dispNum"></span></p>
          <p><strong>Estado:</strong> <span id="dispEstado"></span></p>
        </div>
        <div class="col edit">
          <label for="inpNombre">Nombre completo</label>
          <input id="inpNombre" type="text" value="" disabled readonly/>
          <label for="inpTipo">Tipo de documento</label>
          <input id="inpTipo" type="text" value="" disabled readonly/>
          <label for="inpNum">Número de documento</label>
          <input id="inpNum" type="text" value="" disabled readonly/>
          <label for="inpEstado">Estado</label>
          <input id="inpEstado" type="text" value="" disabled readonly/>
        </div>
      </div>
      <div style="text-align: right; margin-top: 20px;">
        <button id="btnEditPass" class="btn-save">Editar Contraseña</button>
      </div>
    </section>

    <!-- DATOS DE CONTACTO -->
    <section class="card">
      <h2>Datos de Contacto</h2>
      <div class="group">
        <div class="col display">
          <p><strong>Email:</strong> <span id="dispEmail"></span></p>
          <p><strong>Teléfono:</strong> <span id="dispTel"></span></p>
          <p><strong>Dirección:</strong> <span id="dispDireccion"></span></p>
        </div>
        <div class="col edit">
          <label for="inpEmail">Email</label>
          <input id="inpEmail" type="email" value="" disabled/>
          <label for="inpTel">Teléfono</label>
          <input id="inpTel" type="tel" value="" disabled/>
          <label for="inpDireccion">Dirección</label>
          <input id="inpDireccion" type="text" value="" disabled/>
        </div>
      </div>
    </section>
  </main>

  <!-- MODALES -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle"></h3>
      <p id="modalMessage"></p>
      <button id="modalCloseBtn">Cerrar</button>
    </div>
  </div>

  <div class="modal" id="modalPass">
    <div class="modal-content">
      <button class="close-button" id="closeModalPass">&times;</button>
      <h3>Actualizar Contraseña</h3>
      <input type="password" id="claveActual" placeholder="Contraseña actual">
      <input type="password" id="claveNueva" placeholder="Nueva contraseña">
      <input type="password" id="claveConfirmar" placeholder="Confirmar nueva contraseña">
      <div class="password-requirements">
        <p>La contraseña debe contener:</p>
        <ul>
          <li id="req-length">Mínimo 6 caracteres</li>
          <li id="req-uppercase">Al menos una mayúscula</li>
          <li id="req-number">Al menos un número</li>
        </ul>
      </div>
      <button class="btn-save" id="btnGuardarClave">Guardar</button>
    </div>
  </div>

<script>
// Constantes y variables globales
const DOM = {
  modal: document.getElementById('modal'),
  modalTitle: document.getElementById('modalTitle'),
  modalMessage: document.getElementById('modalMessage'),
  modalCloseBtn: document.getElementById('modalCloseBtn'),
  modalPass: document.getElementById('modalPass'),
  closeModalPass: document.getElementById('closeModalPass'),
  btnEditPass: document.getElementById('btnEditPass'),
  btnGuardarClave: document.getElementById('btnGuardarClave'),
  editBtn: document.getElementById('editBtn'),
  saveBtn: document.getElementById('saveBtn'),
  claveNueva: document.getElementById('claveNueva'),
  claveConfirmar: document.getElementById('claveConfirmar')
};

// Funciones de utilidad
const mostrarError = (mensaje) => {
  DOM.modalTitle.textContent = "Error";
  DOM.modalMessage.textContent = mensaje;
  DOM.modal.classList.add('active');
};

const mostrarExito = (mensaje) => {
  DOM.modalTitle.textContent = "Éxito";
  DOM.modalMessage.textContent = mensaje;
  DOM.modal.classList.add('active');
};

// Validar fortaleza de contraseña
const validarContraseña = (contraseña) => {
  const tieneLongitud = contraseña.length >= 6;
  const tieneMayuscula = /[A-Z]/.test(contraseña);
  const tieneNumero = /[0-9]/.test(contraseña);
  
  // Actualizar indicadores visuales
  if(document.getElementById('req-length')) {
    document.getElementById('req-length').style.color = tieneLongitud ? 'green' : 'red';
  }
  if(document.getElementById('req-uppercase')) {
    document.getElementById('req-uppercase').style.color = tieneMayuscula ? 'green' : 'red';
  }
  if(document.getElementById('req-number')) {
    document.getElementById('req-number').style.color = tieneNumero ? 'green' : 'red';
  }
  
  return tieneLongitud && tieneMayuscula && tieneNumero;
};

// Cargar datos del empleado
const cargarDatosEmpleado = async () => {
  try {
    const response = await fetch('../controller/empleadoInfoController.php');
    
    // Verificar si la respuesta es JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      throw new Error(text || 'Respuesta no válida del servidor');
    }
    
    const data = await response.json();
    
    if (!response.ok || data.status !== "success" || !data.data) {
      throw new Error(data.message || "Error al cargar datos del empleado");
    }

    actualizarInterfaz(data.data);
    
  } catch (error) {
    console.error("Error al cargar datos:", error);
    mostrarError("Error al cargar datos. Recarga la página. Detalles: " + error.message);
    
    // Datos de prueba para desarrollo local
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      console.warn("Usando datos de prueba para desarrollo");
      actualizarInterfaz({
        nombre: "Empleado",
        apellido: "Demo",
        tipoDocumento: "DNI",
        numeroDocumento: "12345678",
        estado: "Activo",
        email: "empleado@demo.com",
        numeroTelefono: "987654321",
        direccion: "Av. Ejemplo 123"
      });
    }
  }
};

// Actualizar la interfaz con los datos
const actualizarInterfaz = (empleado) => {
  // Datos personales
  document.getElementById('dispNombre').textContent = 
    `${empleado.nombre || ''} ${empleado.apellido || ''}`.trim() || 'No especificado';
  document.getElementById('dispTipo').textContent = empleado.tipoDocumento || 'No especificado';
  document.getElementById('dispNum').textContent = empleado.numeroDocumento || 'No especificado';
  document.getElementById('dispEstado').textContent = empleado.estado || 'No especificado';
  
  // Datos de contacto
  document.getElementById('dispEmail').textContent = empleado.email || 'No especificado';
  document.getElementById('dispTel').textContent = empleado.numeroTelefono || 'No especificado';
  document.getElementById('dispDireccion').textContent = empleado.direccion || 'No especificada';

  // Campos editables
  document.getElementById('inpNombre').value = `${empleado.nombre || ''} ${empleado.apellido || ''}`.trim();
  document.getElementById('inpTipo').value = empleado.tipoDocumento || '';
  document.getElementById('inpNum').value = empleado.numeroDocumento || '';
  document.getElementById('inpEstado').value = empleado.estado || '';
  document.getElementById('inpEmail').value = empleado.email || '';
  document.getElementById('inpTel').value = empleado.numeroTelefono || '';
  document.getElementById('inpDireccion').value = empleado.direccion || '';
};

// Event Listeners
DOM.modalCloseBtn.addEventListener('click', () => DOM.modal.classList.remove('active'));
DOM.closeModalPass.addEventListener('click', () => DOM.modalPass.classList.remove('active'));
DOM.btnEditPass.addEventListener('click', () => DOM.modalPass.classList.add('active'));

DOM.editBtn.addEventListener('click', () => {
  // Solo habilitar campos editables (email, teléfono y dirección)
  document.getElementById('inpEmail').disabled = false;
  document.getElementById('inpTel').disabled = false;
  document.getElementById('inpDireccion').disabled = false;
  DOM.editBtn.disabled = true;
  DOM.saveBtn.disabled = false;
});

DOM.saveBtn.addEventListener('click', guardarCambios);
DOM.btnGuardarClave.addEventListener('click', cambiarContraseña);

// Validar contraseña en tiempo real
if(DOM.claveNueva) {
  DOM.claveNueva.addEventListener('input', (e) => {
    validarContraseña(e.target.value);
  });
}

// Funciones principales
async function guardarCambios() {
  const campos = {
    email: document.getElementById('inpEmail'),
    telefono: document.getElementById('inpTel'),
    direccion: document.getElementById('inpDireccion')
  };

  // Mostrar loader
  DOM.saveBtn.disabled = true;
  DOM.saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

  // Validaciones
  const errores = [];
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(campos.email.value.trim())) {
    errores.push("El email no es válido.");
    campos.email.classList.add('invalid');
  } else {
    campos.email.classList.remove('invalid');
  }

  if (!/^[\d+\s]+$/.test(campos.telefono.value.trim())) {
    errores.push("El teléfono solo puede contener números y espacios.");
    campos.telefono.classList.add('invalid');
  } else {
    campos.telefono.classList.remove('invalid');
  }

  if (errores.length > 0) {
    mostrarError(errores.join('\n'));
    DOM.saveBtn.disabled = false;
    DOM.saveBtn.textContent = 'Guardar';
    return;
  }

  try {
    const datos = {
      email: campos.email.value.trim(),
      numeroTelefono: campos.telefono.value.trim(),
      direccion: campos.direccion.value.trim()
    };

    const response = await fetch('../controller/actualizarPerfilEmpleado.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    });

    // Verificar si la respuesta es JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      throw new Error(text || 'Respuesta no válida del servidor');
    }

    const resultado = await response.json();
    
    if (!response.ok) {
      throw new Error(resultado.message || "Error al guardar");
    }

    if (resultado.status === "success") {
      mostrarExito("Datos actualizados correctamente");
      // Actualizar vista
      document.getElementById('dispEmail').textContent = datos.email;
      document.getElementById('dispTel').textContent = datos.numeroTelefono;
      document.getElementById('dispDireccion').textContent = datos.direccion;
      // Deshabilitar edición
      Object.values(campos).forEach(campo => campo.disabled = true);
      DOM.editBtn.disabled = false;
      DOM.saveBtn.disabled = true;
      
      // Recargar datos para asegurar consistencia
      setTimeout(cargarDatosEmpleado, 1000);
    } else {
      throw new Error(resultado.message || "Error al guardar");
    }
  } catch (error) {
    console.error("Error al guardar cambios:", error);
    mostrarError(error.message || "Error al guardar cambios. Intenta nuevamente.");
  } finally {
    DOM.saveBtn.disabled = false;
    DOM.saveBtn.textContent = 'Guardar';
  }
}

async function cambiarContraseña() {
  const campos = {
    actual: document.getElementById('claveActual'),
    nueva: document.getElementById('claveNueva'),
    confirmar: document.getElementById('claveConfirmar')
  };

  // Mostrar loader
  DOM.btnGuardarClave.disabled = true;
  DOM.btnGuardarClave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

  try {
    // Validaciones
    const errores = [];
    if (!campos.actual.value || !campos.nueva.value || !campos.confirmar.value) {
      errores.push("Todos los campos son obligatorios.");
    }
    
    if (campos.nueva.value.length < 6) {
      errores.push("La nueva contraseña debe tener al menos 6 caracteres.");
    }
    
    if (!/[A-Z]/.test(campos.nueva.value) || !/[0-9]/.test(campos.nueva.value)) {
      errores.push("La contraseña debe contener al menos una mayúscula y un número.");
    }
    
    if (campos.nueva.value !== campos.confirmar.value) {
      errores.push("Las contraseñas no coinciden.");
    }

    if (errores.length > 0) {
      throw new Error(errores.join('\n'));
    }

    const response = await fetch('../controller/actualizarClaveEmpleado.php', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest' // Para identificar peticiones AJAX
      },
      body: JSON.stringify({
        claveActual: campos.actual.value,
        claveNueva: campos.nueva.value,
        claveConfirmar: campos.confirmar.value
      })
    });

    // Verificar si la respuesta es JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      throw new Error(text || 'Respuesta no válida del servidor');
    }

    const resultado = await response.json();
    
    if (!response.ok) {
      throw new Error(resultado.message || "Error al cambiar contraseña");
    }

    if (resultado.status === 'success') {
      mostrarExito("Contraseña actualizada correctamente");
      DOM.modalPass.classList.remove("active");
      // Limpiar campos
      campos.actual.value = '';
      campos.nueva.value = '';
      campos.confirmar.value = '';
    } else {
      throw new Error(resultado.message || "Error al cambiar contraseña");
    }
  } catch (error) {
    console.error("Error al cambiar contraseña:", error);
    
    // Manejar errores de JSON parse
    let errorMessage = error.message;
    try {
      if (error.message.includes('{')) {
        const errorObj = JSON.parse(error.message);
        errorMessage = errorObj.message || error.message;
      }
    } catch (e) {
      // Si no se puede parsear, mantener el mensaje original
    }
    
    mostrarError(errorMessage || "Error al cambiar contraseña. Verifica los datos e intenta nuevamente.");
  } finally {
    DOM.btnGuardarClave.disabled = false;
    DOM.btnGuardarClave.textContent = 'Guardar';
  }
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
  cargarDatosEmpleado();
  
  // Configurar modal para cerrar al hacer clic fuera
  if(DOM.modal) {
    DOM.modal.addEventListener('click', (e) => {
      if (e.target === DOM.modal) {
        DOM.modal.classList.remove('active');
      }
    });
  }
  
  if(DOM.modalPass) {
    DOM.modalPass.addEventListener('click', (e) => {
      if (e.target === DOM.modalPass) {
        DOM.modalPass.classList.remove('active');
      }
    });
  }
});
</script>
</body>
</html>