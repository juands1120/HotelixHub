const { jsPDF } = window.jspdf;
// Scroll botones
const contenedor = document.querySelector('.contenedor-productos');
const btnIzq = document.getElementById('btn-izq');
const btnDer = document.getElementById('btn-der');
const scrollCantidad = 320;

btnIzq?.addEventListener('click', () => {
    contenedor.scrollBy({ left: -scrollCantidad, behavior: 'smooth' });
});

btnDer?.addEventListener('click', () => {
    contenedor.scrollBy({ left: scrollCantidad, behavior: 'smooth' });
});

// Cerrar barra lateral si se hace clic fuera
const barraLateral = document.querySelector('.barra-lateral');
const toggle = document.querySelector('.toggle-menu');
document.addEventListener('click', function (e) {
    if (barraLateral?.classList.contains('abierta') && !barraLateral.contains(e.target) && !toggle.contains(e.target)) {
        barraLateral.classList.remove('abierta');
    }
});

// Menú de sesión
function toggleSesionMenu() {
    const menu = document.getElementById("menuSesion");
    if (menu) {
        menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
    }
}

function cerrarSesion() {
    alert("Sesión cerrada correctamente.");
}

// Carrusel de imágenes
const imagenesCarrusel = [
    {
        src: "./img_ProductosCliente/promocion.png",
        titulo: "¡Sabores que despiertan tus sentidos!",
        descripcion: "Descubre la experiencia gourmet más exclusiva en nuestro hotel. ¡Deléitate hoy!"
    },
    {
        src: "img/promocion2.png",
        titulo: "Comodidad sin límites",
        descripcion: "Servicio a la habitación 24/7. Relájate, nosotros nos encargamos del resto."
    },
    {
        src: "img/promocion3.png",
        titulo: "Tus vacaciones, al mejor precio",
        descripcion: "Ofertas irresistibles por tiempo limitado. ¡Reserva ahora y ahorra!"
    }
];

let indiceCarrusel = 0;
function cambiarCarrusel() {
    const carrusel = document.getElementById("carrusel");
    const img = carrusel?.querySelector("img");
    const titulo = document.getElementById("textoCarruselTitulo");
    const descripcion = document.getElementById("textoCarruselDescripcion");

    carrusel?.classList.add("fade-out");

    setTimeout(() => {
        indiceCarrusel = (indiceCarrusel + 1) % imagenesCarrusel.length;
        const nuevo = imagenesCarrusel[indiceCarrusel];

        if (img && titulo && descripcion) {
            img.src = nuevo.src;
            titulo.textContent = nuevo.titulo;
            descripcion.textContent = nuevo.descripcion;
        }

        carrusel?.classList.remove("fade-out");
    }, 1000);
}
setInterval(cambiarCarrusel, 10000);

// Filtro de productos y servicios
const tarjetas = document.querySelectorAll('.tarjeta-producto');
const btnServicios = document.querySelectorAll('.menu-btn')[0];
const btnProductos = document.querySelectorAll('.menu-btn')[1];
const btnBuscar = document.getElementById('btn-buscar');
const inputBusqueda = document.getElementById('input-busqueda');
const categorias = document.querySelector('.categorias');
const btnsCategoria = document.querySelectorAll('.btn-categoria');
const contenedorTarjetas = document.querySelector('.contenedor-productos');

const mensajeNoResultado = document.createElement('p');
mensajeNoResultado.textContent = 'No se encontraron resultados.';
mensajeNoResultado.style.fontFamily = 'Outfit, sans-serif';
mensajeNoResultado.style.color = '#333';
mensajeNoResultado.style.marginTop = '20px';
mensajeNoResultado.style.display = 'none';
contenedorTarjetas?.appendChild(mensajeNoResultado);

btnProductos?.addEventListener('click', () => {
    if (categorias) categorias.style.display = 'flex';
    mensajeNoResultado.style.display = 'none';
    mostrarTarjetas('producto');
});

btnServicios?.addEventListener('click', () => {
    if (categorias) categorias.style.display = 'none';
    mensajeNoResultado.style.display = 'none';
    mostrarTarjetas('servicio');
});

