-- SECCIÓN A  Sentencias DDL copiadas SIN CAMBIOS de encuestas_db_sql1.sql.
--            Ejecutar SOLO en una base de datos VACÍA (una copia). Sobre
--            encuestas_db ya creada darían error "la tabla ya existe".
-- SECCIÓN B  Ejemplos y propuestas. Lo ejecutable usa tablas TEMPORARY
--            (viven en la sesión) y es seguro sobre la base real. Los
--            cambios permanentes están comentados.
-- Orden de ejecución de los 4 archivos: 1 DDL -> 2 DML -> 4 TCL -> 3 DCL
-- =====================================================================

-- =====================================================================
-- SECCIÓN A. DDL del original (encuestas_db_sql1.sql)
-- =====================================================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- A.1 CREATE TABLE  (original L.30 a L.350)
CREATE TABLE `barrios` (
  `id_barrio` int(10) UNSIGNED NOT NULL,
  `id_comuna` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ciudad` varchar(80) NOT NULL DEFAULT 'Principal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `comunas` (
  `id_comuna` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `zona` enum('urbana','rural') NOT NULL DEFAULT 'urbana'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `georreferenciacion` (
  `id_geo` int(10) UNSIGNED NOT NULL,
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `fuente` enum('ventanilla','encuestador','enrutador') NOT NULL DEFAULT 'ventanilla',
  `registrada_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `roles` (
  `id_rol` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- A.2 Vista de seguimiento del plazo (original L.409 a L.425)
CREATE TABLE `v_solicitudes_por_vencer` (
`id_solicitud` int(10) unsigned
,`radicado` varchar(30)
,`estado` enum('radicada','asignada','visitada','en_revision','apto','no_apto','devuelta','cancelada')
,`fecha_limite` date
,`dias_restantes` int(7)
,`zona` enum('urbana','rural')
);

DROP TABLE IF EXISTS `v_solicitudes_por_vencer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_solicitudes_por_vencer`  AS SELECT `s`.`id_solicitud` AS `id_solicitud`, `s`.`radicado` AS `radicado`, `s`.`estado` AS `estado`, `s`.`fecha_limite` AS `fecha_limite`, to_days(`s`.`fecha_limite`) - to_days(curdate()) AS `dias_restantes`, `c`.`zona` AS `zona` FROM (((`solicitudes` `s` join `solicitantes` `so` on(`so`.`id_solicitante` = `s`.`id_solicitante`)) join `barrios` `b` on(`b`.`id_barrio` = `so`.`id_barrio`)) join `comunas` `c` on(`c`.`id_comuna` = `b`.`id_comuna`)) WHERE `s`.`estado` in ('radicada','asignada','visitada','en_revision','devuelta') ;

-- A.3 ALTER TABLE: llaves primarias, únicas e índices (original L.434 a L.529)
ALTER TABLE `barrios`
  ADD PRIMARY KEY (`id_barrio`),
  ADD UNIQUE KEY `uq_barrio` (`id_comuna`,`nombre`,`ciudad`);

ALTER TABLE `cargas`
  ADD PRIMARY KEY (`id_carga`),
  ADD KEY `fk_carga_encuestador` (`id_encuestador`),
  ADD KEY `fk_carga_enrutador` (`id_enrutador`),
  ADD KEY `fk_carga_entrega` (`id_usuario_entrega`);

ALTER TABLE `comunas`
  ADD PRIMARY KEY (`id_comuna`);

ALTER TABLE `georreferenciacion`
  ADD PRIMARY KEY (`id_geo`),
  ADD UNIQUE KEY `id_solicitud` (`id_solicitud`);

ALTER TABLE `puntos_atencion`
  ADD PRIMARY KEY (`id_punto`),
  ADD KEY `fk_punto_comuna` (`id_comuna`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `solicitantes`
  ADD PRIMARY KEY (`id_solicitante`),
  ADD KEY `fk_solicitante_barrio` (`id_barrio`);

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

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuario_rol` (`id_rol`),
  ADD KEY `fk_usuario_punto` (`id_punto`);

-- A.4 ALTER TABLE: AUTO_INCREMENT (original L.542 a L.614)
ALTER TABLE `barrios`
  MODIFY `id_barrio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `cargas`
  MODIFY `id_carga` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `comunas`
  MODIFY `id_comuna` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `georreferenciacion`
  MODIFY `id_geo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `puntos_atencion`
  MODIFY `id_punto` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `roles`
  MODIFY `id_rol` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `solicitantes`
  MODIFY `id_solicitante` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

-- A.5 ALTER TABLE: llaves foráneas (original L.624 a L.693)
ALTER TABLE `barrios`
  ADD CONSTRAINT `fk_barrio_comuna` FOREIGN KEY (`id_comuna`) REFERENCES `comunas` (`id_comuna`);

ALTER TABLE `cargas`
  ADD CONSTRAINT `fk_carga_encuestador` FOREIGN KEY (`id_encuestador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carga_enrutador` FOREIGN KEY (`id_enrutador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carga_entrega` FOREIGN KEY (`id_usuario_entrega`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `georreferenciacion`
  ADD CONSTRAINT `fk_geo_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;

ALTER TABLE `puntos_atencion`
  ADD CONSTRAINT `fk_punto_comuna` FOREIGN KEY (`id_comuna`) REFERENCES `comunas` (`id_comuna`);

ALTER TABLE `solicitantes`
  ADD CONSTRAINT `fk_solicitante_barrio` FOREIGN KEY (`id_barrio`) REFERENCES `barrios` (`id_barrio`);

ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_sol_carga` FOREIGN KEY (`id_carga`) REFERENCES `cargas` (`id_carga`),
  ADD CONSTRAINT `fk_sol_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`),
  ADD CONSTRAINT `fk_sol_punto_asignado` FOREIGN KEY (`id_punto_asignado`) REFERENCES `puntos_atencion` (`id_punto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sol_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `solicitantes` (`id_solicitante`),
  ADD CONSTRAINT `fk_sol_usuario` FOREIGN KEY (`id_usuario_radica`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

-- =====================================================================
-- SECCIÓN B. Ejemplos y propuestas de DDL
-- =====================================================================

-- ---------------------------------------------------------------------
-- PUNTO 1 (login): auditoría de intentos de ingreso
-- Justificación: roles/usuarios ya cubren el login (UNIQUE correo,
-- FK a roles y puntos). Se agrega una tabla para auditar y bloquear
-- intentos fallidos. Se demuestra en tabla temporal.
-- ---------------------------------------------------------------------
CREATE TEMPORARY TABLE tmp_intentos_login (
  id_intento   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  correo       VARCHAR(120) NOT NULL,
  id_usuario   INT UNSIGNED DEFAULT NULL,
  exito        TINYINT(1)   NOT NULL,
  ip_origen    VARCHAR(45)  DEFAULT NULL,
  intentado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_intento),
  KEY idx_correo_fecha (correo, intentado_en)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PUNTO 2 (ventanilla): unicidad por documento
-- Justificación: hoy solicitantes no impide registrar dos veces el mismo
-- tipo y número de documento. La comuna NO se guarda en solicitantes:
-- se deduce de barrios.id_comuna (comunas.zona distingue comuna urbana
-- de corregimiento rural), lo que evita datos contradictorios.
-- ---------------------------------------------------------------------
-- Novedad de esta versión: solicitudes incluye id_punto_asignado (A.1), con
-- índice idx_sol_punto_asignado (A.3) y llave foránea fk_sol_punto_asignado
-- (A.5) con ON DELETE SET NULL ON UPDATE CASCADE: si un punto de atención se
-- elimina, la solicitud NO se borra; solo queda sin punto asignado. Además,
-- id_punto = punto donde se radicó; id_punto_asignado = punto asignado (opcional).
CREATE TEMPORARY TABLE tmp_solicitantes LIKE solicitantes;
ALTER TABLE tmp_solicitantes
  ADD UNIQUE KEY uq_solicitante_documento (tipo_documento, numero_documento);

-- Propuesta permanente (comentada). Los 3 documentos actuales son
-- distintos, por lo que se aplicaría sin conflictos:
-- ALTER TABLE solicitantes
--   ADD UNIQUE KEY uq_solicitante_documento (tipo_documento, numero_documento);

-- ---------------------------------------------------------------------
-- PUNTO 3 (georreferenciación): validar rango de coordenadas
-- Justificación: latitud DECIMAL(10,8) y longitud DECIMAL(11,8) ya dan
-- precisión de ~1 mm; UNIQUE id_solicitud asegura una georreferencia por
-- solicitud y ON DELETE CASCADE evita registros huérfanos. Se agrega un
-- CHECK de rango.
-- ---------------------------------------------------------------------
CREATE TEMPORARY TABLE tmp_georreferenciacion LIKE georreferenciacion;
ALTER TABLE tmp_georreferenciacion
  ADD CONSTRAINT ck_geo_rango
  CHECK (latitud BETWEEN -90 AND 90 AND longitud BETWEEN -180 AND 180);

-- Propuesta permanente (comentada). La caja delimitadora exacta de Cali
-- debe definirse con límites oficiales antes de endurecer el CHECK:
-- ALTER TABLE georreferenciacion
--   ADD CONSTRAINT ck_geo_rango
--   CHECK (latitud BETWEEN -90 AND 90 AND longitud BETWEEN -180 AND 180),
--   ADD KEY idx_geo_coord (latitud, longitud);

-- Limpieza de las tablas temporales
DROP TEMPORARY TABLE tmp_georreferenciacion;
DROP TEMPORARY TABLE tmp_solicitantes;
DROP TEMPORARY TABLE tmp_intentos_login;
