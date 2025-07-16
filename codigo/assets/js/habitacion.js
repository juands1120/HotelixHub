// ==================== VARIABLES GLOBALES ====================
let habitaciones = []; 
let habitacionEditando = null; 

const container = document.getElementById("habitacionesContainer");
const form = document.getElementById("formHabitacion");

// ==================== FUNCIONES PARA COMUNICACIÓN CON BACKEND ====================
async function enviarDatos(accion, datos = {}, numero = null) {
    const formData = new FormData();
    formData.append("accion", accion);

    if (datos.numero) formData.append("numero", datos.numero);
    if (datos.tipo) formData.append("tipo", datos.tipo);
    if (datos.piso) formData.append("piso", datos.piso);
    if (datos.precio) formData.append("precio", datos.precio);
    if (datos.servicios) formData.append("servicios", datos.servicios.join(","));
    if (datos.estado) formData.append("estado", datos.estado);

    const imagenInput = document.getElementById("imagenHabitacion");
    if (imagenInput?.files[0]) {
        formData.append("imagen", imagenInput.files[0]);
    } else if (datos.imagenRuta) {
        formData.append("imagenRuta", datos.imagenRuta);
    }

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

// ==================== CARGAR HABITACIONES AL INICIAR ====================
async function cargarHabitaciones() {
    try {
        const response = await fetch('/HotelixHub/codigo/api/apiHabitaciones.php?accion=listar');
        habitaciones = await response.json();
        actualizarUI();
    } catch (error) {
        console.error("Error al cargar habitaciones:", error);
    }
}

// ==================== FUNCIONES DE UI ====================
document.getElementById("habitacion").onclick = () => abrirFormulario();

function abrirFormulario(data) {
    form.reset();
    limpiarErrores();
    document.getElementById("modalHabitacion").style.display = "block";

    const titulo = document.getElementById("modalTitulo");
    titulo.textContent = data ? "Editar Habitación" : "Agregar Nueva Habitación";
    document.getElementById("eliminarBtn").style.display = data ? "inline-block" : "none";
    habitacionEditando = data;

    const previewsAntiguos = form.querySelectorAll('.imagen-preview-actual');
    previewsAntiguos.forEach(preview => preview.remove());

    if (data) {
        document.getElementById("numHabitacion").value = data.numero;
        document.getElementById("tipoHabitacion").value = data.tipo;
        document.getElementById("piso").value = data.piso;
        document.getElementById("precio").value = data.precio;
        document.getElementById("servicios").value = data.servicios.join(", ");

        const imagenPreview = document.createElement('div');
        imagenPreview.classList.add('imagen-preview-actual');
        imagenPreview.innerHTML = `
            <p>Imagen actual:</p>
            <img src="/HotelixHub/codigo/${data.imagen}" style="max-width: 100px; margin: 10px 0;">
        `;
        form.insertBefore(imagenPreview, form.querySelector('button[type="submit"]'));
        document.getElementById("numHabitacion").readOnly = true;
    } else {
        document.getElementById("numHabitacion").readOnly = false;
    }
}

function cerrarModal() {
    form.reset();
    habitacionEditando = null;
    document.getElementById("modalHabitacion").style.display = "none";
}

function cerrarModalExito() {
    document.getElementById("modalExito").style.display = "none";
}

// ==================== SUBMIT DEL FORMULARIO ====================
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

    if (!habitacionEditando && file) {
        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            mostrarError("imagenHabitacion", "Solo se permiten imágenes JPG o PNG.");
            isValid = false;
        }
    }


    if (!isValid) return;

    const nuevaHabitacion = {
        numero,
        tipo,
        piso: pisoVal,
        precio: precioVal,
        servicios,
        estado: habitacionEditando ? habitacionEditando.estado : "Disponible",
        imagenRuta: form.getAttribute("data-imagen-actual") || null 
    };

    try {
        const accion = habitacionEditando ? "editar" : "crear";
        const respuesta = await enviarDatos(accion, nuevaHabitacion);

        if (respuesta.exito) {
            cerrarModal();
            document.getElementById("modalExito").style.display = "block";

            if (habitacionEditando) {
                const index = habitaciones.findIndex(h => h.numero === nuevaHabitacion.numero);
                if (index !== -1 && respuesta.datos) habitaciones[index] = respuesta.datos;
            } else {
                if (respuesta.datos) habitaciones.push(respuesta.datos);
            }

            actualizarUI();
            habitacionEditando = null;
        } else {
            mostrarMensaje('error', "Error al guardar: " + (respuesta.error || "Error desconocido"));
        }
    } catch (error) {
        console.error("Error:", error);
        mostrarMensaje('error', "Error al guardar");
    }
});