btnsCategoria.forEach(btn => {
    btn.addEventListener('click', () => {
        const categoria = btn.querySelector('p')?.textContent.trim().toLowerCase();
        if (categoria) filtrarPorCategoria(categoria);
    });
});

btnBuscar?.addEventListener('click', () => {
    const texto = inputBusqueda?.value.trim().toLowerCase();
    buscarPorTexto(texto);
});

function mostrarTarjetas(tipo) {
    let hayResultados = false;
    tarjetas.forEach(t => {
        const esTipo = t.dataset.tipo === tipo;
        t.style.display = esTipo ? 'flex' : 'none';
        if (esTipo) hayResultados = true;
    });
    mensajeNoResultado.style.display = hayResultados ? 'none' : 'block';
}

function filtrarPorCategoria(categoria) {
    let hayResultados = false;
    tarjetas.forEach(t => {
        const tipo = t.dataset.tipo;
        const cat = t.dataset.categoria || '';
        const coincide = tipo === 'producto' && cat === categoria;
        t.style.display = coincide ? 'flex' : 'none';
        if (coincide) hayResultados = true;
    });
    mensajeNoResultado.style.display = hayResultados ? 'none' : 'block';
}

function buscarPorTexto(texto) {
    let hayResultados = false;
    tarjetas.forEach(t => {
        const nombre = t.querySelector('h3')?.textContent.toLowerCase() || '';
        const coincide = nombre.includes(texto);
        t.style.display = coincide ? 'flex' : 'none';
        if (coincide) hayResultados = true;
    });
    mensajeNoResultado.style.display = hayResultados ? 'none' : 'block';
}

mostrarTarjetas('producto');

// Carrito de compras
const carrito = [];
const botonesAgregar = document.querySelectorAll('.btn-agregar');
const subtotalSpan = document.getElementById('subtotal');
const ivaSpan = document.getElementById('iva');
const totalSpan = document.getElementById('total');

const botonesCantidad = document.querySelectorAll('.btn-cantidad');
botonesCantidad.forEach(btn => {
    btn.addEventListener('click', () => {
        const cantidadSpan = btn.parentElement.querySelector('.cantidad');
        let cantidad = parseInt(cantidadSpan.textContent);
        cantidad += (btn.textContent === '+' ? 1 : -1);
        cantidadSpan.textContent = Math.max(cantidad, 1);
    });
});

botonesAgregar.forEach(btn => {
    btn.addEventListener('click', () => {
        const tarjeta = btn.closest('.tarjeta-producto');
        const nombre = tarjeta.querySelector('h3')?.textContent;
        const precio = parseFloat(tarjeta.querySelector('.precio')?.textContent.replace(/[^0-9\.]/g, ''));
        const cantidad = parseInt(tarjeta.querySelector('.cantidad')?.textContent);
        const img = tarjeta.querySelector('img')?.src;

        if (!nombre || isNaN(precio) || isNaN(cantidad)) return;

        const existente = carrito.find(p => p.nombre === nombre);
        if (existente) {
            existente.cantidad += cantidad;
        } else {
            carrito.push({ nombre, precio, cantidad, img });
        }

        renderizarCarrito();
    });
});

function renderizarCarrito() {
    const listaCarrito = document.querySelector('.lista-carrito');
    if (!listaCarrito) return;

    listaCarrito.innerHTML = '';
    let subtotal = 0;

    carrito.forEach(producto => {
        listaCarrito.innerHTML += `
            <div class="item-carrito">
                <img src="${producto.img}" alt="${producto.nombre}">
                <div>
                    <p>${producto.nombre}</p>
                    <p>${Number(producto.precio).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 })} x ${producto.cantidad}</p>
                </div>
            </div>
        `;
        subtotal += producto.precio * producto.cantidad;
    });

    const iva = subtotal * 0.19;
    const total = subtotal + iva;

    subtotalSpan.textContent = subtotal.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
    ivaSpan.textContent = iva.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
    totalSpan.textContent = total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' });
}


