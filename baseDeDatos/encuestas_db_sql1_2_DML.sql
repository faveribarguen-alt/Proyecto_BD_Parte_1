-- Tablas: roles, usuarios, puntos_atencion, comunas, barrios,
--         solicitantes, solicitudes, georreferenciacion, cargas
--         (misma lista del archivo 1_DDL).
--
-- SECCIÓN A  INSERT copiados SIN CAMBIOS de encuestas_db_sql1.sql, reordenados
--            por dependencia de llaves foráneas (padres antes que hijos).
--            Ejecutar SOLO en una base VACÍA donde ya se corrió 1_DDL.
--            Sobre encuestas_db con datos darían error de llave duplicada.
-- SECCIÓN B  Ejemplos de consulta y manipulación. Es SEGURA sobre la base
--            real: SELECT, escrituras en copias TEMPORARY y escrituras
--            dentro de una transacción que termina en ROLLBACK.
-- =====================================================================

-- =====================================================================
-- SECCIÓN A. INSERT del original (encuestas_db_sql1.sql)
-- =====================================================================
-- comunas
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

-- barrios
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

-- roles
INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'enrutador', 'Perfil que ve la bitacora'),
(2, 'ventanilla', 'Perfil que registra solicitudes'),
(3, 'encuestador', 'Perfil que realiza visitas en campo'),
(4, 'calidad', 'Perfil que revisa y aprueba encuestas'),
(5, 'administrador', 'Administrador general del sistema');

-- puntos_atencion
INSERT INTO `puntos_atencion` (`id_punto`, `nombre`, `direccion`, `es_principal`, `activo`, `id_comuna`, `latitud`, `longitud`) VALUES
(1, 'Oficina Principal - Comuna 17', 'Comuna 17', 1, 1, 17, 3.39000000, -76.53500000),
(2, 'Punto de Atención - Comuna 1', 'Comuna 1', 0, 1, 1, 3.46000000, -76.56000000),
(3, 'Punto de Atención - Comuna 6', 'Comuna 6', 0, 1, 6, 3.47200000, -76.49500000),
(4, 'Punto de Atención - Comuna 7', 'Comuna 7', 0, 1, 7, 3.46000000, -76.48500000),
(5, 'Punto de Atención - Comuna 13', 'Comuna 13', 0, 1, 13, 3.41500000, -76.48300000),
(6, 'Punto de Atención - Comuna 14', 'Comuna 14', 0, 1, 14, 3.42500000, -76.47000000),
(7, 'Punto de Atención - Comuna 20', 'Comuna 20', 0, 1, 20, 3.42900000, -76.55800000),
(8, 'Punto de Atención - Comuna 21', 'Comuna 21', 0, 1, 21, 3.40900000, -76.46800000);

-- usuarios
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

