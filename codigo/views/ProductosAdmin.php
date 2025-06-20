<?php
require_once __DIR__ . '/../../controlador/sessionManager.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Producto</title>
    <link rel="stylesheet" href="../css/productosAdmin.css">
</head>
<body>
            <div class="barra-lateral">

            <div class="logo">
                <a href="Home.php"><img src="../assets/img/imgHome/Logo principal.png" alt="HotelixHub"  class="logo">
            </div>
            <br><br>
            
            <a href="dashAdmin.php"><div class="menu-item">Inicio</div></a>
            <a href="Habitacion.php"><div class="menu-item">Habitaciones</div></a>

            <div class="usu">
                <button id="usuario">Usuarios</button>
                <div class="usu-contenido">
                    <a href="formEmpleados.php">Empleados   </a>
                    <a href="formClientes.php">Clientes</a>
                </div>
            </div>
            <a href="ProductosAdmin.php"><div class="menu-item">Productos</div></a>
        </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Crear Nuevo Producto</h1>
            <button class="agregar-btn" onclick="enviarFormularios()">Guardar Producto</button>
        </div>
        
        <div class="content">
            <div class="section image-section">
                <div class="section-title">Productos</div>
                <div class="grid">
                    <div class="box" onclick="agregarFormulario()">
                        <div class="box-icon">➕</div>
                        <div>Agregar Producto</div>
                    </div>
                    <div class="box">
                        <img src="/api/placeholder/40/60" alt="Botella naranja" class="product-bottle" style="background-color: orange; border-radius: 8px;">
                    </div>
                    <div class="box">
                        <img src="/api/placeholder/40/60" alt="Botella azul" class="product-bottle" style="background-color: cyan; border-radius: 8px;">
                    </div>
                </div>
            </div>
            
            <div class="section category-section">
                <div class="section-title">Categoría</div>
                <div class="grid">
                    <div class="category-box">
                        <div class="category-icon">📦</div>
                        <div>Aseo</div>
                        <div class="item-count">100 items</div>
                    </div>
                    <div class="category-box">
                        <div class="category-icon">🍾</div>
                        <div>Bebidas</div>
                        <div class="item-count">100 items</div>
                    </div>
                    <div class="category-box">
                        <div class="category-icon">🍽</div>
                        <div>Comida</div>
                        <div class="item-count">100 items</div>
                    </div>
                    <div class="category-box" onclick="abrirModalCategoria()">
                        <div class="box-icon">➕</div>
                        <div>Agregar</div>
                        <div>categoría</div>
                    </div>
                    
                </div>
            </div>
            
            <div class="section details-section">
                <div class="section-title">Detalles del Producto</div>
                <div id="formularios"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <!-- Modal para Agregar Categoría -->
    <div id="modalCategoria" class="modal-categoria">
        <div class="modal-categoria-content">
          <span class="close-categoria" onclick="cerrarModalCategoria()">&times;</span>
          <h2>Agregar Categoría</h2>
          <label for="nombreCategoria">Nombre de la categoría</label>
          <input type="text" id="nombreCategoria" class="input-categoria" placeholder="Ingrese el nombre de la categoría">
          
          <div class="modal-categoria-botones">
            <button class="btn-guardar" onclick="guardarCategoria()">Guardar</button>
            <button class="btn-cancelar" onclick="cerrarModalCategoria()">Cancelar</button>
          </div>
        </div>
      </div>
      


    <script>

        function abrirModalCategoria() {
            document.getElementById('modalCategoria').style.display = 'block';
        }

        function cerrarModalCategoria() {
            document.getElementById('modalCategoria').style.display = 'none';
        }

        function agregarCategoria() {
            const nombre = document.getElementById('nombreCategoria').value.trim();
            const icono = document.getElementById('iconoCategoria').value.trim();

            if (!nombre || !icono) {
                showModal("Por favor, completa todos los campos de categoría.");
                return;
            }

            // Crear la nueva categoría visualmente
            const nuevaCategoria = document.createElement('div');
            nuevaCategoria.classList.add('category-box');
            nuevaCategoria.innerHTML = `
                <div class="category-icon">${icono}</div>
                <div>${nombre}</div>
                <div class="item-count">0 items</div>
            `;

            // Insertar antes del último (el botón de agregar)
            const grid = document.querySelector('.category-section .grid');
            grid.insertBefore(nuevaCategoria, grid.lastElementChild);

            // Limpiar y cerrar modal
            document.getElementById('nombreCategoria').value = '';
            document.getElementById('iconoCategoria').value = '';
            cerrarModalCategoria();
        }

        function agregarFormulario() {
            const container = document.getElementById('formularios');
    
            const div = document.createElement('div');
            div.classList.add('product-form');
    
            div.innerHTML = ` 
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-input" placeholder="Ingrese el Nombre del Producto">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Marca</label>
                        <input type="text" class="form-input" placeholder="Ingrese la Marca del Producto">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Precio</label>
                        <input type="text" class="form-input" placeholder="Ingrese el Valor del Producto">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad</label>
                        <input type="text" class="form-input" placeholder="Ingrese la Cantidad del Producto">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-textarea" placeholder="Ingrese una Breve Descripción del Producto"></textarea>
                </div>
                <button class="remove-btn" onclick="this.parentNode.remove()">Eliminar</button>
            `;
    
            container.appendChild(div);
        }
    
        function enviarFormularios() {
            const formularios = document.querySelectorAll('.product-form');
            const productos = [];
    
            formularios.forEach(formulario => {
                const nombre = formulario.querySelector('input[placeholder="Ingrese el Nombre del Producto"]').value.trim();
                const marca = formulario.querySelector('input[placeholder="Ingrese la Marca del Producto"]').value.trim();
                const precio = formulario.querySelector('input[placeholder="Ingrese el Valor del Producto"]').value.trim();
                const cantidad = formulario.querySelector('input[placeholder="Ingrese la Cantidad del Producto"]').value.trim();
                const descripcion = formulario.querySelector('textarea[placeholder="Ingrese una Breve Descripción del Producto"]').value.trim();
    
                if (nombre && marca && precio && cantidad && descripcion) {
                    productos.push({ nombre, marca, precio, cantidad, descripcion });
                } else {
                    showModal("Por favor, completa todos los campos antes de enviar.");
                    return;
                }
            });
    
            if (productos.length === 0) {
                showModal("No hay productos para enviar.");
                return;
            }
    
            // Simulación de envío a la consola (puedes reemplazar esto con fetch())
            console.log("Productos a enviar:", productos);

            showModal("¡Producto(s) guardado(s) correctamente!");

    
            // Aquí podrías hacer un POST a una API real...
        }

        function showModal(message) {
            const modal = document.getElementById("myModal");
            const modalMessage = document.getElementById("modalMessage");
            modalMessage.innerText = message;
            modal.style.display = "block";

            setTimeout(() => {
            modal.style.display = "none";
        }, 2000); // Se cierra en 2 segundos
        }

        function closeModal() {
            const modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>