document.getElementById('formPago')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const metodo = document.getElementById('metodo');
    const tarjeta = document.getElementById('tarjeta');

    // Mostrar u ocultar campo de tarjeta dinámicamente según el método
    metodo.addEventListener('change', () => {
        const grupoTarjeta = document.getElementById('grupo-tarjeta');

        if (metodo.value === 'efectivo') {
            grupoTarjeta.style.display = 'none';
            tarjeta.removeAttribute('required');
        } else {
            grupoTarjeta.style.display = 'flex';
            tarjeta.setAttribute('required', 'true');
        }
    });

    let valido = true;

    // Validación personalizada
    if (!/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]{3,}$/.test(nombre.value)) {
        nombre.setCustomValidity("Escribe un nombre válido (solo letras, al menos 3 caracteres).");
        valido = false;
    } else {
        nombre.setCustomValidity("");
    }

    if (!email.checkValidity()) {
        email.setCustomValidity("Ingresa un correo electrónico válido.");
        valido = false;
    } else {
        email.setCustomValidity("");
    }

    if (!metodo.value) {
        metodo.setCustomValidity("Selecciona un método de pago.");
        valido = false;
    } else {
        metodo.setCustomValidity("");
    }

    if (metodo.value !== "efectivo") {
        if (!/^\d{13,16}$/.test(tarjeta.value)) {
            tarjeta.setCustomValidity("Ingresa entre 13 y 16 dígitos numéricos.");
            valido = false;
        } else {
            tarjeta.setCustomValidity("");
        }
    } else {
        tarjeta.setCustomValidity(""); // No es requerido si es efectivo
    }

    if (!valido) {
        this.reportValidity(); // Muestra errores del navegador
        return;
    }

    // Generar resumen + PDF
    const resumen = document.getElementById('resumenCompraModal');
    if (!resumen) return;

    resumen.innerHTML = '<h4>Resumen del pedido:</h4>';

    let total = 0;
    carrito.forEach(item => {
        const subtotalItem = item.precio * item.cantidad;
        total += subtotalItem;

        resumen.innerHTML += `
            <div style="margin-bottom: 8px;">
                <strong>${item.nombre}</strong><br>
                ${Number(item.precio).toLocaleString('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0
                })} x ${item.cantidad}
            </div>
        `;
    });

    const iva = total * 0.19;
    const totalConIVA = total + iva;

    resumen.innerHTML += `
        <hr style="margin: 10px 0;">
        <p><strong>Subtotal:</strong> ${total.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}</p>
        <p><strong>IVA (19%):</strong> ${iva.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}</p>
        <p><strong>Total:</strong> ${totalConIVA.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}</p>
    `;

    setTimeout(() => {
        // Generar PDF
        const doc = new jsPDF();
        doc.setFontSize(14);
        doc.text('HotelixHub - Recibo de Compra', 20, 20);

        let y = 35;
        carrito.forEach((item, i) => {
            const texto = `${i + 1}. ${item.nombre} - ${item.cantidad} x ${Number(item.precio).toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            })}`;
            doc.text(texto, 20, y);
            y += 10;
        });

        y += 10;
        doc.text(`Subtotal: ${total.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}`, 20, y);
        y += 10;
        doc.text(`IVA (19%): ${iva.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}`, 20, y);
        y += 10;
        doc.text(`Total: ${totalConIVA.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })}`, 20, y);

        y += 20;
        doc.text(`Gracias por tu compra. ¡Esperamos verte pronto!`, 20, y);

        doc.save('recibo_compra.pdf');

        alert('¡Compra confirmada exitosamente!');
        document.getElementById('modalCompra').style.display = 'none';
        carrito.length = 0;
        renderizarCarrito();
        resumen.innerHTML = '';
    }, 500);
});

function cerrarModalCompra() {
    const modal = document.getElementById('modalCompra');
    if (modal) modal.style.display = 'none';
}

const inputTarjeta = document.querySelector('#formPago input[type="text"][maxlength="16"]');
inputTarjeta?.addEventListener("input", () => {
    inputTarjeta.value = inputTarjeta.value.replace(/\D/g, "");
});

document.querySelector('.btnCompra')?.addEventListener('click', () => {
    const modal = document.getElementById('modalCompra');
    if (modal) {
        modal.style.display = 'flex'; // Muestra el modal
    }
});

renderizarCarrito();