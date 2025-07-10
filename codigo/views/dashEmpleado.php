<?php
/**
 * Dashboard de Empleado - HotelixHub
 * 
 * Este archivo muestra el panel de control para empleados con:
 * - Estadísticas de habitaciones por piso
 * - Notificaciones de compras de clientes
 */

// 1. INCLUSIÓN DE ARCHIVOS NECESARIOS
require_once __DIR__ . '/../services/sessionManager.php';
require_once __DIR__ . '/../config/conexionbd.php';
require_once __DIR__ . '/../models/habitacionesdash.php';

// 2. VERIFICACIÓN DE SESIÓN Y ROLES
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Solo permitir acceso a empleados (roles 3, 4 o 5)
if (!in_array($_SESSION['usuario']['usu_idrol'], [3, 4, 5])) {
    header('Location: ../views/login.php');
    exit();
}

// 3. OBTENER ESTADÍSTICAS DE HABITACIONES
$piso2 = obtenerEstadisticasPiso(2, $pdo);
$piso3 = obtenerEstadisticasPiso(3, $pdo);
$piso4 = obtenerEstadisticasPiso(4, $pdo);
$piso5 = obtenerEstadisticasPiso(5, $pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HotelixHub - Dashboard Empleado</title>
    <link rel="stylesheet" href="../assets/css/dashEmpleado.css">
</head>
<body>
    <!-- ==================== BARRA LATERAL ==================== -->
    <div class="barra-lateral" id="sidebar">
        <div class="logo">
            <a href="Home.php">
                <img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo">
            </a>
        </div>
        
        <br><br>
        
        <!-- Menú de navegación -->
        <a href="dashEmpleado.php" class="menu-item">Inicio</a>
        <a href="formClientes.php" class="menu-item">Clientes</a>
        <a href="perfilEmpleado.php" class="menu-item">Perfil</a>
        <a href="../controller/logout.php" class="logout">Cerrar Sesión</a>
    </div>    

    <!-- ==================== CONTENIDO PRINCIPAL ==================== -->
    <div class="main-content">
        <!-- Perfil del usuario -->
        <div class="profile">
            <span class="profile-name">
                <?php echo htmlspecialchars($_SESSION['usuario']['nombre']. ' ' . $_SESSION['usuario']['apellido']); ?>
            </span>
            <div class="profile-img">👤</div>
        </div>

        <!-- Mensaje de bienvenida -->
        <div class="welcome">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']. ' ' . $_SESSION['usuario']['apellido']); ?></h2>
        </div>

        <!-- Contenido del dashboard -->
        <div class="dashboard-content">
            <!-- ==================== ESTADÍSTICAS DE HABITACIONES ==================== -->
            <div class="room-stats">
                <!-- Piso 2 -->
                <div class="room-category">
                    <h3>Habitaciones Piso 2</h3>
                    <div class="stats-grid">
                        <div class="stat-box"><div class="stat-number"><?= $piso2['total'] ?></div><div class="stat-label">Total</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso2['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso2['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso2['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                    </div>
                </div>

                <!-- Piso 3 -->
                <div class="room-category">
                    <h3>Habitaciones Piso 3</h3>
                    <div class="stats-grid">
                        <div class="stat-box"><div class="stat-number"><?= $piso3['total'] ?></div><div class="stat-label">Total</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso3['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso3['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso3['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                    </div>
                </div>

                <!-- Piso 4 -->
                <div class="room-category">
                    <h3>Habitaciones Piso 4</h3>
                    <div class="stats-grid">
                        <div class="stat-box"><div class="stat-number"><?= $piso4['total'] ?></div><div class="stat-label">Total</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso4['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso4['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso4['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                    </div>
                </div>

                <!-- Piso 5 -->
                <div class="room-category">
                    <h3>Habitaciones Piso 5</h3>
                    <div class="stats-grid">
                        <div class="stat-box"><div class="stat-number"><?= $piso5['total'] ?></div><div class="stat-label">Total</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso5['disponibles'] ?></div><div class="stat-label">Disponibles</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso5['ocupadas'] ?></div><div class="stat-label">Ocupadas</div></div>
                        <div class="stat-box"><div class="stat-number"><?= $piso5['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
                    </div>
                </div>
            </div>

            <!-- ==================== NOTIFICACIONES ==================== -->
            <div class="notificaciones-container">
                <h3 class="notifications-title">Notificaciones Recientes</h3>
                <div id="contenedor-notificaciones"></div>
            </div>
        </div>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
    <script>
    /**
     * Script para manejar las notificaciones del empleado
     */
    document.addEventListener('DOMContentLoaded', () => {
        // 1. OBTENER NOTIFICACIONES DEL SERVIDOR
        function obtenerNotificaciones() {
            fetch('../controller/compraController.php?accion=notificaciones')
                .then(res => {
                    if (!res.ok) throw new Error('Error en la respuesta del servidor');
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error del servidor:', data.error);
                        mostrarMensajeError();
                    } else {
                        mostrarNotificacionesAgrupadas(data);
                    }
                })
                .catch(error => {
                    console.error("Error al cargar notificaciones:", error);
                    mostrarMensajeError();
                });
        }

        // 2. MOSTRAR MENSAJE DE ERROR
        function mostrarMensajeError() {
            const contenedor = document.getElementById('contenedor-notificaciones');
            contenedor.innerHTML = '<div class="no-notifications">Error al cargar notificaciones</div>';
        }

        // 3. AGRUPAR Y MOSTRAR NOTIFICACIONES
        function mostrarNotificacionesAgrupadas(notificaciones) {
            const contenedor = document.getElementById('contenedor-notificaciones');
            contenedor.innerHTML = '';

            if (!Array.isArray(notificaciones)) {
                console.error('Las notificaciones no son un array:', notificaciones);
                contenedor.innerHTML = '<div class="no-notifications">Formato de datos incorrecto</div>';
                return;
            }

            if (notificaciones.length === 0) {
                contenedor.innerHTML = '<div class="no-notifications">No hay notificaciones nuevas</div>';
                return;
            }

            // Agrupar notificaciones por compra y habitación
            const agrupadas = {};
            notificaciones.forEach(n => {
                const clave = `${n.id_compra}_${n.id_habitacion || '0'}`;
                if (!agrupadas[clave]) {
                    agrupadas[clave] = {
                        cliente: n.nombre_cliente || 'Cliente desconocido',
                        habitacion: n.nombre_habitacion || 'No asignada',
                        productos: []
                    };
                }
                agrupadas[clave].productos.push({
                    nombre: n.nombre_producto || 'Producto sin nombre',
                    cantidad: n.cantidad || 0,
                    precio: n.precio || 0,
                    categoria: n.nombre_categoria || 'Sin categoría'
                });
            });

            // Crear tarjetas de notificación
            Object.entries(agrupadas).forEach(([clave, datos]) => {
                const idCompra = clave.split('_')[0];
                const totalCompra = datos.productos.reduce((sum, p) => sum + (p.precio * p.cantidad), 0);

                const tarjeta = document.createElement('div');
                tarjeta.className = 'notificacion-card';
                tarjeta.innerHTML = `
                    <div class="notificacion-header" onclick="toggleDetalle(this)">
                        <div class="avatar-circle">${datos.cliente.charAt(0)}</div>
                        <div>
                            <div class="nombre-cliente">${datos.cliente}</div>
                            <div class="mensaje">Habitación: ${datos.habitacion}</div>
                        </div>
                    </div>
                    <div class="detalle-notificacion">
                        <div class="detalle-info">
                            ${datos.productos.map(p => `
                                <div class="producto-item">
                                    <strong>${p.nombre}</strong> (${p.categoria})<br>
                                    Cantidad: ${p.cantidad}<br>
                                    Precio: ${parseFloat(p.precio).toLocaleString('es-CO', { style: 'currency', currency: 'COP' })}
                                </div>
                            `).join('')}
                            <div class="total-compra">
                                <strong>Total:</strong> ${parseFloat(totalCompra).toLocaleString('es-CO', { style: 'currency', currency: 'COP' })}
                            </div>
                        </div>
                        <button class="btn-leido" data-id="${idCompra}">Marcar como leída</button>
                    </div>
                `;

                // Evento para el botón "Marcar como leída"
                tarjeta.querySelector('.btn-leido').addEventListener('click', (e) => {
                    e.stopPropagation();
                    marcarComoLeida(idCompra, tarjeta);
                });

                contenedor.appendChild(tarjeta);
            });
        }

        // 4. FUNCIÓN PARA MARCAR NOTIFICACIÓN COMO LEÍDA
        function marcarComoLeida(idCompra, tarjetaElemento) {
            fetch('../controller/compraController.php?accion=marcarLeida', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idCompra })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.mensaje) {
                    tarjetaElemento.style.opacity = '0.5';
                    setTimeout(() => tarjetaElemento.remove(), 300);
                } else {
                    alert("Error al marcar como leída: " + (resp.error || ''));
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error al marcar como leída");
            });
        }

        // 5. FUNCIÓN PARA MOSTRAR/OCULTAR DETALLES
        function toggleDetalle(elemento) {
            const detalle = elemento.nextElementSibling;
            detalle.style.display = detalle.style.display === 'none' ? 'block' : 'none';
        }

        // Inicializar
        obtenerNotificaciones();
    });
    </script>
</body>
</html>