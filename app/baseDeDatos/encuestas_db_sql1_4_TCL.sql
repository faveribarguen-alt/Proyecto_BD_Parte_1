-- =====================================================================
-- encuestas_db_sql1  |  ARCHIVO 4 DE 4: TCL (Transaction Control Language)
-- Motor de referencia: MariaDB 10.4 (InnoDB)
--
-- Tablas que intervienen en las transacciones
--   usuarios ............ Punto 1 (activar / desactivar cuentas)
--   roles ............... Punto 1 (perfil del usuario)
--   solicitantes ........ Punto 2 (registro del solicitante)
--   solicitudes ......... Punto 2 y 3 (radicación con plazo de 15 días)
--   georreferenciacion .. Punto 3 (coordenadas de la solicitud)
--   barrios, comunas, puntos_atencion .. consultadas por las llaves foráneas
--                         (solicitudes tiene dos: id_punto e id_punto_asignado)
--
-- Requisito: tablas InnoDB (todas lo son: ENGINE=InnoDB en cada CREATE TABLE).
--
-- Reglas de este archivo
--   * Nada queda guardado: cada transacción termina en ROLLBACK.
--   * Los ejemplos con INSERT usan copias TEMPORARY para no consumir el
--     AUTO_INCREMENT real. En operación se usan las tablas reales y COMMIT
--     (ver la plantilla comentada al final).
--   * No hay ALTER dentro de transacciones (haría COMMIT implícito).
-- =====================================================================

-- ---------------------------------------------------------------------
-- TCL en el original (encuestas_db_sql1.sql):
--   START TRANSACTION ... L.11   abre la transacción de la importación
--   COMMIT .............. L.696  la confirma al terminar de crear todo
-- ---------------------------------------------------------------------
SELECT @@autocommit AS autocommit_actual;   -- 1 = cada sentencia se confirma sola

-- Copias temporales de apoyo
CREATE TEMPORARY TABLE tmp_solicitantes       LIKE solicitantes;
CREATE TEMPORARY TABLE tmp_solicitudes        LIKE solicitudes;
CREATE TEMPORARY TABLE tmp_georreferenciacion LIKE georreferenciacion;

-- =====================================================================
-- PUNTO 1. LOGIN: gestión de cuentas
-- Justificación: el login solo lee datos (SELECT) y NO necesita
-- transacción. Sí se justifica al desactivar un usuario: debe impedir su
-- ingreso y poder deshacerse si fue un error.
-- Sentencias: START TRANSACTION, ROLLBACK
-- =====================================================================
START TRANSACTION;

  UPDATE usuarios SET estado = 'inactivo' WHERE id_usuario = 3;

  -- Dentro de la transacción el encuestador ya no puede ingresar (0 filas)
  SELECT u.id_usuario, r.nombre AS rol
  FROM usuarios u
  JOIN roles r ON r.id_rol = u.id_rol
  WHERE u.correo = 'encuestador@test.com' AND u.estado = 'activo';

ROLLBACK;   -- en operación real: COMMIT

-- Tras el ROLLBACK la cuenta sigue activa
SELECT u.id_usuario, r.nombre AS rol, u.estado
FROM usuarios u
JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo = 'encuestador@test.com';

