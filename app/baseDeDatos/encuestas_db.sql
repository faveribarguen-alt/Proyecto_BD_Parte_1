-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

--Este archivo contiene la estructura y los datos de la base de datos `encuestas_db` 
--completamente exportada para trabajar con phpMyAdmin 5.2.1 y MySQL 8.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: `encuestas_db`
CREATE DATABASE IF NOT EXISTS encuestas_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;
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

--
-- Volcado de datos para la tabla `barrios`
--

INSERT INTO `barrios` (`id_barrio`, `id_comuna`, `nombre`, `ciudad`) VALUES
(1, 1, 'Centro', 'Principal'),
(2, 1, 'Terrón Colorado', 'Principal'),
(3, 1, 'Vista Hermosa', 'Principal'),
(4, 6, 'Floralia', 'Principal'),
(5, 6, 'Petecuy', 'Principal'),
(6, 7, 'Alfonso López', 'Principal'),
(7, 7, 'Calima', 'Principal'),
(9, 13, 'Ciudad Córdoba', 'Principal'),
(8, 13, 'El Vergel', 'Principal'),
(11, 14, 'Alirio Mora Beltrán', 'Principal'),
(10, 14, 'Manuela Beltrán', 'Principal'),
(12, 17, 'Ciudad Jardín', 'Principal'),
(13, 17, 'Valle del Lili', 'Principal'),
(15, 20, 'Belisario Caicedo', 'Principal'),
(14, 20, 'Siloé', 'Principal'),
(16, 21, 'El Retiro', 'Principal'),
(17, 21, 'Potrero Grande', 'Principal');

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
  `creada_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `entregada_en` datetime DEFAULT NULL,
  `id_usuario_entrega` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargas`
--

