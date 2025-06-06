/**
 * MÓDULO DE GESTIÓN DE HABITACIONES
 * 
 * Este script maneja la lógica de frontend para:
 * - Cargar habitaciones desde el backend
 * - Crear/editar/eliminar habitaciones
 * - Validar formularios
 * - Renderizar la interfaz de usuario
 * - Filtrar habitaciones por piso
 */

// ==================== VARIABLES GLOBALES ====================
let habitaciones = []; // Almacena el listado de habitaciones cargadas desde MySQL
let habitacionEditando = null; // Referencia a la habitación que se está editando (null para creación)

// Referencias a elementos DOM principales
const container = document.getElementById("habitacionesContainer"); // Contenedor de las tarjetas de habitaciones
const form = document.getElementById("formHabitacion"); // Formulario de creación/edición

// ==================== FUNCIONES PARA COMUNICACIÓN CON BACKEND (PHP/MYSQL) ====================

/**
 * Envía datos al backend mediante fetch API
 * @param {string} accion - Tipo de operación ('crear', 'editar', 'eliminar')
 * @param {object} datos - Objeto con los datos de la habitación
 * @param {string|null} numero - Número de habitación (opcional, usado para edición/eliminación)
 * @returns {Promise<object>} Respuesta del servidor
 */
async function enviarDatos(accion, datos, numero = null) {
    const formData = new FormData();
    formData.append('accion', accion);
    if (numero) formData.append('numero', numero);
    formData.append('datos', JSON.stringify(datos));

    try {
        const response = await fetch('api_habitaciones.php', {
            method: 'POST',
            body: formData
        });
        return await response.json();
    } catch (error) {
        console.error("Error en la petición:", error);
        return { exito: false, error: "Error de conexión" };
    }
}

/**
 * Carga las habitaciones desde el backend al iniciar la aplicación
 * Actualiza la variable global 'habitaciones' y refresca la UI
 */
async function cargarHabitaciones() {
    try {
        const response = await fetch('api_habitaciones.php?accion=listar');
        habitaciones = await response.json();
        actualizarUI();
    } catch (error) {
        console.error("Error al cargar habitaciones:", error);
    }
}

// ==================== FUNCIONES DE INTERFAZ DE USUARIO ====================

// Evento para abrir el modal al hacer clic en el botón "Agregar Habitación"
document.getElementById("habitacion").onclick = () => abrirFormulario();

/**
 * Abre el formulario modal para crear o editar una habitación
 * @param {object|null} data - Datos de la habitación a editar (null para creación)
 */
function abrirFormulario(data = null) {
    form.reset(); // Limpiar formulario
    limpiarErrores(); // Eliminar mensajes de error previos
    document.getElementById("modalHabitacion").style.display = "block";
    
    // Configurar título del modal según sea creación o edición
    document.getElementById("modalTitulo").textContent = data ? "Editar Habitación" : "Agregar Nueva Habitación";
    
    // Mostrar botón de eliminar solo en modo edición
    document.getElementById("eliminarBtn").style.display = data ? "inline-block" : "none";
    
    habitacionEditando = data; // Guardar referencia a la habitación en edición

    // Si estamos editando, llenar el formulario con los datos existentes
    if (data) {
        document.getElementById("numHabitacion").value = data.numero;
        document.getElementById("tipoHabitacion").value = data.tipo;
        document.getElementById("piso").value = data.piso;
        document.getElementById("precio").value = data.precio;
        document.getElementById("servicios").value = data.servicios.join(", ");
    }
}

/**
 * Cierra el modal de formulario y limpia las variables relacionadas
 */
function cerrarModal() {
    form.reset();
    habitacionEditando = null;
    document.getElementById("modalHabitacion").style.display = "none";
}

/**
 * Cierra el modal de éxito después de una operación
 */
function cerrarModalExito() {
    document.getElementById("modalExito").style.display = "none";
}

// ==================== MANEJO DEL FORMULARIO (SUBMIT) ====================

