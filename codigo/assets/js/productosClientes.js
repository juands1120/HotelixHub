document.addEventListener('DOMContentLoaded', () => {
    const carrito = [];
    const listaCarrito = document.querySelector('.lista-carrito');
    const subtotalSpan = document.getElementById('subtotal');
    const ivaSpan = document.getElementById('iva');
    const totalSpan = document.getElementById('total');
    const modalCompra = document.getElementById('modalCompra');
    const resumenCompraModal = document.getElementById('resumenCompraModal');
    const formPago = document.getElementById('formPago');
    const metodoPago = document.getElementById('metodo');
    const campoTarjeta = document.getElementById('grupo-tarjeta');
    const contenedorProductos = document.querySelector('.contenedor-productos');
    const filtrosCategorias = document.querySelector('.categorias');

    let productos = [];
    let categorias = [];

    // ========================== AUTOCOMPLETAR FORMULARIO ==========================
    fetch('../services/sessionUsuario.php')
    .then(res => res.json())
    .then(user => {
        if (user) {
            document.getElementById('nombre').value = user.nombre;
            document.getElementById('email').value = user.email;
        }
    })
    .catch(() => console.warn('No se pudo cargar datos de sesión'));

    // ========================== OCULTAR TARJETA SEGÚN MÉTODO ==========================
    metodoPago.addEventListener('change', () => {
        campoTarjeta.style.display = (metodoPago.value === 'efectivo') ? 'none' : 'block';
    });

    // ========================== CARGAR CATEGORÍAS Y PRODUCTOS ==========================
    cargarCategorias();
    cargarProductos();

    function cargarCategorias() {
        fetch('../controller/categoriaController.php?accion=listar')
        .then(res => res.json())
        .then(data => {
            categorias = data;
            renderizarBotonesCategorias();
        })
        .catch(() => console.error("Error al cargar categorías"));
    }

    function renderizarBotonesCategorias() {
        filtrosCategorias.innerHTML = '';
        categorias.forEach(cat => {
            const btn = document.createElement('button');
            btn.className = 'btn-categoria';
            btn.dataset.categoria = cat.nombre_categoria.toLowerCase();
            btn.innerHTML = `<p>${cat.nombre_categoria}</p>`;
            btn.addEventListener('click', () => filtrarPorCategoria(cat.nombre_categoria.toLowerCase()));
            filtrosCategorias.appendChild(btn);
        });
    }

    function filtrarPorCategoria(nombreCat) {
        document.querySelectorAll('.tarjeta-producto').forEach(card => {
            card.style.display = 
                card.getAttribute('data-categoria').toLowerCase() === nombreCat ? 'block' : 'none';
        });
    }

    function cargarProductos() {
        fetch('../controller/productosClienteController.php')
        .then(res => res.json())
        .then(data => {
            productos = data;
            renderizarProductos(productos);
        })
        .catch((err) => {
            console.error("Error al cargar productos:", err);
            alert('Error al cargar productos desde la base de datos.');
        });
    }

    function renderizarProductos(lista) {
        contenedorProductos.innerHTML = '';
        lista.forEach(prod => {
            const productoDiv = document.createElement('div');
            productoDiv.classList.add('tarjeta-producto');
            productoDiv.setAttribute('data-categoria', prod.nombre_categoria.toLowerCase());

            productoDiv.innerHTML = `
                <div class="producto-info">
                    <img class="img-producto" src="/HotelixHub/codigo/${prod.imagen}" alt="${prod.nombre}">
                    <div class="contenido-producto">
                        <h3>${prod.nombre}</h3>
                        <p style="word-break: break-word;">${prod.descripcion}</p>
                        <span class="precio">${parseFloat(prod.precio).toLocaleString('es-CO', { style: 'currency', currency: 'COP' })}</span>
                    </div>
                </div>
                <div class="acciones-producto">
                    <button class="btn-cantidad">−</button>
                    <span class="cantidad">1</span>
                    <button class="btn-cantidad">+</button>
                    <button class="btn-agregar">Agregar al carrito</button>
                </div>
            `;
            contenedorProductos.appendChild(productoDiv);
        });
        inicializarBotones();
    }

// ========================== BOTONES CANTIDAD Y AGREGAR ==========================
function inicializarBotones() {
    document.querySelectorAll('.btn-cantidad').forEach(btn => {
        btn.addEventListener('click', () => {
            const cantidadSpan = btn.parentElement.querySelector('.cantidad');
            let cantidad = parseInt(cantidadSpan.textContent);
            cantidad += (btn.textContent === '+' ? 1 : -1);
            cantidadSpan.textContent = Math.max(cantidad, 1);
        });
    });

    document.querySelectorAll('.btn-agregar').forEach(btn => {
        btn.addEventListener('click', () => {
            const tarjeta = btn.closest('.tarjeta-producto');
            const nombre = tarjeta.querySelector('.contenido-producto h3').textContent;
            const producto = productos.find(p => p.nombre === nombre);
            if (!producto) return;
            
            const precio = parseFloat(producto.precio);
            const cantidad = parseInt(tarjeta.querySelector('.cantidad').textContent);
            const imgSrc = tarjeta.querySelector('.img-producto').getAttribute('src');

            const itemExistente = carrito.find(item => item.nombre === nombre);
            if (itemExistente) {
                itemExistente.cantidad += cantidad;
            } else {
                carrito.push({ nombre, precio, cantidad, imgSrc });
            }

            actualizarCarrito(); // <- ESTA LÍNEA NECESITA QUE actualizarCarrito ESTÉ EN EL SCOPE GLOBAL
        });
    });
}

// ========================== ACTUALIZAR CARRITO ==========================
function actualizarCarrito() {
    listaCarrito.innerHTML = '';
    let subtotal = 0;

    carrito.forEach(item => {
        subtotal += item.precio * item.cantidad;
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item-carrito');
        itemDiv.innerHTML = `
            <img src="${item.imgSrc}" alt="${item.nombre}">
            <p>${item.nombre}</p>
            <p>${(item.precio * item.cantidad).toLocaleString('es-CO',{style:'currency',currency:'COP'})} x ${item.cantidad}</p>
            <button class="btn-eliminar-item" data-nombre="${item.nombre}">Quitar</button>
        `;

        listaCarrito.appendChild(itemDiv);
    });

    document.querySelectorAll('.btn-eliminar-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const nombreProducto = btn.getAttribute('data-nombre');
            const index = carrito.findIndex(item => item.nombre === nombreProducto);
            if (index !== -1) {
                carrito.splice(index, 1);
                actualizarCarrito();
            }
        });
    });

    const iva = subtotal * 0.19;
    const total = subtotal + iva;

    subtotalSpan.textContent = subtotal.toLocaleString('es-CO',{style:'currency',currency:'COP'});
    ivaSpan.textContent = iva.toLocaleString('es-CO',{style:'currency',currency:'COP'});
    totalSpan.textContent = total.toLocaleString('es-CO',{style:'currency',currency:'COP'});
}


    // ========================== MODAL COMPRA ==========================
    document.querySelector('.btnCompra').addEventListener('click', () => {
        if (carrito.length === 0) {
            alert('El carrito está vacío');
            return;
        }
        resumenCompraModal.innerHTML = carrito.map(item => `
            <div>
                <p><strong>${item.nombre}</strong></p>
                <p>${(item.precio * item.cantidad).toLocaleString('es-CO',{style:'currency',currency:'COP'})} x ${item.cantidad}</p>
            </div>
        `).join('');
        modalCompra.style.display = 'flex';
    });

    formPago.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!formPago.checkValidity()) {
            alert("Por favor completa todos los campos correctamente.");
            return;
        }

        // Enviar a la BDD
        const data = {
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            metodo: metodoPago.value,
            tarjeta: document.getElementById('tarjeta').value,
            items: carrito
        };

        fetch('../controller/compraController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.mensaje.includes('éxito')) {
                alert(resp.mensaje);
                // Limpia
                formPago.reset();
                metodoPago.dispatchEvent(new Event('change'));
                carrito.length = 0;
                actualizarCarrito();
                modalCompra.style.display = 'none';
                // Abre el PDF del recibo
                window.open('../pdf/generarReciboCompra.php', '_blank');
            } else {
                alert("Error al registrar la compra");
            }
        })
        .catch(() => alert('Error al registrar la compra'));
    });

    window.cerrarModalCompra = function() {
        modalCompra.style.display = 'none';
    };

    // ========================== SESIÓN, BUSCAR Y SCROLL ==========================
    window.toggleSesionMenu = function() {
        document.getElementById("menuSesion").classList.toggle("show");
    };

    window.cerrarSesion = function() {
        if (confirm("¿Deseas cerrar sesión?")) {
            window.location.href = "../services/cerrarSesion.php";
        }
    };

    document.getElementById('btn-buscar').addEventListener('click', () => {
        const query = document.getElementById('input-busqueda').value.toLowerCase();
        document.querySelectorAll('.tarjeta-producto').forEach(card => {
            const titulo = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = titulo.includes(query) ? 'block' : 'none';
        });
    });

    document.getElementById('btn-izq').addEventListener('click', () => {
        contenedorProductos.scrollBy({ left: -300, behavior: 'smooth' });
    });
    document.getElementById('btn-der').addEventListener('click', () => {
        contenedorProductos.scrollBy({ left: 300, behavior: 'smooth' });
    });

    // ========================== CARRUSEL DINÁMICO ==========================
    const anuncios = [
        { titulo: "¡Promoción de Vino!", descripcion: "Lleva 2 y paga 1 solo hoy.", imagen: "../assets/img/imgProductosCliente/promocion.png" },
        { titulo: "Descuento en Sushi", descripcion: "Hasta 30% off en rolls seleccionados.", imagen: "../assets/img/imgProductosCliente/promocion2.png" },
        { titulo: "Día de Spa", descripcion: "Relájate con un 20% de descuento.", imagen: "../assets/img/imgProductosCliente/promocion3.png" }
    ];

    let index = 0;
    const carrusel = document.getElementById('carrusel');
    carrusel.innerHTML = `
        <div class="texto-anuncio">
            <h3 id="textoCarruselTitulo">${anuncios[0].titulo}</h3>
            <p id="textoCarruselDescripcion">${anuncios[0].descripcion}</p>
        </div>
        <img id="imgCarrusel" src="${anuncios[0].imagen}" alt="Anuncio">
    `;

    const textoTitulo = document.getElementById('textoCarruselTitulo');
    const textoDescripcion = document.getElementById('textoCarruselDescripcion');
    const imgCarrusel = document.getElementById('imgCarrusel');

    setInterval(() => {
        index = (index + 1) % anuncios.length;
        textoTitulo.textContent = anuncios[index].titulo;
        textoDescripcion.textContent = anuncios[index].descripcion;
        imgCarrusel.src = anuncios[index].imagen;
    }, 4000);
});
