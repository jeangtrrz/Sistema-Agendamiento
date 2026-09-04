-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-09-2026 a las 17:21:52
-- Versión del servidor: 10.6.27-MariaDB-cll-lve
-- Versión de PHP: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `internet_agenda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_telefono` varchar(20) NOT NULL,
  `cliente_email` varchar(100) DEFAULT NULL,
  `cliente_direccion` varchar(255) DEFAULT NULL,
  `tipo_cita` enum('instalacion','retiro','soporte') NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('pendiente','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `cliente_nombre`, `cliente_telefono`, `cliente_email`, `cliente_direccion`, `tipo_cita`, `fecha_cita`, `hora_inicio`, `hora_fin`, `tecnico_id`, `descripcion`, `estado`, `observaciones`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 'Julián Herrera', '+56 9 8575 8752', 'julianmhr501@gmail.com', 'Calle 465 número 5489 las torres 3 Peñalolen', 'instalacion', '2026-06-01', '16:00:00', '17:00:00', 1, 'Son dos instalaciones en la misma dirección', 'completada', 'Realizado', 2, '2026-06-01 17:44:10', '2026-06-02 14:50:18'),
(7, 'Jimena Figueroa', '+56 9 6684 4599', 'floresnatita1234@gmail.com', 'El Parque 1340 Peñalolén', 'instalacion', '2026-06-02', '10:00:00', '11:00:00', 1, 'wifi: Sofía\r\ncontraseña: emma2022', 'completada', 'Realizado', 2, '2026-06-01 17:45:32', '2026-06-02 15:42:30'),
(8, '5° Jornada Nacional ISP 2026', '+56 9 2243 6235', 'contacto@internetpenalolen.cl', '𝐇𝐨𝐭𝐞𝐥 𝐌𝐚𝐫𝐫𝐢𝐨𝐭𝐭 𝐒𝐚𝐧𝐭𝐢𝐚𝐠𝐨', 'soporte', '2026-09-03', '09:00:00', '10:00:00', 1, 'Evento Hayex ISP 2026', 'completada', 'listo', 2, '2026-08-04 16:45:22', '2026-09-01 16:21:05'),
(9, 'Lourdes Cardozo', '56963321852', 'katerinecalle45@gmail.com', 'Apolitos 6471, Peñalolen', 'instalacion', '2026-09-04', '17:00:00', '18:00:00', 1, 'Plan Estandar', 'pendiente', NULL, 2, '2026-09-01 16:20:54', '2026-09-01 16:20:54'),
(10, 'Juan Carlos Delgado Vinasco', '56977471942', 'jauncarlosdelgado462@gmail.com', 'Desfiladero 6360 Peñalolén ', 'instalacion', '2026-09-01', '15:30:00', '16:30:00', 1, 'Plan full\r\nONU S/N: 48575443BDA07DB4', 'completada', 'Instalación realizada 02-09-2026', 2, '2026-09-01 16:22:22', '2026-09-02 20:29:47'),
(11, 'Edith Cuevas Castañón', '56971926617', 'edith.cuevas1891@gmail.com', 'Calle perú 2399, Peñalolén', 'instalacion', '2026-09-02', '12:00:00', '13:00:00', 1, 'Plan Estandar', 'completada', 'Instalación realizada', 2, '2026-09-01 16:23:56', '2026-09-02 20:29:22'),
(12, 'Frangelys Capote Ramírez', '+56 9 9778 1614', 'frangelysc13@gmail.com', 'Laguna San Pedro 1725', 'instalacion', '2026-09-03', '14:00:00', '15:00:00', 1, 'RUT: 266706162\r\nWiFi: Familia Capote \r\nClave: Frange.2310\r\nPlan Estándar', 'pendiente', NULL, 2, '2026-09-02 20:26:36', '2026-09-02 20:26:36'),
(13, 'Jocelyn Emilene Arevalo Araneda', '+56 9 5681 6427', 'jearevaloaraneda@gmail.com', 'Calle 114-B casa 986 Peñalolén', 'instalacion', '2026-09-05', '10:00:00', '11:00:00', 1, 'RUT: 154655344\r\nWiFi: CAMILO\r\nClave: Yosi2026\r\nPlan: Full', 'pendiente', NULL, 2, '2026-09-02 20:28:06', '2026-09-02 20:28:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo_evento` enum('reunion','compromiso','evento') NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time DEFAULT NULL,
  `responsable_id` int(11) NOT NULL,
  `lugar` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('programado','completado','cancelado') NOT NULL DEFAULT 'programado',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `tipo_evento`, `fecha_evento`, `hora_inicio`, `hora_fin`, `responsable_id`, `lugar`, `descripcion`, `estado`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Reunión semanal', 'reunion', '2026-08-04', '11:00:00', '12:30:00', 4, 'Planta', '', '', 2, '2026-08-04 18:56:32', '2026-08-04 18:56:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_citas`
--

CREATE TABLE `historial_citas` (
  `id` int(11) NOT NULL,
  `cita_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo` enum('completadas','pendientes','canceladas','por_tipo','por_tecnico') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `datos_json` longtext DEFAULT NULL,
  `generado_por` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `perfil` enum('tecnico','administrador') NOT NULL DEFAULT 'tecnico',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `perfil`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Cesar Camaño', 'ccamano@internetcordillera.cl', '$2y$10$unTuAczANzn8XJU2COSgFutSw9It.SRmj3Og6HWK2SPLAcij8XmgK', 'tecnico', 'activo', '2026-05-31 23:05:16', '2026-09-01 16:51:03'),
(2, 'Jean Gutierrez', 'jpgutierrez@internetcordillera.cl', '$2y$10$AH7R4GIJ8wFtnSUy78MV7O4mg79p10lkZ4RXr3zbnES7qDrF4P5ea', 'administrador', 'activo', '2026-05-31 23:05:16', '2026-06-01 13:28:13'),
(4, 'Gabriela Castro', 'gcastro@internetcordillera.cl', '$2y$10$HZ1txYh8RCTsibnNDGuJDeN4xc5oxImgzeUOFU0bLuGUgQiDj5eAG', 'administrador', 'activo', '2026-06-01 15:24:42', '2026-09-01 16:27:52'),
(5, 'Jaime Castro', 'jaimecastro@internetcordillera.cl', '$2y$10$7bT4ZbOntKnWeTE3A4aHCuRyudJuBmiHcIu29O/2IsMG0S8YbsLdO', 'administrador', 'activo', '2026-06-01 15:40:05', '2026-06-01 15:40:05');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_citas_completadas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_citas_completadas` (
`total` bigint(21)
,`tipo_cita` enum('instalacion','retiro','soporte')
,`fecha` date
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_citas_pendientes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_citas_pendientes` (
`total` bigint(21)
,`tipo_cita` enum('instalacion','retiro','soporte')
,`fecha` date
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_citas_por_tecnico`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_citas_por_tecnico` (
`id` int(11)
,`nombre` varchar(100)
,`total_citas` bigint(21)
,`completadas` decimal(22,0)
,`pendientes` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_resumen_diario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_resumen_diario` (
`fecha` date
,`total_citas` bigint(21)
,`completadas` decimal(22,0)
,`pendientes` decimal(22,0)
,`canceladas` decimal(22,0)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cita_horario` (`fecha_cita`,`hora_inicio`,`tecnico_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_fecha` (`fecha_cita`),
  ADD KEY `idx_tecnico` (`tecnico_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_tipo` (`tipo_cita`),
  ADD KEY `idx_cliente_email` (`cliente_email`),
  ADD KEY `idx_fecha_hora` (`fecha_cita`,`hora_inicio`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_fecha_evento` (`fecha_evento`),
  ADD KEY `idx_responsable` (`responsable_id`),
  ADD KEY `idx_estado_evento` (`estado`);

--
-- Indices de la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_cita` (`cita_id`),
  ADD KEY `idx_fecha` (`created_at`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generado_por` (`generado_por`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_fecha` (`created_at`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_perfil` (`perfil`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_estado_perfil` (`estado`,`perfil`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_citas_completadas`
--
DROP TABLE IF EXISTS `vw_citas_completadas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`internet`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_completadas`  AS SELECT count(0) AS `total`, `citas`.`tipo_cita` AS `tipo_cita`, cast(`citas`.`created_at` as date) AS `fecha` FROM `citas` WHERE `citas`.`estado` = 'completada' GROUP BY `citas`.`tipo_cita`, cast(`citas`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_citas_pendientes`
--
DROP TABLE IF EXISTS `vw_citas_pendientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`internet`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_pendientes`  AS SELECT count(0) AS `total`, `citas`.`tipo_cita` AS `tipo_cita`, cast(`citas`.`created_at` as date) AS `fecha` FROM `citas` WHERE `citas`.`estado` = 'pendiente' GROUP BY `citas`.`tipo_cita`, cast(`citas`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_citas_por_tecnico`
--
DROP TABLE IF EXISTS `vw_citas_por_tecnico`;

CREATE ALGORITHM=UNDEFINED DEFINER=`internet`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_por_tecnico`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, count(`c`.`id`) AS `total_citas`, sum(case when `c`.`estado` = 'completada' then 1 else 0 end) AS `completadas`, sum(case when `c`.`estado` = 'pendiente' then 1 else 0 end) AS `pendientes` FROM (`usuarios` `u` left join `citas` `c` on(`u`.`id` = `c`.`tecnico_id`)) WHERE `u`.`perfil` = 'tecnico' GROUP BY `u`.`id`, `u`.`nombre` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_resumen_diario`
--
DROP TABLE IF EXISTS `vw_resumen_diario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`internet`@`localhost` SQL SECURITY DEFINER VIEW `vw_resumen_diario`  AS SELECT cast(`citas`.`fecha_cita` as date) AS `fecha`, count(0) AS `total_citas`, sum(case when `citas`.`estado` = 'completada' then 1 else 0 end) AS `completadas`, sum(case when `citas`.`estado` = 'pendiente' then 1 else 0 end) AS `pendientes`, sum(case when `citas`.`estado` = 'cancelada' then 1 else 0 end) AS `canceladas` FROM `citas` GROUP BY cast(`citas`.`fecha_cita` as date) ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `eventos_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_citas`
--
ALTER TABLE `historial_citas`
  ADD CONSTRAINT `historial_citas_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_citas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`generado_por`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