-- solicitantes
INSERT INTO `solicitantes` (`id_solicitante`, `tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `telefono`, `correo`, `direccion`, `id_barrio`) VALUES
(1, 'CC', '1105371870', 'LUIS', 'RENGIFO', '3026658879', NULL, 'CL 20 B  40 F SUR 10', 1),
(2, 'CC', '51651', 'felipe', 'galindo', '3045451', NULL, 'CL 21 B  41  F SUR 11', 2),
(3, 'CC', '5165154', 'MIGUEL', 'ARANGO', '3045451474', NULL, 'CL 20 B  40 F SUR 10', 14),
(4, 'CC', '111495', 'Lucia', 'Arango', '3107772898', NULL, 'trnasversal 17 #10-10 ', 4);

-- cargas
INSERT INTO `cargas` (`id_carga`, `id_encuestador`, `id_enrutador`, `fecha_visita`, `estado`, `creada_en`, `entregada_en`, `id_usuario_entrega`) VALUES
(1, 3, 1, '2026-11-09', 'en_campo', '2026-11-08 23:10:34', '2026-11-09 19:21:17', 2),
(2, 3, 1, '2026-11-10', 'programada', '2026-11-10 00:26:41', NULL, NULL),
(3, 3, 1, '2026-11-11', 'programada', '2026-11-11 05:32:02', NULL, NULL);

-- solicitudes
INSERT INTO `solicitudes` (`id_solicitud`, `radicado`, `id_solicitante`, `id_punto`, `id_punto_asignado`, `id_usuario_radica`, `id_carga`, `fecha_radicacion`, `fecha_limite`, `estado`) VALUES
(1, 'RAD-20261108-225405', 1, 1, 1, 2, 1, '2026-11-08 16:54:05', '2026-11-23', 'asignada'),
(2, 'RAD-20261108-235435', 2, 1, 1, 2, 1, '2026-11-08 17:54:35', '2026-11-23', 'asignada'),
(3, 'RAD-20261109-192503', 3, 1, 1, 2, 2, '2026-11-09 19:25:03', '2026-11-24', 'asignada'),
(4, 'RAD-20261110-235514', 4, 1, 1, 2, 3, '2026-11-10 23:55:14', '2026-11-25', 'asignada');

-- georreferenciacion
INSERT INTO `georreferenciacion` (`id_geo`, `id_solicitud`, `latitud`, `longitud`, `fuente`, `registrada_en`) VALUES
(1, 1, 3.24185311, -76.51501473, 'ventanilla', '2026-11-08 21:54:05'),
(2, 2, 3.24204660, -76.51540158, 'ventanilla', '2026-11-08 22:54:35'),
(3, 3, 3.24198744, -76.51539935, 'ventanilla', '2026-11-10 00:25:03'),
(4, 4, 3.26448046, -76.54508120, 'ventanilla', '2026-11-11 04:55:14');

-- =====================================================================
-- SECCIÓN B. Ejemplos de DML
-- =====================================================================

-- Copias temporales de apoyo (solo para esta sesión)
CREATE TEMPORARY TABLE tmp_solicitantes       LIKE solicitantes;
CREATE TEMPORARY TABLE tmp_georreferenciacion LIKE georreferenciacion;

-- ---------------------------------------------------------------------
-- PUNTO 1. LOGIN
-- Justificación: el login es lectura (SELECT). La contraseña NO se
-- compara en SQL: la aplicación usa password_verify() contra password_hash.
-- ---------------------------------------------------------------------
-- Consulta que ejecuta la aplicación ("?" = correo digitado):
--   SELECT u.id_usuario, u.nombres, u.apellidos, u.id_punto,
--          r.nombre AS rol, u.password_hash
--   FROM usuarios u JOIN roles r ON r.id_rol = u.id_rol
--   WHERE u.correo = ? AND u.estado = 'activo';

-- Demostración sin exponer el hash
SELECT u.id_usuario, CONCAT(u.nombres, ' ', u.apellidos) AS nombre,
       r.nombre AS rol, u.id_punto, u.estado
FROM usuarios u
JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo = 'ventanilla@test.com' AND u.estado = 'activo';

-- Verificación del requisito: un usuario activo por cada perfil
SELECT r.id_rol, r.nombre AS perfil, COUNT(u.id_usuario) AS usuarios_activos
FROM roles r
LEFT JOIN usuarios u ON u.id_rol = r.id_rol AND u.estado = 'activo'
GROUP BY r.id_rol, r.nombre
ORDER BY r.id_rol;

-- Calidad de datos del login: una contraseña bcrypt mide 60 caracteres.
-- Si aparece algún usuario aquí, no podrá iniciar sesión.
SELECT id_usuario, correo, CHAR_LENGTH(password_hash) AS largo_hash
FROM usuarios
WHERE CHAR_LENGTH(password_hash) <> 60;

-- Perfil y punto de atención de cada usuario de ventanilla (uno por punto)
SELECT u.id_usuario, CONCAT(u.nombres, ' ', u.apellidos) AS usuario,
       p.nombre AS punto_atencion
FROM usuarios u
JOIN roles r           ON r.id_rol   = u.id_rol
JOIN puntos_atencion p ON p.id_punto = u.id_punto
WHERE r.nombre = 'ventanilla'
ORDER BY u.id_punto;

-- ---------------------------------------------------------------------
-- PUNTO 2. REGISTRO DEL SOLICITANTE EN VENTANILLA
-- Campos: tipo y número de documento, nombres, apellidos, barrio, comuna,
-- celular (columna telefono) y dirección. La comuna se obtiene del barrio.
-- ---------------------------------------------------------------------
-- Lista desplegable barrio -> comuna -> zona
SELECT b.id_barrio, b.nombre AS barrio, c.id_comuna, c.nombre AS comuna, c.zona
FROM barrios b
JOIN comunas c ON c.id_comuna = b.id_comuna
ORDER BY c.id_comuna, b.nombre;

-- Corregimientos (zona rural) y barrios registrados en cada uno
SELECT c.id_comuna, c.nombre AS corregimiento, COUNT(b.id_barrio) AS barrios_registrados
FROM comunas c
LEFT JOIN barrios b ON b.id_comuna = c.id_comuna
WHERE c.zona = 'rural'
GROUP BY c.id_comuna, c.nombre
ORDER BY c.id_comuna;

-- Evitar duplicados: buscar al solicitante por documento
SELECT id_solicitante, nombres, apellidos
FROM solicitantes
WHERE tipo_documento = 'CC' AND numero_documento = '1105371870';

-- INSERT sobre la copia temporal (no consume AUTO_INCREMENT real)
INSERT INTO tmp_solicitantes
  (tipo_documento, numero_documento, nombres, apellidos, telefono, correo, direccion, id_barrio)
VALUES ('CC', '1234567890', 'ANA', 'PEREZ', '3001234567', NULL, 'CL 5 # 10-20', 1);

-- Registro completo con barrio, comuna y zona
SELECT t.tipo_documento, t.numero_documento, t.nombres, t.apellidos,
       t.telefono AS celular, t.direccion,
       b.nombre AS barrio, c.nombre AS comuna, c.zona
FROM tmp_solicitantes t
JOIN barrios b ON b.id_barrio = t.id_barrio
JOIN comunas c ON c.id_comuna = b.id_comuna;

-- Punto donde se radicó vs punto asignado (id_punto_asignado es opcional)
SELECT s.radicado, pr.nombre AS punto_radicacion, pa.nombre AS punto_asignado
FROM solicitudes s
JOIN puntos_atencion pr      ON pr.id_punto = s.id_punto
LEFT JOIN puntos_atencion pa ON pa.id_punto = s.id_punto_asignado
ORDER BY s.id_solicitud;

-- Seguimiento del plazo de 15 días con la vista existente
SELECT * FROM v_solicitudes_por_vencer WHERE dias_restantes <= 5 ORDER BY dias_restantes;

-- UPDATE de ejemplo, revertido (corrección de un dato del solicitante)
START TRANSACTION;
UPDATE solicitantes SET direccion = 'DIRECCION CORREGIDA' WHERE id_solicitante = 1;
SELECT id_solicitante, direccion FROM solicitantes WHERE id_solicitante = 1;
ROLLBACK;

-- ---------------------------------------------------------------------
-- PUNTO 3. GEORREFERENCIACIÓN
-- ---------------------------------------------------------------------
-- Solicitudes con su ubicación
SELECT s.radicado, g.latitud, g.longitud, g.fuente, g.registrada_en
FROM georreferenciacion g
JOIN solicitudes s ON s.id_solicitud = g.id_solicitud
ORDER BY g.registrada_en;

-- Solicitudes pendientes de georreferenciar
SELECT s.id_solicitud, s.radicado, s.estado
FROM solicitudes s
LEFT JOIN georreferenciacion g ON g.id_solicitud = s.id_solicitud
WHERE g.id_geo IS NULL AND s.estado <> 'cancelada';

-- Distancia en km entre la solicitud y su punto de atención (Haversine)
SELECT s.radicado, p.nombre AS punto_atencion,
       ROUND(6371 * ACOS(LEAST(1,
             COS(RADIANS(g.latitud)) * COS(RADIANS(p.latitud))
           * COS(RADIANS(p.longitud) - RADIANS(g.longitud))
           + SIN(RADIANS(g.latitud)) * SIN(RADIANS(p.latitud)))), 2) AS distancia_km
FROM georreferenciacion g
JOIN solicitudes s     ON s.id_solicitud = g.id_solicitud
JOIN puntos_atencion p ON p.id_punto     = s.id_punto
WHERE p.latitud IS NOT NULL AND p.longitud IS NOT NULL;

-- INSERT y UPDATE sobre la copia temporal
INSERT INTO tmp_georreferenciacion (id_solicitud, latitud, longitud, fuente)
VALUES (1, 3.45160000, -76.53200000, 'ventanilla');
UPDATE tmp_georreferenciacion
   SET latitud = 3.45170000, longitud = -76.53210000
 WHERE id_solicitud = 1;
SELECT * FROM tmp_georreferenciacion;

-- DELETE de ejemplo, revertido
START TRANSACTION;
DELETE FROM georreferenciacion WHERE id_solicitud = 3;
SELECT COUNT(*) AS registros_en_transaccion FROM georreferenciacion;
ROLLBACK;
SELECT COUNT(*) AS registros_reales FROM georreferenciacion;

DROP TEMPORARY TABLE tmp_georreferenciacion;
DROP TEMPORARY TABLE tmp_solicitantes;
