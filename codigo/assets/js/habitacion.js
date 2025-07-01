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

async function enviarDatos(accion, datos = {}, numero = null) {
    const formData = new FormData();
    formData.append("accion", accion);

    // Datos comunes
    if (datos.numero) formData.append("numero", datos.numero);
    if (datos.tipo) formData.append("tipo", datos.tipo);
    if (datos.piso) formData.append("piso", datos.piso);
    if (datos.precio) formData.append("precio", datos.precio);
    if (datos.servicios) formData.append("servicios", datos.servicios.join(","));
    if (datos.estado) formData.append("estado", datos.estado);

    // Imagen si existe
    const imagenInput = document.getElementById("imagenHabitacion");
    if (imagenInput?.files[0]) {
        formData.append("imagen", imagenInput.files[0]);
    } else if (datos.imagenRuta) {
        formData.append("imagenRuta", datos.imagenRuta); // para conservar la imagen anterior
    }

    // Enviar al servidor
    try {
        const response = await fetch("/HotelixHub/codigo/api/apiHabitaciones.php", {
            method: "POST",
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
        const response = await fetch('/HotelixHub/codigo/api/apiHabitaciones.php?accion=listar');
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
console.log("EDITANDO:", habitacionEditando);

function abrirFormulario(data) {
    form.reset(); // Limpiar formulario
    limpiarErrores(); // Eliminar mensajes de error previos
    document.getElementById("modalHabitacion").style.display = "block";

    // Configurar título
    const titulo = document.getElementById("modalTitulo");
    titulo.textContent = data ? "Editar Habitación" : "Agregar Nueva Habitación";
    
    // Mostrar botón de eliminar solo en modo edición
    document.getElementById("eliminarBtn").style.display = data ? "inline-block" : "none";
    
    habitacionEditando = data; // Guardar referencia a la habitación en edición

    // Eliminar previews anteriores
    const previewsAntiguos = form.querySelectorAll('.imagen-preview-actual');
    previewsAntiguos.forEach(preview => preview.remove());

    // Si estamos editando, llenar el formulario con los datos existentes
    if (data) {
        document.getElementById("numHabitacion").value = data.numero;
        document.getElementById("tipoHabitacion").value = data.tipo;
        document.getElementById("piso").value = data.piso;
        document.getElementById("precio").value = data.precio;
        document.getElementById("servicios").value = data.servicios.join(", ");

    // Mostrar imagen actual (nuevo)
        const imagenPreview = document.createElement('div');
        imagenPreview.classList.add('imagen-preview-actual');
        imagenPreview.innerHTML = `
            <p>Imagen actual:</p>
            <img src="/HotelixHub/codigo/${data.imagen}" style="max-width: 100px; margin: 10px 0;">
        `;
        form.insertBefore(imagenPreview, form.querySelector('button[type="submit"]'));
        // Hacer el campo de número de habitación readonly al editar
        document.getElementById("numHabitacion").readOnly = true;
    } else {
        // Asegurarse de que no es readonly al crear
        document.getElementById("numHabitacion").readOnly = false;
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
    e.preventDefault();
    limpiarErrores();

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

    let isValid = true;

    if (!/^\d+$/.test(numero) || numeroVal < 200 || numeroVal > 511) {
        mostrarError("numHabitacion", "Número de habitación entre 200 y 511");
        isValid = false;
    }

    if (habitaciones.some(h => h.numero === numero) && !habitacionEditando) {
        mostrarError("numHabitacion", "Este número ya está registrado");
        isValid = false;
    }

    if (tipo === "") {
        mostrarError("tipoHabitacion", "Seleccione tipo de habitación");
        isValid = false;
    }

    if (isNaN(pisoVal) || pisoVal < 2 || pisoVal > 5) {
        mostrarError("piso", "Piso válido entre 2 y 5");
        isValid = false;
    }

    if (isNaN(precioVal) || precioVal <= 0 || precioVal > 1000000) {
        mostrarError("precio", "Precio inválido (máx 1 millón)");
        isValid = false;
    }

    if (servicios.length === 0) {
        mostrarError("servicios", "Seleccione al menos un servicio");
        isValid = false;
    }

    if (!habitacionEditando && (!file || file.size > 1024 * 1024)) {
        mostrarError("imagenHabitacion", "Imagen obligatoria (máx 1MB)");
        isValid = false;
    }

    if (!isValid) return;

    const nuevaHabitacion = {
        numero: document.getElementById("numHabitacion").value.trim(),
        tipo: document.getElementById("tipoHabitacion").value,
        piso: parseInt(document.getElementById("piso").value.trim()),
        precio: parseFloat(document.getElementById("precio").value.trim()),
        servicios: document.getElementById("servicios").value.split(",").map(s => s.trim()),
        estado: habitacionEditando ? habitacionEditando.estado : "Disponible",
        imagenRuta: form.getAttribute("data-imagen-actual") || null 
    };

    try {
        const accion = habitacionEditando ? "editar" : "crear";
        const respuesta = await enviarDatos(accion, nuevaHabitacion);

        if (respuesta.exito) {
            cerrarModal();
            document.getElementById("modalExito").style.display = "block";

            // Actualización clave: Modificar el array `habitaciones` en memoria
            if (habitacionEditando) {
                // 1. Buscar la habitación editada en el array
                const index = habitaciones.findIndex(h => h.numero === nuevaHabitacion.numero);
                
                if (index !== -1) {
                    // 2. Sobrescribir los datos antiguos con los nuevos
                    if (respuesta.datos) {
                        habitaciones[index] = respuesta.datos; // Reemplazar por lo que devuelve el backend
                    }

                }
            } else {
                // Si es una nueva habitación, agregarla al array
                if (respuesta.datos) habitaciones.push(respuesta.datos);
            }

            actualizarUI(); // Refrescar la interfaz con los datos actualizados
            habitacionEditando = null;
        } else {
            alert("Error al guardar: " + (respuesta.error || "Error desconocido"));
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error al guardar");
    }
});


// ==================== RENDERIZADO DE HABITACIONES ====================

/**
 * Crea y renderiza una tarjeta HTML para una habitación
 * @param {object} data - Datos de la habitación a renderizar
 */
// =============== FUNCIÓN AUXILIAR PARA VALIDAR LA IMAGEN ===============
function validarImagen(imagen) {
    if (!imagen || typeof imagen !== "string") return 'imgHabitacion/no-imagen.png';

    if (!imagen.startsWith('data:image')) return 'imgHabitacion/no-imagen.png';

    const partes = imagen.split(',');
    if (partes.length < 2) return 'imgHabitacion/no-imagen.png';

    const header = partes[0];
    const base64 = partes[1].split(':')[0]; // Elimina errores como ":1"
    return `${header},${base64}`;
}

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
            <li>Servicios Incluidos: ${(Array.isArray(data.servicios) ? data.servicios.join(", ") : "Sin servicios")}</li>
        </ul>
        <img src="/HotelixHub/codigo/${data.imagen}" alt="Imagen habitación" />   
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
        const datosActualizados = {
            numero: data.numero,
            tipo: data.tipo,
            piso: data.piso,
            precio: data.precio,
            servicios: data.servicios,
            estado: this.value,
            imagenRuta: data.imagen // mantener la imagen actual
        };

        aplicarEstiloEstado(this);

        const respuesta = await enviarDatos("editar", datosActualizados);
        if (!respuesta.exito) {
            alert("Error al cambiar el estado");
        }
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
            const respuesta = await enviarDatos("eliminar", { numero: habitacionEditando.numero });
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