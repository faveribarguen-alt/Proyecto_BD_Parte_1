-- =====================================================================
-- encuestas_db_sql1  |  ARCHIVO 3 DE 4: DCL (Data Control Language)
-- Motor de referencia: MariaDB 10.4 (InnoDB)
--
-- Tablas sobre las que se otorgan permisos
--   roles, usuarios ................... Punto 1 (login)
--   puntos_atencion, comunas, barrios . Punto 2 (listas del formulario)
--   solicitantes, solicitudes ......... Punto 2 (registro y radicación)
--   georreferenciacion ................ Punto 3 (coordenadas)
--   v_solicitudes_por_vencer (vista) .. seguimiento del plazo
--
-- ESTADO ACTUAL: encuestas_db_sql1.sql NO contiene GRANT ni REVOKE (solo
-- DEFINER=`root`@`localhost` en la vista, L.425). Probablemente la
-- aplicación entra como root.
--
-- ESTE ARCHIVO ES UNA PROPUESTA. DCL no cambia datos ni estructura, pero
-- sí los privilegios del servidor, por eso todo está comentado salvo
-- SHOW GRANTS. Para aplicarla: quite los "-- ", cambie 'CAMBIAR_CLAVE'
-- y, si trabaja en una copia, sustituya encuestas_db por su nombre.
--
-- Justificación general: principio de mínimo privilegio. La tabla `roles`
-- controla el acceso dentro de la aplicación; el DCL agrega una segunda
-- barrera dentro del propio motor de base de datos.
-- =====================================================================

-- Consulta de solo lectura: privilegios de la cuenta actual
SHOW GRANTS FOR CURRENT_USER();

-- =====================================================================
-- PUNTO 1. LOGIN
-- Un rol de base de datos por cada perfil de la tabla `roles`, y una
-- cuenta exclusiva de autenticación: la única que puede leer password_hash.
-- =====================================================================
-- CREATE ROLE rol_administrador, rol_ventanilla, rol_encuestador,
--             rol_calidad, rol_enrutador;

-- CREATE USER 'app_login'@'localhost' IDENTIFIED BY 'CAMBIAR_CLAVE';
-- GRANT SELECT (id_usuario, id_rol, id_punto, nombres, apellidos,
--               correo, password_hash, estado)
--   ON encuestas_db.usuarios TO 'app_login'@'localhost';
-- GRANT SELECT ON encuestas_db.roles TO 'app_login'@'localhost';

-- Los demás perfiles ven usuarios SIN la columna password_hash
-- GRANT SELECT (id_usuario, id_rol, id_punto, nombres, apellidos, correo, estado)
--   ON encuestas_db.usuarios
--   TO rol_ventanilla, rol_encuestador, rol_calidad, rol_enrutador;

-- Administrador: control total solo sobre esta base
-- GRANT ALL PRIVILEGES ON encuestas_db.* TO rol_administrador;

-- =====================================================================
-- PUNTO 2. VENTANILLA: registro del solicitante
-- Puede consultar las listas, registrar y corregir solicitantes y radicar
-- solicitudes. NO puede eliminar solicitantes (trazabilidad ante demandas).
-- =====================================================================
-- GRANT SELECT ON encuestas_db.barrios          TO rol_ventanilla;
-- GRANT SELECT ON encuestas_db.comunas          TO rol_ventanilla;
-- GRANT SELECT ON encuestas_db.puntos_atencion  TO rol_ventanilla;
-- GRANT SELECT, INSERT, UPDATE ON encuestas_db.solicitantes TO rol_ventanilla;
-- GRANT SELECT, INSERT         ON encuestas_db.solicitudes  TO rol_ventanilla;
-- (solicitudes ahora trae id_punto_asignado; ventanilla puede informarlo al
--  insertar. Una vez radicada, solo debería cambiarlo quien asigna.)

-- Los demás perfiles solo leen solicitantes y solicitudes
-- GRANT SELECT ON encuestas_db.solicitantes TO rol_enrutador, rol_encuestador, rol_calidad;
-- GRANT SELECT ON encuestas_db.solicitudes  TO rol_enrutador, rol_encuestador, rol_calidad;

-- El enrutador consulta el seguimiento del plazo de 15 días por la vista
-- (la vista usa SQL SECURITY DEFINER, así que lee las tablas con los
-- privilegios de su definidor)
-- GRANT SELECT ON encuestas_db.v_solicitudes_por_vencer TO rol_enrutador;

-- Asignación de solicitudes: en los datos, las solicitudes 'asignada' tienen
-- historial registrado por el enrutador (usuario 1). Por eso se propone que
-- solo este rol modifique carga, estado y punto asignado (permiso por columna).
-- GRANT UPDATE (id_carga, estado, id_punto_asignado)
--   ON encuestas_db.solicitudes TO rol_enrutador;

-- =====================================================================
-- PUNTO 3. GEORREFERENCIACIÓN
-- Ventanilla inserta la georreferencia y solo puede corregir latitud y
-- longitud (permiso por columna); nunca borrarla ni reasignarla a otra
-- solicitud.
-- =====================================================================
-- GRANT SELECT, INSERT ON encuestas_db.georreferenciacion TO rol_ventanilla;
-- GRANT UPDATE (latitud, longitud) ON encuestas_db.georreferenciacion TO rol_ventanilla;
-- GRANT SELECT ON encuestas_db.georreferenciacion
--   TO rol_enrutador, rol_encuestador, rol_calidad;
-- El campo `fuente` (ventanilla, encuestador, enrutador) ya prevé que
-- otros perfiles también georreferencien; si se decide, añadir INSERT y
-- UPDATE (latitud, longitud) a esos roles.

-- =====================================================================
-- ASIGNACIÓN DE ROLES A CUENTAS Y REVOKE
-- =====================================================================
-- CREATE USER 'app_ventanilla'@'localhost' IDENTIFIED BY 'CAMBIAR_CLAVE';
-- GRANT rol_ventanilla TO 'app_ventanilla'@'localhost';
-- SET DEFAULT ROLE rol_ventanilla FOR 'app_ventanilla'@'localhost';

-- Retirar permisos cuando ya no se justifiquen
-- REVOKE UPDATE ON encuestas_db.solicitantes FROM rol_ventanilla;
-- REVOKE UPDATE (latitud, longitud) ON encuestas_db.georreferenciacion FROM rol_ventanilla;
-- REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'app_ventanilla'@'localhost';
-- DROP USER 'app_ventanilla'@'localhost';

-- Verificación de los permisos de un rol (solo lectura, tras aplicar)
-- SHOW GRANTS FOR rol_ventanilla;
-- =====================================================================
-- Fin del archivo
-- =====================================================================