INSERT INTO `cargas` (`id_carga`, `id_encuestador`, `id_enrutador`, `fecha_visita`, `estado`, `creada_en`, `entregada_en`, `id_usuario_entrega`) VALUES
(1, 3, 1, '2026-11-09', 'en_campo', '2026-11-08 23:10:34', '2026-11-09 19:21:17', 2),
(2, 3, 1, '2026-11-10', 'programada', '2026-11-10 00:26:41', NULL, NULL),
(3, 3, 1, '2026-11-11', 'programada', '2026-11-11 05:32:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

CREATE TABLE `comunas` (
  `id_comuna` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `zona` enum('urbana','rural') NOT NULL DEFAULT 'urbana'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comunas`
--

INSERT INTO `comunas` (`id_comuna`, `nombre`, `zona`) VALUES
(1, 'Comuna 1', 'urbana'),
(2, 'Comuna 2', 'urbana'),
(3, 'Comuna 3', 'urbana'),
(4, 'Comuna 4', 'urbana'),
(5, 'Comuna 5', 'urbana'),
(6, 'Comuna 6', 'urbana'),
(7, 'Comuna 7', 'urbana'),
(8, 'Comuna 8', 'urbana'),
(9, 'Comuna 9', 'urbana'),
(10, 'Comuna 10', 'urbana'),
(11, 'Comuna 11', 'urbana'),
(12, 'Comuna 12', 'urbana'),
(13, 'Comuna 13', 'urbana'),
(14, 'Comuna 14', 'urbana'),
(15, 'Comuna 15', 'urbana'),
(16, 'Comuna 16', 'urbana'),
(17, 'Comuna 17', 'urbana'),
(18, 'Comuna 18', 'urbana'),
(19, 'Comuna 19', 'urbana'),
(20, 'Comuna 20', 'urbana'),
(21, 'Comuna 21', 'urbana'),
(22, 'Comuna 22', 'urbana'),
(23, 'Corregimiento Los Andes', 'rural'),
(24, 'Corregimiento Golondrinas', 'rural'),
(25, 'Corregimiento La Elvira', 'rural'),
(26, 'Corregimiento El Saladito', 'rural'),
(27, 'Corregimiento La Leonera', 'rural'),
(28, 'Corregimiento Felidia', 'rural'),
(29, 'Corregimiento Pichindé', 'rural'),
(30, 'Corregimiento Montebello', 'rural'),
(31, 'Corregimiento La Paz', 'rural'),
(32, 'Corregimiento Villacarmelo', 'rural'),
(33, 'Corregimiento La Castilla', 'rural'),
(34, 'Corregimiento La Buitrera', 'rural'),
(35, 'Corregimiento Navarro', 'rural'),
(36, 'Corregimiento El Hormiguero', 'rural'),
(37, 'Corregimiento Pance', 'rural');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `georreferenciacion`
--

INSERT INTO `georreferenciacion` (`id_geo`, `id_solicitud`, `latitud`, `longitud`, `fuente`, `registrada_en`) VALUES
(1, 1, 3.24185311, -76.51501473, 'ventanilla', '2026-11-08 21:54:05'),
(2, 2, 3.24204660, -76.51540158, 'ventanilla', '2026-11-08 22:54:35'),
(3, 3, 3.24198744, -76.51539935, 'ventanilla', '2026-11-10 00:25:03'),
(4, 4, 3.26448046, -76.54508120, 'ventanilla', '2026-11-11 04:55:14');

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

--
-- Volcado de datos para la tabla `historial_estados`
--

INSERT INTO `historial_estados` (`id_historial`, `id_solicitud`, `estado`, `id_usuario`, `cambiado_en`) VALUES
(1, 1, 'asignada', 1, '2026-11-08 23:10:34'),
(2, 2, 'asignada', 1, '2026-11-08 23:10:34'),
(3, 3, 'asignada', 1, '2026-11-10 00:26:41'),
(4, 4, 'asignada', 1, '2026-11-11 05:32:02');

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
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_comuna` smallint(5) UNSIGNED DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puntos_atencion`
--

INSERT INTO `puntos_atencion` (`id_punto`, `nombre`, `direccion`, `es_principal`, `activo`, `id_comuna`, `latitud`, `longitud`) VALUES
(1, 'Oficina Principal - Comuna 17', 'Comuna 17', 1, 1, 17, 3.39000000, -76.53500000),
(2, 'Punto de Atención - Comuna 1', 'Comuna 1', 0, 1, 1, 3.46000000, -76.56000000),
(3, 'Punto de Atención - Comuna 6', 'Comuna 6', 0, 1, 6, 3.47200000, -76.49500000),
(4, 'Punto de Atención - Comuna 7', 'Comuna 7', 0, 1, 7, 3.46000000, -76.48500000),
(5, 'Punto de Atención - Comuna 13', 'Comuna 13', 0, 1, 13, 3.41500000, -76.48300000),
(6, 'Punto de Atención - Comuna 14', 'Comuna 14', 0, 1, 14, 3.42500000, -76.47000000),
(7, 'Punto de Atención - Comuna 20', 'Comuna 20', 0, 1, 20, 3.42900000, -76.55800000),
(8, 'Punto de Atención - Comuna 21', 'Comuna 21', 0, 1, 21, 3.40900000, -76.46800000);

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

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'enrutador', 'Perfil que ve la bitacora'),
(2, 'ventanilla', 'Perfil que registra solicitudes'),
(3, 'encuestador', 'Perfil que realiza visitas en campo'),
(4, 'calidad', 'Perfil que revisa y aprueba encuestas'),
(5, 'administrador', 'Administrador general del sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitantes`
--

CREATE TABLE `solicitantes` (
  `id_solicitante` int(10) UNSIGNED NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `numero_documento` varchar(30) NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `direccion` varchar(150) NOT NULL,
  `id_barrio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitantes`
--

INSERT INTO `solicitantes` (`id_solicitante`, `tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `telefono`, `correo`, `direccion`, `id_barrio`) VALUES
(1, 'CC', '1105371870', 'LUIS', 'RENGIFO', '3026658879', NULL, 'CL 20 B  40 F SUR 10', 1),
(2, 'CC', '51651', 'felipe', 'galindo', '3045451', NULL, 'CL 21 B  41  F SUR 11', 2),
(3, 'CC', '5165154', 'MIGUEL', 'ARANGO', '3045451474', NULL, 'CL 20 B  40 F SUR 10', 14),
(4, 'CC', '111495', 'Lucia', 'Arango', '3107772898', NULL, 'trnasversal 17 #10-10 ', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `radicado` varchar(30) NOT NULL,
  `id_solicitante` int(10) UNSIGNED NOT NULL,
  `id_punto` smallint(5) UNSIGNED NOT NULL,
  `id_punto_asignado` smallint(5) UNSIGNED DEFAULT NULL,
  `id_usuario_radica` int(10) UNSIGNED NOT NULL,
  `id_carga` int(10) UNSIGNED DEFAULT NULL,
  `fecha_radicacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date NOT NULL,
  `estado` enum('radicada','asignada','visitada','en_revision','apto','no_apto','devuelta','cancelada') NOT NULL DEFAULT 'radicada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id_solicitud`, `radicado`, `id_solicitante`, `id_punto`, `id_punto_asignado`, `id_usuario_radica`, `id_carga`, `fecha_radicacion`, `fecha_limite`, `estado`) VALUES
(1, 'RAD-20261108-225405', 1, 1, 1, 2, 1, '2026-11-08 16:54:05', '2026-11-23', 'asignada'),
(2, 'RAD-20261108-235435', 2, 1, 1, 2, 1, '2026-11-08 17:54:35', '2026-11-23', 'asignada'),
(3, 'RAD-20261109-192503', 3, 1, 1, 2, 2, '2026-11-09 19:25:03', '2026-11-24', 'asignada'),
(4, 'RAD-20261110-235514', 4, 1, 1, 2, 3, '2026-11-10 23:55:14', '2026-11-25', 'asignada');

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

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `id_punto`, `nombres`, `apellidos`, `correo`, `password_hash`, `estado`, `creado_en`) VALUES
(1, 1, 1, 'Carlos', 'Enrutador', 'enrutador@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-11-08 21:35:01'),
(2, 2, 1, 'Maria', 'Ventanilla', 'ventanilla@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-11-08 21:35:01'),
(3, 3, 1, 'Pedro', 'Encuestador', 'encuestador@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-11-08 22:33:25'),
(4, 4, 1, 'Laura', 'Calidad', 'calidad@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-11-08 22:33:25'),
(5, 5, 1, 'Admin', 'General', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-11-08 22:33:25'),
(6, 3, 1, 'Andres', 'Ramirez', 'andres@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(7, 3, 1, 'Laura', 'Martinez', 'laura@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(8, 3, 2, 'Carlos', 'Hernandez', 'carlos@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(9, 3, 2, 'Sofia', 'Torres', 'sofia@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(10, 3, 3, 'Daniel', 'Gomez', 'daniel@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(11, 3, 3, 'Valentina', 'Castro', 'valentina@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(12, 3, 4, 'Sebastian', 'Lopez', 'sebastian@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(13, 3, 4, 'Camila', 'Rodriguez', 'camila@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(14, 3, 5, 'Mateo', 'Perez', 'mateo@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(15, 3, 5, 'Isabella', 'Moreno', 'isabella@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(16, 3, 6, 'Juan', 'Vargas', 'juan@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(17, 3, 6, 'Mariana', 'Sanchez', 'mariana@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(18, 3, 7, 'Nicolas', 'Herrera', 'nicolas@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(19, 3, 7, 'Gabriela', 'Diaz', 'gabriela@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(20, 3, 8, 'Samuel', 'Ortiz', 'samuel@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(21, 3, 8, 'Lucia', 'Mendoza', 'lucia@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(22, 3, 7, 'David', 'Rojas', 'david@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(23, 3, 5, 'Paula', 'Jimenez', 'paula@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(24, 3, 4, 'Diego', 'Navarro', 'diego@encuestas360.com', '$2y$12$M3CWfp1/7GQ8AmpoeZSwsexX4lwj0OcUhXlweU7J2ocxFVYt6DlHm', 'activo', '2026-11-11 20:19:24'),
(25, 4, 1, 'Natalia', 'Fernandez', 'natalia@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(26, 4, 1, 'Ricardo', 'Molina', 'ricardo@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(27, 4, 1, 'Patricia', 'Cortes', 'patricia@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(28, 4, 1, 'Fernando', 'Silva', 'fernando@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(29, 2, 2, 'Andrea', 'Quintero', 'andrea@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(30, 2, 3, 'Monica', 'Salazar', 'monica@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(31, 2, 4, 'Felipe', 'Cardona', 'felipe@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(32, 2, 5, 'Daniela', 'Acosta', 'daniela@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(33, 2, 6, 'Alejandro', 'Velez', 'alejandro@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(34, 2, 7, 'Carolina', 'Mejia', 'carolina@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4o3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39'),
(35, 2, 8, 'Jorge', 'Cabrera', 'jorge@encuestas360.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oEa3Ro9llC/.og/at2uheWG/igi.', 'activo', '2026-11-11 20:31:39');

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
  ADD KEY `fk_carga_enrutador` (`id_enrutador`),
  ADD KEY `fk_carga_entrega` (`id_usuario_entrega`);

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
  ADD PRIMARY KEY (`id_punto`),
  ADD KEY `fk_punto_comuna` (`id_comuna`);

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
  ADD KEY `idx_fecha_radicacion` (`fecha_radicacion`),
  ADD KEY `idx_sol_punto_asignado` (`id_punto_asignado`);

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
  MODIFY `id_barrio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `cargas`
--
ALTER TABLE `cargas`
  MODIFY `id_carga` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comunas`
--
ALTER TABLE `comunas`
  MODIFY `id_comuna` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id_encuesta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `georreferenciacion`
--
ALTER TABLE `georreferenciacion`
  MODIFY `id_geo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_estados`
--
ALTER TABLE `historial_estados`
  MODIFY `id_historial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `menores`
--
ALTER TABLE `menores`
  MODIFY `id_menor` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `puntos_atencion`
--
ALTER TABLE `puntos_atencion`
  MODIFY `id_punto` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `revisiones`
--
ALTER TABLE `revisiones`
  MODIFY `id_revision` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `solicitantes`
--
ALTER TABLE `solicitantes`
  MODIFY `id_solicitante` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  ADD CONSTRAINT `fk_carga_enrutador` FOREIGN KEY (`id_enrutador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carga_entrega` FOREIGN KEY (`id_usuario_entrega`) REFERENCES `usuarios` (`id_usuario`);

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
-- Filtros para la tabla `puntos_atencion`
--
ALTER TABLE `puntos_atencion`
  ADD CONSTRAINT `fk_punto_comuna` FOREIGN KEY (`id_comuna`) REFERENCES `comunas` (`id_comuna`);

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
  ADD CONSTRAINT `fk_sol_punto_asignado` FOREIGN KEY (`id_punto_asignado`) REFERENCES `puntos_atencion` (`id_punto`) ON DELETE SET NULL ON UPDATE CASCADE,
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
