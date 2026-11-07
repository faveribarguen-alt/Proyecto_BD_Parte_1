-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-11-2026 a las 23:20:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `encuestas_db`
--
CREATE DATABASE IF NOT EXISTS `encuestas_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `encuestas_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barrios`
--

CREATE TABLE `barrios` (
  `id_barrio` int(10) UNSIGNED NOT NULL,
  `id_comuna` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ciudad` varchar(80) NOT NULL DEFAULT 'Principal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargas`
--

CREATE TABLE `cargas` (
  `id_carga` int(10) UNSIGNED NOT NULL,
  `id_encuestador` int(10) UNSIGNED NOT NULL,
  `id_enrutador` int(10) UNSIGNED NOT NULL,
  `fecha_visita` date NOT NULL,
  `estado` enum('programada','en_campo','cerrada') NOT NULL DEFAULT 'programada',
  `creada_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

CREATE TABLE `comunas` (
  `id_comuna` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `zona` enum('urbana','rural') NOT NULL DEFAULT 'urbana'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id_encuesta` int(10) UNSIGNED NOT NULL,
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `id_encuestador` int(10) UNSIGNED NOT NULL,
  `fecha_visita` datetime NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `georreferenciacion`
--

CREATE TABLE `georreferenciacion` (
  `id_geo` int(10) UNSIGNED NOT NULL,
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `fuente` enum('ventanilla','encuestador','enrutador') NOT NULL DEFAULT 'ventanilla',
  `registrada_en` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_estados`
--

CREATE TABLE `historial_estados` (
  `id_historial` int(10) UNSIGNED NOT NULL,
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `estado` varchar(20) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `cambiado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menores`
--

CREATE TABLE `menores` (
  `id_menor` int(10) UNSIGNED NOT NULL,
  `id_encuesta` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad_declarada` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_atencion`
--

CREATE TABLE `puntos_atencion` (
  `id_punto` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `revisiones`
--

CREATE TABLE `revisiones` (
  `id_revision` int(10) UNSIGNED NOT NULL,
  `id_encuesta` int(10) UNSIGNED NOT NULL,
  `id_usuario_calidad` int(10) UNSIGNED NOT NULL,
  `resultado` enum('apto','no_apto','devuelta') NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `revisada_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitantes`
--

CREATE TABLE `solicitantes` (
  `id_solicitante` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `direccion` varchar(150) NOT NULL,
  `id_barrio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `radicado` varchar(30) NOT NULL,
  `id_solicitante` int(10) UNSIGNED NOT NULL,
  `id_punto` smallint(5) UNSIGNED NOT NULL,
  `id_usuario_radica` int(10) UNSIGNED NOT NULL,
  `id_carga` int(10) UNSIGNED DEFAULT NULL,
  `fecha_radicacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date NOT NULL,
  `estado` enum('radicada','asignada','visitada','en_revision','apto','no_apto','devuelta','cancelada') NOT NULL DEFAULT 'radicada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_rol` tinyint(3) UNSIGNED NOT NULL,
  `id_punto` smallint(5) UNSIGNED DEFAULT NULL,
  `nombres` varchar(80) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_solicitudes_por_vencer`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_solicitudes_por_vencer` (
`id_solicitud` int(10) unsigned
,`radicado` varchar(30)
,`estado` enum('radicada','asignada','visitada','en_revision','apto','no_apto','devuelta','cancelada')
,`fecha_limite` date
,`dias_restantes` int(7)
,`zona` enum('urbana','rural')
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_solicitudes_por_vencer`
--
DROP TABLE IF EXISTS `v_solicitudes_por_vencer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_solicitudes_por_vencer`  AS SELECT `s`.`id_solicitud` AS `id_solicitud`, `s`.`radicado` AS `radicado`, `s`.`estado` AS `estado`, `s`.`fecha_limite` AS `fecha_limite`, to_days(`s`.`fecha_limite`) - to_days(curdate()) AS `dias_restantes`, `c`.`zona` AS `zona` FROM (((`solicitudes` `s` join `solicitantes` `so` on(`so`.`id_solicitante` = `s`.`id_solicitante`)) join `barrios` `b` on(`b`.`id_barrio` = `so`.`id_barrio`)) join `comunas` `c` on(`c`.`id_comuna` = `b`.`id_comuna`)) WHERE `s`.`estado` in ('radicada','asignada','visitada','en_revision','devuelta') ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `barrios`
--
ALTER TABLE `barrios`
  ADD PRIMARY KEY (`id_barrio`),
  ADD UNIQUE KEY `uq_barrio` (`id_comuna`,`nombre`,`ciudad`);

--
-- Indices de la tabla `cargas`
--
ALTER TABLE `cargas`
  ADD PRIMARY KEY (`id_carga`),
  ADD KEY `fk_carga_encuestador` (`id_encuestador`),
  ADD KEY `fk_carga_enrutador` (`id_enrutador`);

--
-- Indices de la tabla `comunas`
--
ALTER TABLE `comunas`
  ADD PRIMARY KEY (`id_comuna`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id_encuesta`),
  ADD UNIQUE KEY `id_solicitud` (`id_solicitud`),
  ADD KEY `fk_enc_encuestador` (`id_encuestador`);

--
-- Indices de la tabla `georreferenciacion`
--
ALTER TABLE `georreferenciacion`
  ADD PRIMARY KEY (`id_geo`),
  ADD UNIQUE KEY `id_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `fk_hist_solicitud` (`id_solicitud`),
  ADD KEY `fk_hist_usuario` (`id_usuario`);

--
-- Indices de la tabla `menores`
--
ALTER TABLE `menores`
  ADD PRIMARY KEY (`id_menor`),
  ADD KEY `fk_menor_encuesta` (`id_encuesta`);

--
-- Indices de la tabla `puntos_atencion`
--
ALTER TABLE `puntos_atencion`
  ADD PRIMARY KEY (`id_punto`);

--
-- Indices de la tabla `revisiones`
--
ALTER TABLE `revisiones`
  ADD PRIMARY KEY (`id_revision`),
  ADD KEY `fk_rev_encuesta` (`id_encuesta`),
  ADD KEY `fk_rev_usuario` (`id_usuario_calidad`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  ADD PRIMARY KEY (`id_solicitante`),
  ADD KEY `fk_solicitante_barrio` (`id_barrio`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD UNIQUE KEY `radicado` (`radicado`),
  ADD KEY `fk_sol_solicitante` (`id_solicitante`),
  ADD KEY `fk_sol_punto` (`id_punto`),
  ADD KEY `fk_sol_usuario` (`id_usuario_radica`),
  ADD KEY `fk_sol_carga` (`id_carga`),
  ADD KEY `idx_estado_limite` (`estado`,`fecha_limite`),
  ADD KEY `idx_fecha_radicacion` (`fecha_radicacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuario_rol` (`id_rol`),
  ADD KEY `fk_usuario_punto` (`id_punto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `barrios`
--
ALTER TABLE `barrios`
  MODIFY `id_barrio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargas`
--
ALTER TABLE `cargas`
  MODIFY `id_carga` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comunas`
--
ALTER TABLE `comunas`
  MODIFY `id_comuna` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id_encuesta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `georreferenciacion`
--
ALTER TABLE `georreferenciacion`
  MODIFY `id_geo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  MODIFY `id_historial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `menores`
--
ALTER TABLE `menores`
  MODIFY `id_menor` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `puntos_atencion`
--
ALTER TABLE `puntos_atencion`
  MODIFY `id_punto` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `revisiones`
--
ALTER TABLE `revisiones`
  MODIFY `id_revision` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  MODIFY `id_solicitante` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `barrios`
--
ALTER TABLE `barrios`
  ADD CONSTRAINT `fk_barrio_comuna` FOREIGN KEY (`id_comuna`) REFERENCES `comunas` (`id_comuna`);

--
-- Filtros para la tabla `cargas`
--
ALTER TABLE `cargas`
  ADD CONSTRAINT `fk_carga_encuestador` FOREIGN KEY (`id_encuestador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carga_enrutador` FOREIGN KEY (`id_enrutador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD CONSTRAINT `fk_enc_encuestador` FOREIGN KEY (`id_encuestador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_enc_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`);

--
-- Filtros para la tabla `georreferenciacion`
--
ALTER TABLE `georreferenciacion`
  ADD CONSTRAINT `fk_geo_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  ADD CONSTRAINT `fk_hist_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`),
  ADD CONSTRAINT `fk_hist_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `menores`
--
ALTER TABLE `menores`
  ADD CONSTRAINT `fk_menor_encuesta` FOREIGN KEY (`id_encuesta`) REFERENCES `encuestas` (`id_encuesta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `revisiones`
--
ALTER TABLE `revisiones`
  ADD CONSTRAINT `fk_rev_encuesta` FOREIGN KEY (`id_encuesta`) REFERENCES `encuestas` (`id_encuesta`),
  ADD CONSTRAINT `fk_rev_usuario` FOREIGN KEY (`id_usuario_calidad`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  ADD CONSTRAINT `fk_solicitante_barrio` FOREIGN KEY (`id_barrio`) REFERENCES `barrios` (`id_barrio`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_sol_carga` FOREIGN KEY (`id_carga`) REFERENCES `cargas` (`id_carga`),
  ADD CONSTRAINT `fk_sol_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`),
  ADD CONSTRAINT `fk_sol_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `solicitantes` (`id_solicitante`),
  ADD CONSTRAINT `fk_sol_usuario` FOREIGN KEY (`id_usuario_radica`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