// ==================== RENDERIZADO DE HABITACIONES ====================
function renderHabitacion(data) {
    const div = document.createElement("div");
    div.className = "habitacion-card";
    div.setAttribute("data-piso", data.piso);

    div.innerHTML = `
        <h3>Habitación ${data.numero}</h3>
        <ul class="habitacion-features">
            <li>Piso: ${data.piso}</li>
            <li>Tipo: ${data.tipo}</li>
            <li>Servicios: ${Array.isArray(data.servicios) ? data.servicios.join(", ") : "Sin servicios"}</li>
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

    div.querySelector(".editar-btn").onclick = () => abrirFormulario(data);
    container.appendChild(div);

    const estadoSelect = div.querySelector(".estado-select");
    aplicarEstiloEstado(estadoSelect);

    estadoSelect.addEventListener("change", async function () {
        const datosActualizados = {
            numero: data.numero,
            tipo: data.tipo,
            piso: data.piso,
            precio: data.precio,
            servicios: data.servicios,
            estado: this.value,
            imagenRuta: data.imagen
        };

        aplicarEstiloEstado(this);

        const respuesta = await enviarDatos("editar", datosActualizados);
        if (!respuesta.exito) {
            mostrarMensaje('error', 'Error al cambiar el estado');
        }
    });
}

// ==================== FUNCIONES AUXILIARES ====================
function aplicarEstiloEstado(select) {
    select.classList.remove("estado-disponible", "estado-ocupada", "estado-mantenimiento");
    select.classList.add(`estado-${select.value.toLowerCase()}`);
}

function actualizarUI() {
    container.innerHTML = "";
    habitaciones.forEach(h => renderHabitacion(h));
}

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

function mostrarError(id, mensaje) {
    const el = document.getElementById(id);
    const err = document.createElement("span");
    err.className = "error-js";
    err.style.color = "red";
    err.style.fontSize = "0.8rem";
    err.textContent = mensaje;
    el.parentNode.appendChild(err);
    el.classList.add("input-error");
}

function limpiarErrores() {
    document.querySelectorAll(".error-js").forEach(e => e.remove());
    document.querySelectorAll(".input-error").forEach(e => e.classList.remove("input-error"));
}

// ==================== EVENTOS ADICIONALES ====================
["numHabitacion", "piso", "precio"].forEach(id => {
    document.getElementById(id).addEventListener("keydown", e => {
        if (!/[0-9]/.test(e.key) && !["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"].includes(e.key)) {
            e.preventDefault();
        }
    });
});

document.getElementById("filtroPiso").addEventListener("change", function () {
    const pisoSeleccionado = this.value;
    const tarjetas = document.querySelectorAll(".habitacion-card");
    tarjetas.forEach(tarjeta => {
        const pisoHabitacion = tarjeta.getAttribute("data-piso");
        tarjeta.style.display = pisoSeleccionado === "todos" || pisoHabitacion === pisoSeleccionado ? "block" : "none";
    });
});

document.addEventListener("DOMContentLoaded", cargarHabitaciones);

// ========================== TOAST MENSAJES ==========================
window.mostrarMensaje = function(tipo, mensaje) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `mensaje-toast ${tipo}`;
    msgDiv.textContent = mensaje;
    document.body.appendChild(msgDiv);

    setTimeout(() => {
        msgDiv.style.opacity = '1';
        msgDiv.style.transform = 'translateY(0)';
    }, 100);

    setTimeout(() => {
        msgDiv.style.opacity = '0';
        msgDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => msgDiv.remove(), 500);
    }, 3000);
};