// Evento submit del formulario para crear/editar habitaciones
form.addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevenir envío tradicional del formulario
    limpiarErrores(); // Limpiar errores previos

    // Obtener y procesar valores del formulario
    const numero = document.getElementById("numHabitacion").value.trim();
    const numeroVal = parseInt(numero);
    const tipo = document.getElementById("tipoHabitacion").value;
    const piso = document.getElementById("piso").value.trim();
    const pisoVal = parseInt(piso);
    const precio = document.getElementById("precio").value.trim();
    const precioVal = parseFloat(precio);
    const serviciosTexto = document.getElementById("servicios").value.trim();
    const servicios = serviciosTexto.split(",").map(s => s.trim()).filter(Boolean);
    const imagenInput = document.getElementById("imagenHabitacion");
    const file = imagenInput.files[0];

    let isValid = true; // Bandera para validación

    // ========= VALIDACIONES DEL FORMULARIO =========

    // Validación número de habitación (200-511)
    if (!/^\d+$/.test(numero) || numeroVal < 200 || numeroVal > 511) {
        mostrarError("numHabitacion", "Número de habitación entre 200 y 511");
        isValid = false;
    }

    // Validación que el número no esté repetido (excepto en edición)
    if (habitaciones.some(h => h.numero === numero) && !habitacionEditando) {
        mostrarError("numHabitacion", "Este número ya está registrado");
        isValid = false;
    }

    // Validación tipo de habitación (no vacío)
    if (tipo === "") {
        mostrarError("tipoHabitacion", "Seleccione tipo de habitación");
        isValid = false;
    }

    // Validación piso (2-5)
    if (isNaN(pisoVal) || pisoVal < 2 || pisoVal > 5) {
        mostrarError("piso", "Piso válido entre 2 y 5");
        isValid = false;
    }

    // Validación precio (positivo, máximo 1 millón)
    if (isNaN(precioVal) || precioVal <= 0 || precioVal > 1000000) {
        mostrarError("precio", "Precio inválido (máx 1 millón)");
        isValid = false;
    }

    // Validación servicios (al menos uno)
    if (servicios.length === 0) {
        mostrarError("servicios", "Seleccione al menos un servicio");
        isValid = false;
    }

    // Validación imagen (obligatoria solo para creación, máximo 1MB)
    if (!habitacionEditando && (!file || file.size > 1024 * 1024)) {
        mostrarError("imagenHabitacion", "Imagen obligatoria (máx 1MB)");
        isValid = false;
    }

    if (!isValid) return; // Detener proceso si hay errores

    // Procesar imagen si existe (convertir a Base64)
    const reader = new FileReader();
    reader.onload = async () => {
        // Crear objeto con los datos de la habitación
        const nuevaHabitacion = {
            numero,
            tipo,
            piso,
            precio: precioVal,
            servicios,
            imagen: file ? reader.result : habitacionEditando?.imagen, // Usar imagen nueva o mantener la existente
            estado: habitacionEditando ? habitacionEditando.estado : "Disponible" // Mantener estado o establecer como disponible
        };

        try {
            // Determinar si es creación o edición
            const accion = habitacionEditando ? "editar" : "crear";
            const respuesta = await enviarDatos(accion, nuevaHabitacion, numero);

            if (respuesta.exito) {
                cerrarModal();
                document.getElementById("modalExito").style.display = "block";
                await cargarHabitaciones(); // Refrescar lista desde MySQL
            } else {
                alert("Error al guardar: " + (respuesta.error || ""));
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Error al guardar");
        }
    };

    // Si hay archivo, leerlo, sino continuar directamente
    if (file) reader.readAsDataURL(file);
    else reader.onload();
});

// ==================== RENDERIZADO DE HABITACIONES ====================

/**
 * Crea y renderiza una tarjeta HTML para una habitación
 * @param {object} data - Datos de la habitación a renderizar
 */
