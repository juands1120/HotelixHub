document.addEventListener('DOMContentLoaded', () => {
    const tablaBody = document.querySelector('#tablaProductos');
    const modal = document.getElementById('modalProducto');
    const modalCategoria = document.getElementById('modalCategoria');
    const btnAgregar = document.getElementById('btnAgregar');
    const btnAgregarCategoria = document.getElementById('btnAgregarCategoria');
    const cerrarModal = document.getElementById('cerrarModal');
    const cerrarModalCategoria = document.getElementById('cerrarModalCategoria');
    const formProducto = document.getElementById('formProducto');
    const formCategoria = document.getElementById('formCategoria');
    const selectCategoria = document.getElementById('categoria');
    const listaCategorias = document.getElementById('listaCategorias');
    const filtrosCategorias = document.getElementById('filtrosCategorias');

    let productos = [];
    let categorias = [];
    let categoriaActual = null;

    cargarProductos();
    cargarCategorias();

    // =========================
    // PRODUCTOS
    // =========================
    btnAgregar.addEventListener('click', () => {
        formProducto.reset();
        document.getElementById('productoId').value = '';
        document.getElementById('imagenActual').value = '';
        document.getElementById('tituloModal').textContent = 'Nuevo Producto';
        cargarCategoriasSelect();
        modal.classList.add('active');
    });

    cerrarModal.addEventListener('click', () => modal.classList.remove('active'));

    formProducto.addEventListener('submit', (e) => {
        e.preventDefault();

        const imagenInput = document.getElementById('imagen');
        const file = imagenInput?.files[0];

        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png'];
            const allowedExt = ['jpg', 'jpeg', 'png'];
            const ext = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) || !allowedExt.includes(ext)) {
                mostrarMensaje("Solo se permiten imágenes JPG o PNG", "error");
                return;
            }
        }

        if (!validarFormularioProducto()) return;

        const id = document.getElementById('productoId').value;
        const accion = id ? 'editar' : 'guardar';
        const formData = new FormData(formProducto);
        if(id) formData.append('id', id);
        formData.append('imagen_actual', document.getElementById('imagenActual').value);

        fetch(`../controller/productoController.php?accion=${accion}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            mostrarMensaje(resp.mensaje, "success");
            modal.classList.remove('active');
            cargarProductos();
        })
        .catch(() => mostrarMensaje("Error al guardar el producto", "error"));
    });

    // =========================
    // CATEGORÍAS
    // =========================
    btnAgregarCategoria.addEventListener('click', () => {
        formCategoria.reset();
        document.getElementById('categoriaId').value = '';
        modalCategoria.classList.add('active');
        cargarCategoriasLista();
    });

    cerrarModalCategoria.addEventListener('click', () => modalCategoria.classList.remove('active'));

    formCategoria.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = document.getElementById('categoriaId').value;
        const nombre = document.getElementById('nombreCategoria').value.trim();
        if (!nombre) {
            mostrarMensaje("Por favor ingresa un nombre para la categoría", "error");
            return;
        }
        const data = { id, nombre };

        fetch(`../controller/categoriaController.php?accion=guardar`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            mostrarMensaje(resp.mensaje, "success");
            formCategoria.reset();
            cargarCategorias();
            cargarCategoriasLista();
        })
        .catch(() => mostrarMensaje("Error al guardar la categoría", "error"));
    });

    // =========================
    // VALIDACIONES CAMPOS NUMÉRICOS
    // =========================
    document.getElementById('precio').addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    document.getElementById('stock').addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // =========================
    // FUNCIONES DE CARGA
    // =========================
    function cargarProductos() {
        fetch('../controller/productoController.php?accion=listar')
            .then(res => res.json())
            .then(data => {
                productos = data;
                renderizarProductos();
            })
            .catch(() => mostrarMensaje("Error al cargar productos", "error"));
    }

    function cargarCategorias() {
        fetch('../controller/categoriaController.php?accion=listar')
            .then(res => res.json())
            .then(data => {
                categorias = data;
                renderizarBotonesCategorias();
                cargarCategoriasSelect();
            })
            .catch(() => mostrarMensaje("Error al cargar categorías", "error"));
    }

    function cargarCategoriasSelect() {
        selectCategoria.innerHTML = categorias.map(c =>
            `<option value="${c.id_categoria}">${c.nombre_categoria}</option>`
        ).join('');
    }

    function cargarCategoriasLista() {
        listaCategorias.innerHTML = categorias.map(c =>
            `<div style="margin:5px 0;">
                <input type="text" value="${c.nombre_categoria}" id="catInput-${c.id_categoria}" style="width:200px;" />
                <button class="agregar-btn" onclick="guardarCategoria(${c.id_categoria})">Guardar</button>
                <button class="remove-btn" onclick="eliminarCategoria(${c.id_categoria})">Eliminar</button>
            </div>`
        ).join('');
    }

    // =========================
    // FILTRADO
    // =========================
    function renderizarBotonesCategorias() {
        filtrosCategorias.innerHTML = `<button class="agregar-btn" onclick="filtrarPorCategoria(null)">Todos</button>`;
        categorias.forEach(cat => {
            filtrosCategorias.innerHTML += `<button class="agregar-btn" onclick="filtrarPorCategoria(${cat.id_categoria})">${cat.nombre_categoria}</button>`;
        });
    }

    window.filtrarPorCategoria = function(idCat) {
        categoriaActual = idCat;
        renderizarProductos();
    };

    function renderizarProductos() {
        tablaBody.innerHTML = '';
        productos
            .filter(p => categoriaActual == null || p.id_categoria == categoriaActual)
            .forEach(prod => {
                const precioFormateado = `$${parseInt(prod.precio).toLocaleString('es-CO')} COP`;
                const tr = document.createElement('div');
                tr.classList.add('producto-card');
                tr.innerHTML = `
                    <div class="producto-info">
                        <div class="colum">
                            <img src="/HotelixHub/codigo/${prod.imagen}" alt="img">
                        </div>
                        <div class="colum">
                            <div>${prod.nombre}</div>
                            <div>${prod.descripcion}</div>
                            <div>${precioFormateado}</div>
                        </div>
                    </div>
                    <div class="acciones">
                        <div>Stock: ${prod.stock}</div>
                        <button class="editarbtn" onclick='editarProducto(${JSON.stringify(prod)})'>Editar</button>
                        <button class="removebtn" onclick='eliminarProducto(${prod.id})'>Eliminar</button>
                    </div>
                `;
                tablaBody.appendChild(tr);
            });
    }

    // =========================
    // FUNCIONES GLOBALES
    // =========================
    window.editarProducto = function(prod) {
        document.getElementById('productoId').value = prod.id;
        document.getElementById('nombre').value = prod.nombre;
        document.getElementById('precio').value = prod.precio;
        document.getElementById('descripcion').value = prod.descripcion;
        document.getElementById('stock').value = prod.stock;
        document.getElementById('imagenActual').value = prod.imagen;
        cargarCategoriasSelect();
        setTimeout(() => {
            selectCategoria.value = prod.id_categoria;
        }, 200);
        document.getElementById('tituloModal').textContent = 'Editar Producto';
        modal.classList.add('active');
    }

    window.eliminarProducto = function(id) {
        confirmarAccion("¿Estás seguro de eliminar este producto?", () => {
            fetch('../controller/productoController.php?accion=eliminar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(resp => {
                mostrarMensaje(resp.mensaje, "success");
                cargarProductos();
            })
            .catch(() => mostrarMensaje("Error al eliminar el producto", "error"));
        });
    }

    window.guardarCategoria = function(id) {
        const nombre = document.getElementById(`catInput-${id}`).value.trim();
        if (!nombre) {
            mostrarMensaje("Nombre de categoría no puede estar vacío", "error");
            return;
        }
        const data = { id, nombre };
        fetch('../controller/categoriaController.php?accion=guardar', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            mostrarMensaje(resp.mensaje, "success");
            cargarCategorias();
            cargarCategoriasLista();
        })
        .catch(() => mostrarMensaje("Error al guardar categoría", "error"));
    }

    window.eliminarCategoria = function(id) {
        confirmarAccion("¿Estás seguro de eliminar esta categoría?", () => {
            fetch('../controller/categoriaController.php?accion=eliminar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(resp => {
                mostrarMensaje(resp.mensaje, "success");
                cargarCategorias();
                cargarCategoriasLista();
            })
            .catch(() => mostrarMensaje("Error al eliminar la categoría", "error"));
        });
    }

    function validarFormularioProducto() {
        const nombre = document.getElementById('nombre').value.trim();
        const precio = document.getElementById('precio').value.trim();
        const stock  = document.getElementById('stock').value.trim();
        const desc   = document.getElementById('descripcion').value.trim();
        if (!nombre || !precio || !stock || !desc) {
            mostrarMensaje("Completa todos los campos correctamente", "error");
            return false;
        }
        return true;
    }
});

document.getElementById('exportPDF').addEventListener('click', () => {
    window.open('../pdf/generarReporteProductos.php', '_blank');
});

// =========================
// TOAST Y CONFIRM
// =========================
function mostrarMensaje(mensaje, tipo = 'success') {
    const contenedor = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.textContent = mensaje;
    contenedor.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function confirmarAccion(mensaje, callback) {
    const modal = document.getElementById('confirm-modal');
    const texto = document.getElementById('confirm-text');
    const btnSi = document.getElementById('confirm-yes');
    const btnNo = document.getElementById('confirm-no');

    texto.textContent = mensaje;
    modal.classList.add('active');

    const cerrar = () => {
        modal.classList.remove('active');
        btnSi.removeEventListener('click', confirmar);
        btnNo.removeEventListener('click', cancelar);
    };

    const confirmar = () => { callback(); cerrar(); };
    const cancelar = () => cerrar();

    btnSi.addEventListener('click', confirmar);
    btnNo.addEventListener('click', cancelar);
}
