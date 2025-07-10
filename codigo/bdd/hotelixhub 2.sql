-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2025 a las 23:49:22
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EliminarHabitacion` (IN `p_numero` VARCHAR(50))   BEGIN
    DELETE FROM habitacion WHERE nombre = p_numero;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_usuario` (IN `p_email` VARCHAR(100))   BEGIN
    SELECT * FROM usuarios WHERE email = p_email;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ObtenerHabitaciones` ()   BEGIN
    SELECT * FROM habitacion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ObtenerHabitacionPorNumero` (IN `p_numero` VARCHAR(50))   BEGIN
    SELECT * FROM habitacion WHERE nombre = p_numero;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_empleados` ()   BEGIN
  SELECT 
    u.id_usuario,
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
  WHERE u.usu_idrol IN (3, 4, 5); -- solo empleados
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_empleado` (IN `P_usu_idrol` INT, IN `P_nombre` VARCHAR(100), IN `P_apellido` VARCHAR(100), IN `P_tipoDocumento` VARCHAR(100), IN `P_numeroDocumento` VARCHAR(10), IN `P_direccion` VARCHAR(100), IN `P_email` VARCHAR(20), IN `P_numeroTelefono` VARCHAR(100), IN `P_estado` VARCHAR(100), IN `P_password` VARCHAR(255), IN `P_reset_token` VARCHAR(255), IN `P_token_expires` DATETIME)   BEGIN
    INSERT INTO usuarios (
        usu_idrol, nombre, apellido, tipoDocumento, numeroDocumento, numeroTelefono, direccion, email, password, estado, reset_token, token_expires
    )
    VALUES (
        P_usu_idrol, P_nombre, P_apellido, P_tipoDocumento, P_numeroDocumento, P_numeroTelefono, P_direccion, P_email, P_password, P_estado, P_reset_token, P_token_expires
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
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `estado` enum('Pendiente','Confirmada','Cancelada') DEFAULT 'Pendiente',
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
  `paisProcedencia` varchar(100) NOT NULL,
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
(1, 1, 'juan', 'diego', 'CC', '1026553308', '3138916559', 'colombia', 'js@gmail.com', '$2y$10$twI7PjcEblpqhTqt9z8yM.HiIORYHtTFvGuEjJsjPYpJCePnuotI6', NULL, NULL, NULL, ''),
(5, 3, 'Juan Diego', 'Sanchez Velosa', 'CC', '1026555330', 'juandis.pt55@gmail.c', '', 'calle 3#10 a 25', '$2y$10$Z3WtTxjpkhUHo9Y330CPv.SM54h1UmWAhwM/AzVmPcFz12aeJKFnu', NULL, NULL, 'en turno', '3138916559');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_anulacion` timestamp NULL DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('Activa','Anulada') DEFAULT 'Activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

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
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
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
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fecha_evento`
--
ALTER TABLE `fecha_evento`
  MODIFY `id_fecha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  MODIFY `id_habitacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

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

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