function renderHabitacion(data) {
    const div = document.createElement("div");
    div.className = "habitacion-card";
    div.setAttribute("data-piso", data.piso); // Atributo para filtrado
    
    // Plantilla HTML de la tarjeta
    div.innerHTML = `
        <h3>Habitación ${data.numero}</h3>
        <ul class="habitacion-features">
            <li>Piso: ${data.piso}</li>
            <li>Tipo de habitación: ${data.tipo}</li>
            <li>Servicios Incluidos: ${data.servicios.join(", ")}</li>
        </ul>
        <div class="img"><img src="${data.imagen}" /></div>
        <div class="habitacion-precio">$${parseInt(data.precio).toLocaleString()} COP</div>
        <select class="estado-select">
            <option ${data.estado === "Disponible" ? "selected" : ""}>Disponible</option>
            <option ${data.estado === "Ocupada" ? "selected" : ""}>Ocupada</option>
            <option ${data.estado === "Mantenimiento" ? "selected" : ""}>Mantenimiento</option>
        </select>
        <button class="editar-btn">Editar</button>
    `;
    
    // Evento para el botón editar
    div.querySelector(".editar-btn").onclick = () => abrirFormulario(data);
    container.appendChild(div);

    // Configurar el selector de estado
    const estadoSelect = div.querySelector(".estado-select");
    aplicarEstiloEstado(estadoSelect);

    // Evento para cambios de estado
    estadoSelect.addEventListener("change", async function () {
        data.estado = this.value;
        aplicarEstiloEstado(this);
        await enviarDatos("editar", data, data.numero); // Actualizar estado en backend
    });
}

// ==================== FUNCIONES AUXILIARES ====================

/**
 * Aplica estilos CSS según el estado seleccionado
 * @param {HTMLSelectElement} select - Elemento select del estado
 */
function aplicarEstiloEstado(select) {
    // Limpiar clases previas
    select.classList.remove("estado-disponible", "estado-ocupada", "estado-mantenimiento");
    // Añadir clase correspondiente al estado actual
    select.classList.add(`estado-${select.value.toLowerCase()}`);
}

/**
 * Actualiza la interfaz de usuario renderizando todas las habitaciones
 */
function actualizarUI() {
    container.innerHTML = ""; // Limpiar contenedor
    habitaciones.forEach(h => renderHabitacion(h)); // Renderizar cada habitación
}

/**
 * Maneja la eliminación de una habitación
 */
document.getElementById("eliminarBtn").onclick = async () => {
    if (confirm("¿Eliminar esta habitación?")) {
        try {
            const respuesta = await enviarDatos("eliminar", null, habitacionEditando.numero);
            if (respuesta.exito) {
                await cargarHabitaciones();
                cerrarModal();
            }
        } catch (error) {
            console.error("Error al eliminar:", error);
        }
    }
};

/**
 * Muestra un mensaje de error en un campo del formulario
 * @param {string} id - ID del campo con error
 * @param {string} mensaje - Mensaje de error a mostrar
 */
function mostrarError(id, mensaje) {
    const el = document.getElementById(id);
    const err = document.createElement("span");
    err.className = "error-js";
    err.style.color = "red";
    err.style.fontSize = "0.8rem";
    err.textContent = mensaje;
    el.parentNode.appendChild(err);
    el.classList.add("input-error"); // Resaltar campo con error
}

/**
 * Limpia todos los mensajes de error del formulario
 */
function limpiarErrores() {
    document.querySelectorAll(".error-js").forEach(e => e.remove());
    document.querySelectorAll(".input-error").forEach(e => e.classList.remove("input-error"));
}

// ==================== EVENTOS ADICIONALES ====================

// Bloquear entrada de letras en campos numéricos
["numHabitacion", "piso", "precio"].forEach(id => {
    document.getElementById(id).addEventListener("keydown", e => {
        if (!/[0-9]/.test(e.key) && !["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"].includes(e.key)) {
            e.preventDefault();
        }
    });
});

// Filtro por piso
document.getElementById("filtroPiso").addEventListener("change", function () {
    const pisoSeleccionado = this.value;
    const tarjetas = document.querySelectorAll(".habitacion-card");
    
    tarjetas.forEach(tarjeta => {
        const pisoHabitacion = tarjeta.getAttribute("data-piso");
        // Mostrar u ocultar según el filtro
        tarjeta.style.display = pisoSeleccionado === "todos" || pisoHabitacion === pisoSeleccionado ? "block" : "none";
    });
});

// ==================== INICIALIZACIÓN ====================

// Cargar datos cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", cargarHabitaciones);