-- =====================================================================
-- PUNTO 2. VENTANILLA: registrar solicitante + solicitud
-- Justificación: un INSERT suelto ya es atómico en InnoDB. La transacción
-- se justifica porque atender a una persona guarda DOS cosas que no pueden
-- quedar a medias: el solicitante y su solicitud con el plazo de 15 días.
-- Sentencias: START TRANSACTION, ROLLBACK
-- =====================================================================
START TRANSACTION;

  INSERT INTO tmp_solicitantes
    (tipo_documento, numero_documento, nombres, apellidos, telefono, direccion, id_barrio)
  VALUES ('CC', '80012345', 'JUAN', 'GOMEZ', '3109876543', 'KR 15 # 20-30', 14);
  SET @id_solicitante = LAST_INSERT_ID();

  -- Radica el usuario de ventanilla 29 (Andrea Quintero) en el punto 2
  INSERT INTO tmp_solicitudes
    (radicado, id_solicitante, id_punto, id_usuario_radica, fecha_limite)
  VALUES
    (CONCAT('RAD-', DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s')),
     @id_solicitante, 2, 29, DATE_ADD(CURDATE(), INTERVAL 15 DAY));

  SELECT s.radicado, s.estado, s.fecha_limite,
         CONCAT(t.nombres, ' ', t.apellidos) AS solicitante
  FROM tmp_solicitudes s
  JOIN tmp_solicitantes t ON t.id_solicitante = s.id_solicitante
  WHERE s.id_solicitante = @id_solicitante;

ROLLBACK;   -- en operación real: COMMIT

-- =====================================================================
-- PUNTO 3. GEORREFERENCIACIÓN con SAVEPOINT
-- Justificación: georreferenciar es opcional respecto a radicar. Con un
-- SAVEPOINT, si la georreferencia falla, se deshace solo ese paso y la
-- solicitud se conserva para georreferenciarla después.
-- Sentencias: START TRANSACTION, SAVEPOINT, ROLLBACK TO SAVEPOINT,
--             RELEASE SAVEPOINT, ROLLBACK
-- =====================================================================
START TRANSACTION;

  INSERT INTO tmp_solicitantes
    (tipo_documento, numero_documento, nombres, apellidos, telefono, direccion, id_barrio)
  VALUES ('CC', '80012346', 'MARIA', 'LOPEZ', '3201112233', 'CL 9 # 4-15', 2);
  SET @id_sol = LAST_INSERT_ID();

  -- id_punto_asignado es opcional (nuevo en esta versión); aquí se deja asignado
  INSERT INTO tmp_solicitudes
    (radicado, id_solicitante, id_punto, id_punto_asignado, id_usuario_radica, fecha_limite)
  VALUES
    (CONCAT('RAD-', DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s'), '-B'),
     @id_sol, 3, 3, 30, DATE_ADD(CURDATE(), INTERVAL 15 DAY));
  SET @id_solic = LAST_INSERT_ID();

  SAVEPOINT sp_solicitud_creada;

  INSERT INTO tmp_georreferenciacion (id_solicitud, latitud, longitud, fuente)
  VALUES (@id_solic, 3.43000000, -76.54000000, 'ventanilla');

  -- Se simula que la georreferencia falló: se deshace solo ese paso
  ROLLBACK TO SAVEPOINT sp_solicitud_creada;

  SELECT
    (SELECT COUNT(*) FROM tmp_solicitudes        WHERE id_solicitud = @id_solic) AS solicitud_conservada,
    (SELECT COUNT(*) FROM tmp_georreferenciacion WHERE id_solicitud = @id_solic) AS georreferencia_deshecha;

  RELEASE SAVEPOINT sp_solicitud_creada;

ROLLBACK;   -- en operación real: COMMIT

-- Limpieza de las tablas temporales
DROP TEMPORARY TABLE tmp_georreferenciacion;
DROP TEMPORARY TABLE tmp_solicitudes;
DROP TEMPORARY TABLE tmp_solicitantes;

-- =====================================================================
-- PLANTILLA DE OPERACIÓN REAL (comentada): radicación completa
-- Usa las tablas reales y termina en COMMIT. La aplicación calcula el
-- radicado y las coordenadas; los valores van como parámetros.
-- =====================================================================
-- START TRANSACTION;
--   INSERT INTO solicitantes
--     (tipo_documento, numero_documento, nombres, apellidos, telefono, correo, direccion, id_barrio)
--   VALUES (?, ?, ?, ?, ?, ?, ?, ?);
--   SET @id_solicitante = LAST_INSERT_ID();
--
--   INSERT INTO solicitudes
--     (radicado, id_solicitante, id_punto, id_punto_asignado, id_usuario_radica, fecha_limite)
--   VALUES (?, @id_solicitante, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 15 DAY));
--   -- id_punto_asignado puede ir NULL; la FK es ON DELETE SET NULL
--   SET @id_solicitud = LAST_INSERT_ID();
--
--   SAVEPOINT sp_solicitud_creada;
--   INSERT INTO georreferenciacion (id_solicitud, latitud, longitud, fuente)
--   VALUES (@id_solicitud, ?, ?, 'ventanilla');
--   -- Si esta última sentencia falla: ROLLBACK TO SAVEPOINT sp_solicitud_creada;
-- COMMIT;
-- =====================================================================
-- Fin del archivo
-- =====================================================================
