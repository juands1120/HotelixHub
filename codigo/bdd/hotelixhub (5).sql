-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 10-07-2025 a las 16:45:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hotelixhub`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_empleado` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(100), IN `p_apellido` VARCHAR(100), IN `p_tipoDocumento` ENUM('CC','PA','TI','CE'), IN `p_numeroDocumento` VARCHAR(100), IN `p_numeroTelefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_direccion` VARCHAR(100), IN `p_usu_idrol` INT, IN `p_estado` ENUM('en turno','fuera de turno','vacaciones'))   BEGIN
    DECLARE document_exists INT;
    DECLARE phone_exists INT;
    DECLARE email_exists INT;
    DECLARE error_msg VARCHAR(255);
    
    -- Verificar si el documento ya existe en otro usuario
    SELECT COUNT(*) INTO document_exists 
    FROM usuarios 
    WHERE numeroDocumento = p_numeroDocumento AND id_usuario != p_id_usuario;
    
    -- Verificar si el teléfono ya existe en otro usuario
    SELECT COUNT(*) INTO phone_exists 
    FROM usuarios 
    WHERE numeroTelefono = p_numeroTelefono AND id_usuario != p_id_usuario;
    
    -- Verificar si el email ya existe en otro usuario
    SELECT COUNT(*) INTO email_exists 
    FROM usuarios 
    WHERE email = p_email AND id_usuario != p_id_usuario;
    
    -- Validaciones
    IF document_exists > 0 THEN
        SET error_msg = 'El número de documento ya está registrado por otro usuario';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = error_msg;
    ELSEIF phone_exists > 0 THEN
        SET error_msg = 'El número de teléfono ya está registrado por otro usuario';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = error_msg;
    ELSEIF email_exists > 0 THEN
        SET error_msg = 'El correo electrónico ya está registrado por otro usuario';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = error_msg;
    ELSE
        -- Actualizar el usuario
        UPDATE usuarios SET
            nombre = p_nombre,
            apellido = p_apellido,
            tipoDocumento = p_tipoDocumento,
            numeroDocumento = p_numeroDocumento,
            numeroTelefono = p_numeroTelefono,
            email = p_email,
            direccion = p_direccion,
            usu_idrol = p_usu_idrol,
            estado = p_estado
        WHERE id_usuario = p_id_usuario;
        
        -- Verificar si se actualizó correctamente
        IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'No se realizaron cambios en el empleado';
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estado_reserva` (IN `p_id_reserva` INT, IN `p_estado` ENUM('Pendiente','Confirmada','Cancelada','Sin reserva'))   BEGIN
  UPDATE reserva
  SET estado = p_estado
  WHERE id_reserva = p_id_reserva;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CancelarReserva` (IN `p_id_reserva` INT)   BEGIN
    UPDATE reserva SET estado = 'Cancelada' WHERE id_reserva = p_id_reserva;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CrearHabitacion` (IN `p_nombre` VARCHAR(50), IN `p_tipo` VARCHAR(50), IN `p_piso` INT, IN `p_precio` DECIMAL(10,2), IN `p_servicios` VARCHAR(255), IN `p_estado` VARCHAR(20), IN `p_imagen` VARCHAR(255))   BEGIN
    INSERT INTO habitacion 
    (nombre, tipoHabitacion, piso, precio, serviciosIncluidos, estado, imagen)
    VALUES (p_nombre, p_tipo, p_piso, p_precio, p_servicios, p_estado, p_imagen);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CrearReserva` (IN `p_id_usuario` INT, IN `p_id_habitacion` INT, IN `p_fecha_entrada` DATE, IN `p_fecha_salida` DATE, IN `p_num_huespedes` INT, IN `p_servicios_adicionales` TEXT, IN `p_precio_total` DECIMAL(10,2))   BEGIN
    INSERT INTO reserva (
        id_usuario, id_habitacion, fecha_entrada, fecha_salida, num_huespedes,
        servicios_adicionales, precio_total, estado, fecha_reserva
    )
    VALUES (
        p_id_usuario, p_id_habitacion, p_fecha_entrada, p_fecha_salida, p_num_huespedes,
        p_servicios_adicionales, p_precio_total, 'Pendiente', NOW()
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EditarHabitacion` (IN `p_numero` VARCHAR(50), IN `p_tipo` VARCHAR(50), IN `p_piso` INT, IN `p_precio` DECIMAL(10,2), IN `p_servicios` TEXT, IN `p_estado` VARCHAR(20), IN `p_imagen` VARCHAR(255))   BEGIN
    UPDATE habitacion 
    SET tipoHabitacion = p_tipo, piso = p_piso, precio = p_precio,
        serviciosIncluidos = p_servicios, estado = p_estado, imagen = p_imagen
    WHERE nombre = p_numero;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_editar_categoria` (IN `p_id_categoria` INT, IN `p_nombre_categoria` VARCHAR(100))   BEGIN
    UPDATE categorias
    SET nombre_categoria = p_nombre_categoria
    WHERE id_categoria = p_id_categoria;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_editar_producto` (IN `p_id` INT, IN `p_nombre` VARCHAR(100), IN `p_precio` DECIMAL(10,2), IN `p_descripcion` TEXT, IN `p_imagen` VARCHAR(255), IN `p_stock` INT, IN `p_id_categoria` INT)   BEGIN
    UPDATE productos 
    SET nombre = p_nombre, precio = p_precio, descripcion = p_descripcion, 
        imagen = p_imagen, stock = p_stock, id_categoria = p_id_categoria
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EliminarHabitacion` (IN `p_numero` VARCHAR(50))   BEGIN
    DELETE FROM habitacion WHERE nombre = p_numero;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_categoria` (IN `p_id_categoria` INT)   BEGIN
    DELETE FROM categorias WHERE id_categoria = p_id_categoria;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_empleado` (IN `p_id_usuario` INT, IN `p_id_usuario_eliminador` INT)   BEGIN
    DECLARE es_administrador INT;
    DECLARE tiene_reservas INT;
    
    -- Verificar si el usuario que elimina es administrador
    SELECT COUNT(*) INTO es_administrador 
    FROM usuarios 
    WHERE id_usuario = p_id_usuario_eliminador AND usu_idrol = 1;
    
    -- Verificar si el empleado tiene reservas asociadas
    SELECT COUNT(*) INTO tiene_reservas 
    FROM reserva 
    WHERE id_usuario = p_id_usuario;
    
    IF es_administrador = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo los administradores pueden eliminar empleados';
    ELSEIF tiene_reservas > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar el empleado porque tiene reservas asociadas';
    ELSE
        -- Eliminar el usuario
        DELETE FROM usuarios WHERE id_usuario = p_id_usuario;
        
        -- Verificar si se eliminó correctamente
        IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'No se encontró el empleado para eliminar';
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_producto` (IN `p_id` INT)   BEGIN
    DELETE FROM productos WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_estadisticas_habitaciones` (IN `piso_param` INT)   BEGIN
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) AS disponibles,
        SUM(CASE WHEN estado = 'ocupada' THEN 1 ELSE 0 END) AS ocupadas,
        SUM(CASE WHEN estado = 'mantenimiento' THEN 1 ELSE 0 END) AS mantenimiento
    FROM habitacion
    WHERE piso = piso_param;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_mensaje_contacto` (IN `p_id_usuario` INT, IN `p_nombre` VARCHAR(100), IN `p_telefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_ciudad` VARCHAR(50), IN `p_motivo` VARCHAR(50), IN `p_mensaje` TEXT)   BEGIN
    INSERT INTO contacto 
        (id_usuario, nombre, telefono, email, ciudad, motivo, mensaje)
    VALUES 
        (p_id_usuario, p_nombre, p_telefono, p_email, p_ciudad, p_motivo, p_mensaje);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_categoria` (IN `p_nombre_categoria` VARCHAR(100))   BEGIN
    INSERT INTO categorias (nombre_categoria) VALUES (p_nombre_categoria);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_producto` (IN `p_nombre` VARCHAR(100), IN `p_precio` DECIMAL(10,2), IN `p_descripcion` TEXT, IN `p_imagen` VARCHAR(255), IN `p_stock` INT, IN `p_id_categoria` INT)   BEGIN
    INSERT INTO productos (nombre, precio, descripcion, imagen, stock, id_categoria)
    VALUES (p_nombre, p_precio, p_descripcion, p_imagen, p_stock, p_id_categoria);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ListarHabitacionesDisponibles` ()   BEGIN
    SELECT * FROM habitacion WHERE estado != 'Mantenimiento';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ListarReservasPorEmail` (IN `p_email` VARCHAR(100))   BEGIN
    SELECT 
        r.id_reserva,
        r.fecha_reserva,
        r.fecha_entrada,
        r.fecha_salida,
        r.num_huespedes,
        r.precio_total,
        r.estado,
        r.servicios_adicionales,
        h.nombre AS nombre_habitacion,
        h.tipoHabitacion
    FROM reserva r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    JOIN habitacion h ON r.id_habitacion = h.id_habitacion
    WHERE u.email = p_email
    ORDER BY r.fecha_reserva DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_categorias` ()   BEGIN
    SELECT * FROM categorias;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_productos` ()   BEGIN
    SELECT p.*, c.nombre_categoria 
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.stock > 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_roles_empleados` ()   BEGIN
    SELECT id_rol, rol_nombre 
    FROM rol 
    WHERE id_rol IN (3, 4, 5); -- Solo roles de empleados
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_usuario` (IN `p_email` VARCHAR(100))   BEGIN
    SELECT * FROM usuarios WHERE email = p_email;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marcar_compra_leida` (IN `p_id` INT)   BEGIN
    UPDATE compras SET leida = 1 WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ObtenerHabitaciones` ()   BEGIN
    SELECT * FROM habitacion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ObtenerHabitacionPorNumero` (IN `p_numero` VARCHAR(50))   BEGIN
    SELECT * FROM habitacion WHERE nombre = p_numero;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_clientes` ()   BEGIN
    SELECT 
        u.id_usuario,
        u.nombre,
        u.apellido,
        u.tipoDocumento,
        u.numeroDocumento,
        u.numeroTelefono,
        u.paisProcedencia,
        u.email,

        r.id_habitacion,
        r.fecha_entrada,
        r.fecha_salida,
        r.estado,
        r.fecha_reserva,
        r.id_reserva,

        

        h.nombre AS nombre_habitacion,        
        h.tipoHabitacion,
        h.serviciosIncluidos

    FROM usuarios u
    LEFT JOIN reserva r ON r.id_usuario = u.id_usuario
    LEFT JOIN habitacion h ON r.id_habitacion = h.id_habitacion
    WHERE u.usu_idrol = 2;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_empleados` ()   BEGIN
  SELECT 
    u.id_usuario,
    u.usu_idrol, 
    r.rol_nombre,
    u.nombre,
    u.apellido,
    u.tipoDocumento,
    u.numeroDocumento,
    u.numeroTelefono,
    u.paisProcedencia,
    u.email,
    u.estado,
    u.direccion
  FROM usuarios u
  INNER JOIN rol r ON u.usu_idrol = r.id_rol
  WHERE u.usu_idrol IN (3, 4, 5);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_empleados_por_rol` (IN `p_rol_nombre` VARCHAR(100))   BEGIN
  SELECT 
    u.id_usuario,
    u.usu_idrol,
    r.rol_nombre,
    u.nombre,
    u.apellido,
    u.tipoDocumento,
    u.numeroDocumento,
    u.numeroTelefono,
    u.paisProcedencia,
    u.email,
    u.estado,
    u.direccion
  FROM usuarios u
  INNER JOIN rol r ON u.usu_idrol = r.id_rol
  WHERE u.usu_idrol IN (3, 4, 5)
    AND (p_rol_nombre = '' OR r.rol_nombre = p_rol_nombre);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_empleado_por_id` (IN `p_id_usuario` INT)   BEGIN
    SELECT 
        u.id_usuario,
        u.usu_idrol,
        r.rol_nombre,
        u.nombre,
        u.apellido,
        u.tipoDocumento,
        u.numeroDocumento,
        u.numeroTelefono,
        u.paisProcedencia,
        u.email,
        u.estado,
        u.direccion
    FROM usuarios u
    INNER JOIN rol r ON u.usu_idrol = r.id_rol
    WHERE u.id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_notificaciones_compras` ()   BEGIN
    SELECT 
        c.id AS id_compra,
        c.nombre AS nombre_cliente,
        c.email,
        c.metodo_pago,
        c.fecha,
        dc.nombre_producto,
        dc.cantidad,
        dc.precio,
        cat.nombre_categoria,
        COALESCE(h.nombre, 'No asignada') AS nombre_habitacion,
        h.id_habitacion
    FROM compras c
    JOIN detalle_compras dc ON c.id = dc.id_compra
    JOIN productos p ON dc.nombre_producto = p.nombre
    JOIN categorias cat ON p.id_categoria = cat.id_categoria
    LEFT JOIN reserva r ON r.id_usuario = c.id_usuario
    LEFT JOIN habitacion h ON r.id_habitacion = h.id_habitacion
    WHERE c.leida = 0
    ORDER BY c.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_empleado` (IN `P_usu_idrol` INT, IN `P_nombre` VARCHAR(100), IN `P_apellido` VARCHAR(100), IN `P_tipoDocumento` VARCHAR(100), IN `P_numeroDocumento` VARCHAR(100), IN `P_numeroTelefono` VARCHAR(20), IN `P_paisProcedencia` VARCHAR(100), IN `P_email` VARCHAR(100), IN `P_password` VARCHAR(255), IN `P_reset_token` VARCHAR(255), IN `P_token_expires` DATETIME, IN `P_estado` VARCHAR(100), IN `P_direccion` VARCHAR(100))   BEGIN
    INSERT INTO usuarios (
        usu_idrol, nombre, apellido, tipoDocumento, numeroDocumento,
        numeroTelefono, paisProcedencia, email, password,
        reset_token, token_expires, estado, direccion
    ) VALUES (
        P_usu_idrol, P_nombre, P_apellido, P_tipoDocumento, P_numeroDocumento,
        P_numeroTelefono, P_paisProcedencia, P_email, P_password,
        P_reset_token, P_token_expires, P_estado, P_direccion
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_usuario` (IN `P_usu_idrol` INT, IN `P_nombre` VARCHAR(100), IN `P_apellido` VARCHAR(100), IN `P_tipoDocumento` VARCHAR(10), IN `P_numeroDocumento` VARCHAR(100), IN `P_numeroTelefono` VARCHAR(20), IN `P_paisProcedencia` VARCHAR(100), IN `P_email` VARCHAR(100), IN `P_password` VARCHAR(255), IN `P_reset_token` VARCHAR(255), IN `P_token_expires` DATETIME)   BEGIN
    INSERT INTO usuarios (
        usu_idrol, nombre, apellido, tipoDocumento, numeroDocumento, numeroTelefono, paisProcedencia, email, password, reset_token, token_expires
    )
    VALUES (
        P_usu_idrol, P_nombre, P_apellido, P_tipoDocumento, P_numeroDocumento, P_numeroTelefono, P_paisProcedencia, P_email, P_password, P_reset_token, P_token_expires
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reservas_completadas_por_fecha` (IN `fecha_inicio` DATE, IN `fecha_fin` DATE)   BEGIN
    SELECT 
        DATE(fecha_reserva) AS fecha, 
        COUNT(*) AS total
    FROM reserva
    WHERE estado = 'Confirmada'
      AND DATE(fecha_reserva) BETWEEN fecha_inicio AND fecha_fin
    GROUP BY DATE(fecha_reserva)
    ORDER BY fecha ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_VerificarDisponibilidad` (IN `p_id_habitacion` INT, IN `p_fecha_entrada` DATE, IN `p_fecha_salida` DATE)   BEGIN
    SELECT 
        COUNT(*) = 0 AS disponible
    FROM reserva
    WHERE id_habitacion = p_id_habitacion
      AND estado != 'Cancelada'
      AND (
          (fecha_entrada <= p_fecha_salida AND fecha_salida >= p_fecha_entrada)
      );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'aseo'),
(2, 'comida'),
(3, 'miniBar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `numero_tarjeta` varchar(20) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leida` tinyint(1) DEFAULT 0 COMMENT '0=no leída, 1=leída'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `id_usuario`, `nombre`, `email`, `metodo_pago`, `numero_tarjeta`, `fecha`, `leida`) VALUES
(23, 34, 'Laura', 'lauportillo@cliente.com', 'debito', '98765433456789', '2025-07-10 09:30:30', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE `contacto` (
  `id_contacto` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `motivo` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_contacto` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `nombre_producto` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compras`
--

INSERT INTO `detalle_compras` (`id`, `id_compra`, `id_producto`, `nombre_producto`, `precio`, `cantidad`) VALUES
(29, 23, 1, 'cepillo dientes', 6000.00, 1),
(30, 23, 2, 'galletas festival', 3500.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fecha_evento`
--

CREATE TABLE `fecha_evento` (
  `id_fecha` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tipo_fecha` enum('reserva','entrada','salida','venta','contacto') NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitacion`
--

CREATE TABLE `habitacion` (
  `id_habitacion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipoHabitacion` enum('Sencilla','Doble','Triple','Suite') DEFAULT NULL,
  `piso` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `serviciosIncluidos` varchar(255) NOT NULL,
  `estado` enum('Disponible','Ocupada','Mantenimiento') DEFAULT 'Disponible',
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitacion`
--

INSERT INTO `habitacion` (`id_habitacion`, `nombre`, `tipoHabitacion`, `piso`, `precio`, `serviciosIncluidos`, `estado`, `imagen`) VALUES
(1, '201', 'Sencilla', 2, 120000, 'wifi', 'Ocupada', 'uploads/habitaciones/686c8e6914986_Copia de habitacion_doble.webp'),
(2, '301', 'Doble', 3, 280000, 'wifi', 'Disponible', 'uploads/habitaciones/686ec6df889bd_Copia de habitacion_triple.webp'),
(3, '402', 'Triple', 4, 400000, 'wifi', 'Disponible', 'uploads/habitaciones/686f596c27c19_Copia de suite.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `descripcion`, `imagen`, `stock`, `id_categoria`) VALUES
(1, 'cepillo dientes', 6000.00, 'marca oral B', 'uploads/productos/686f7116d6b16-Copia de habitacion_triple.webp', 4, 1),
(2, 'galletas festival', 3500.00, 'sabor limon', 'uploads/productos/686f715ac8c0a-Copia de habitacion_doble.webp', 4, 2),
(3, 'gaseosa', 4000.00, 'En Colombia, \"gaseosa\" se refiere a una bebida carbonatada, o refresco, usualmente con sabor y edulcorantes, y que se sirve fría. Es decir, es una bebida no alcohólica que contiene dióxido de carbono disuelto, lo que le da su característica efervescenci', 'uploads/productos/686f805f50fb8-Copia de habitacion_doble.webp', 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_habitacion` int(11) NOT NULL,
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `num_huespedes` int(11) NOT NULL,
  `servicios_adicionales` text DEFAULT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Sin reserva') DEFAULT 'Pendiente',
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol_nombre`) VALUES
(1, 'administrador'),
(2, 'cliente'),
(3, 'recepcionista'),
(4, 'cocinero'),
(5, 'camarero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usu_idrol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `tipoDocumento` enum('CC','PA','TI','CE') NOT NULL,
  `numeroDocumento` varchar(100) NOT NULL,
  `numeroTelefono` varchar(20) NOT NULL,
  `paisProcedencia` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `estado` enum('en turno','fuera de turno','vacaciones') DEFAULT NULL,
  `direccion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usu_idrol`, `nombre`, `apellido`, `tipoDocumento`, `numeroDocumento`, `numeroTelefono`, `paisProcedencia`, `email`, `password`, `reset_token`, `token_expires`, `estado`, `direccion`) VALUES
(33, 1, 'Laura', 'Portillo', 'CC', '12456789', '1234567890', 'Colombia', 'lauportillo@administrador.com', '$2y$10$U5My4DbtlVJC90hwsZzVdO4zslIvjBqLqMFSYZw/FhjlsJSKNM/Xa', NULL, NULL, NULL, ''),
(34, 2, 'Laura', 'Portillo', 'CC', '124567890', '123456789', 'Colombia', 'lauportillo@cliente.com', '$2y$10$84/p7U1e7l3exZVKALfoJeSde48RrbueGOMaskndI3DMlEi7.x70m', NULL, NULL, NULL, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compra` (`id_compra`);

--
-- Indices de la tabla `fecha_evento`
--
ALTER TABLE `fecha_evento`
  ADD PRIMARY KEY (`id_fecha`);

--
-- Indices de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD PRIMARY KEY (`id_habitacion`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_habitacion` (`id_habitacion`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `numeroDocumento` (`numeroDocumento`),
  ADD UNIQUE KEY `numeroTelefono` (`numeroTelefono`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usu_idrol` (`usu_idrol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `fecha_evento`
--
ALTER TABLE `fecha_evento`
  MODIFY `id_fecha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  MODIFY `id_habitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD CONSTRAINT `contacto_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_habitacion`) REFERENCES `habitacion` (`id_habitacion`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`usu_idrol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
