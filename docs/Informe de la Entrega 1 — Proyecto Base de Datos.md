# **Informe de la Entrega 1 — Proyecto Base de Datos**

Universidad del Valle · Curso de Base de Datos · Entrega 1 (12 de noviembre de 2026\)

Integrantes: MIGUEL ANGEL ROMERO ARANGO (2565086) **\|** LUIS FELIPE GALINDO RENGIFO (2564956) **\|** FAVER ANDRES IBARÜEN (2564955) 

## **1\. Contexto y alcance** 

Una empresa encuestadora de Cali recibe solicitudes de encuesta en sus puntos de atención y debe responderlas en un máximo de 15 días calendario. Hoy el proceso es manual: cerca del 30% de las encuestas se hacen fuera de plazo y los formatos impresos tienen errores frecuentes (barrios o comunas mal asignados, fechas de nacimiento inconsistentes, preguntas sin responder). El proyecto construye una aplicación web que automatiza la atención, el levantamiento y la revisión de las encuestas.

La Entrega 1 cubre los puntos 1 a 4 del enunciado:

* **Punto 1:** login que valida los perfiles administrador, ventanilla, encuestador, calidad y enrutador.  
* **Punto 2:** registro de solicitudes en ventanilla (tipo y número de documento, nombres, apellidos, barrio, comuna, celular y dirección).  
* **Punto 3:** georreferenciación de cada solicitud desde el perfil ventanilla.  
* **Punto 4:** bitácora de las solicitudes del día, de todos los puntos de atención, para el perfil enrutador.

Los puntos 5 a 9 (puntos de atención fijos, hojas de ruta, entrega de hojas, informes PDF y dashboard) corresponden a la Entrega 2\.

## **2\. Metodología Scrum**

\[COMPLETAR: ajustar esta sección a las reuniones, fechas y reparto reales del equipo; lo que está escrito es la estructura que exige el enunciado.\]

### **2.1 Roles**

| Rol Scrum | Responsabilidad | Integrante |
| :---- | :---- | :---- |
| Product Owner | Define y prioriza los requerimientos según el enunciado del curso | John Robert Causimanse |
| Scrum Master | Facilita el proceso y resuelve impedimentos del equipo | Todo el equipo |
| Equipo de desarrollo | diseño y script de la base de datos | Todo el equipo |
| Equipo de desarrollo | backend (login, ventanilla, georreferenciación, bitácora y control de acceso) | Todo el equipo |
| Equipo de desarrollo | Tester (Realiza pruebas, verifica y valida que una aplicación o sistema funcione correctamente ) | Todos el equipo |

### 

### **2.2 Fases y eventos**

| Fase | Aplicación en el proyecto |
| :---- | :---- |
| Product Backlog | Lista priorizada de requerimientos del enunciado, escrita como historias de usuario (sección 3\) |
| Sprint Planning | Al inicio de cada sprint se eligen las historias a desarrollar y se reparten entre los integrantes |
| Sprint | Desarrollo en ramas de Git, una por integrante; al terminar un módulo probado se une a la rama principal |
| Daily Scrum | El equipo se comunicaba por WhatsApp y meet para compartir los avances, comentar las dificultades y coordinar las tareas pendientes. Se realizaba un seguimiento frecuente durante el desarrollo del proyecto para verificar que cada integrante cumpliera con sus actividades.  |
| Sprint Review | Demostración de lo que funciona: login, ventanilla, georreferenciación y bitácora |
| Sprint Retrospective | Al finalizar cada etapa, el equipo revisaba el trabajo realizado, identificaba los errores y las dificultades presentadas y acordaba mejoras. Se decidió mantener una mejor comunicación, organizar las tareas según las responsabilidades de cada integrante y probar cada módulo antes de continuar con el siguiente.  |

### 

### **2.3 Sprints de la Entrega 1**

| Sprint | Objetivo | Historias | Fechas |
| :---- | :---- | :---- | :---- |
| 1 | Análisis y diseño: backlog, modelo de base de datos y estructura MVC | — | 7/11/2026 |
| 2 | Autenticación, control de acceso y ventanilla | HU-01, HU-02, HU-03 | 9/11/2026 |
| 3 | Georreferenciación, bitácora, pruebas e informe | HU-04, HU-05 | 10/11/2026 |

### 

### **2.4 Definición de terminado**

Una historia se considera terminada cuando: cumple sus criterios de aceptación, respeta la arquitectura MVC, usa consultas preparadas, fue probada en el navegador con los usuarios de prueba y está unida a la rama principal.

## **3\. Requerimientos e historias de usuario**

### **3.1 Requerimientos funcionales**

| Código | Requerimiento | Punto del enunciado |
| :---- | :---- | :---- |
| RF-01 | Iniciar sesión con correo y contraseña para los 5 perfiles | 1 |
| RF-02 | Redirigir a cada perfil a su pantalla y bloquear las pantallas de otros perfiles | 1 |
| RF-03 | Registrar solicitudes con tipo y número de documento, nombres, apellidos, barrio, comuna, celular y dirección | 2 |
| RF-04 | Elegir el barrio desde un catálogo que ya trae su comuna (división sectorial de Cali) | 2 |
| RF-05 | Georreferenciar cada solicitud con latitud y longitud | 3 |
| RF-06 | Mostrar al enrutador la bitácora de las solicitudes del día de todos los puntos de atención | 4 |

### 

### **3.2 Requerimientos no funcionales**

| Código | Requerimiento | Cómo se cumple |
| :---- | :---- | :---- |
| RNF-01 | Seguridad | Contraseñas con password\_hash y password\_verify, consultas preparadas con PDO, sesión renovada al ingresar, control de acceso por rol y escape de datos en pantalla |
| RNF-02 | Integridad de los datos | Llaves foráneas, restricciones únicas y transacciones en el registro de solicitudes |
| RNF-03 | Usabilidad (interfaz tipo RIA) | Captura de coordenadas y mapa interactivo sin recargar la página; barrio elegido de una lista para evitar errores de digitación |
| RNF-04 | Mantenibilidad | Arquitectura MVC, un modelo por entidad y hojas de estilo separadas de las vistas |
| RNF-05 | Portabilidad | Se ejecuta en XAMPP con PHP 8.2 y MariaDB 10.4, compatible con MySQL |
| RNF-06 | Rendimiento | Índices sobre estado, fecha límite y fecha de radicación de las solicitudes |

### **3.3 Historias de usuario**

**HU-01 — Iniciar sesión** (prioridad alta) Como usuario del sistema, quiero ingresar con mi correo y contraseña para acceder a las funciones de mi perfil.

* Con las credenciales correctas de un usuario activo se inicia la sesión.  
* Con credenciales incorrectas se muestra “Usuario o contraseña incorrectos.”  
* La contraseña nunca se guarda en texto plano.

**HU-02 — Acceso según perfil** (prioridad alta) Como administrador del sistema, quiero que cada perfil solo vea sus pantallas para proteger la información.

* Después de ingresar, cada perfil llega a su pantalla inicial.  
* Si un perfil abre la dirección de otro, ve “No tienes permiso para ver esta página.”  
* Sin sesión iniciada, cualquier pantalla redirige al login.

**HU-03 — Registrar solicitud** (prioridad alta) Como usuario de ventanilla, quiero registrar los datos del interesado en una encuesta para iniciar el trámite.

* El formulario pide tipo y número de documento, nombres, apellidos, celular, dirección y barrio (con su comuna).  
* El barrio se elige de una lista, no se escribe.  
* La solicitud queda radicada en el punto de atención del usuario que la registra, con fecha límite de 15 días.  
* Los datos se guardan completos o no se guardan.

**HU-04 — Georreferenciar solicitud** (prioridad alta) Como usuario de ventanilla, quiero capturar la ubicación de cada solicitud para que la visita se planee con datos exactos.

* Un botón captura latitud y longitud con el navegador.  
* Los campos de coordenadas no se pueden editar a mano.  
* Las coordenadas quedan guardadas junto con la solicitud.

**HU-05 — Bitácora del día** (prioridad alta) Como enrutador, quiero ver todas las solicitudes generadas en el día en todos los puntos de atención para planear las cargas de trabajo.

* Por defecto se muestra el día de hoy y se puede consultar otro día.  
* Cada fila muestra radicado, fecha, punto de atención, ciudadano, dirección, barrio, comuna, estado y enlace al mapa.  
* Solo el perfil enrutador puede verla.

## 

## **4\. Tecnologías utilizadas**

| Tecnología | Uso | Justificación |
| :---- | :---- | :---- |
| PHP 8.2 | Lógica del servidor (controladores y modelos) | Lenguaje permitido por el enunciado y soportado por XAMPP |
| MariaDB 10.4 (compatible con MySQL) | Gestor de base de datos | Relacional, con transacciones y llaves foráneas |
| PDO | Conexión y consultas | Consultas preparadas que previenen la inyección SQL |
| HTML5, CSS3 y JavaScript | Interfaz | Interfaz tipo RIA sin recargar la página en las acciones de ubicación |
| API de Geolocalización del navegador | Captura de coordenadas | No requiere servicios de pago |
| Leaflet 1.9 y OpenStreetMap | Mapa de puntos de atención | Mapa interactivo gratuito |
| XAMPP y phpMyAdmin 5.2 | Entorno local y administración de la base de datos | Entorno uniforme para todo el equipo |
| Git y GitHub | Control de versiones | Una rama por integrante e integración a la rama principal |
| Visual Studio Code | Editor de código | Herramienta de desarrollo del equipo |

## 

## **5\. Arquitectura MVC**

La aplicación usa el patrón Modelo-Vista-Controlador con un único punto de entrada (public/index.php).

* **Modelos** (app/models): conexión y consultas SQL con PDO. Ejemplos: UsuarioModel, SolicitudModel, BitacoraModel, PuntoModel.

* **Vistas** (app/views): solo presentación en HTML. No contienen consultas SQL.

* **Controladores** (app/controllers): reciben la petición, validan el permiso del perfil, piden los datos al modelo y cargan la vista. Ejemplos: AuthController, VentanillaController, EnrutadorController.

* **Configuración** (app/config): conexión a la base de datos y funciones de sesión y permisos.

* **Recursos públicos** (public/assets): hojas de estilo y scripts.

Flujo de una petición: el navegador llama a index.php con una acción; el enrutador del archivo elige el controlador; el controlador usa el modelo para leer o guardar y muestra la vista con los datos.

##  **6\. Diseño de la base de datos**

La base se llama encuestas\_db y tiene 13 tablas y una vista. En la Entrega 1 se usan directamente roles, usuarios, puntos\_atencion, comunas, barrios, solicitantes, solicitudes y georreferenciacion; el resto queda preparado para la Entrega 2\.

### **6.1 Modelo conceptual**

Entidades principales y sus relaciones:

| Entidad | Descripción |
| :---- | :---- |
| Rol | Los 5 perfiles del sistema |
| Usuario | Empleado que ingresa al sistema con un rol y un punto de atención |
| Punto de atención | Sede donde se radican solicitudes, una de ellas principal |
| Comuna | Zona de la ciudad, urbana o rural (incluye los corregimientos) |
| Barrio | Sector que pertenece a una comuna |
| Solicitante | Persona interesada en la encuesta |
| Solicitud | Trámite de encuesta radicado en un punto, con fecha límite y estado |
| Georreferenciación | Ubicación geográfica de una solicitud |
| Carga (hoja de ruta) | Grupo de solicitudes asignado a un encuestador |
| Encuesta, menor y revisión | Resultado de la visita y su revisión por calidad |
| Historial de estados | Registro de los cambios de estado de cada solicitud |

**Diagrama de entidad-relación de alto nivel:**

[https\://miro.com/app/board/uXjVEOjEfx8=/?moveToWidgets=AQA64\_32gZGFgIAwAQEBAQEBAQEBAQEBAQEBAQEBAQQBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQIBAQEBAQEBAQEBAQHGmogB](https://miro.com/app/board/uXjVEOjEfx8=/?moveToWidgets=AQA64_32gZGFgIAwAQEBAQEBAQEBAQEBAQEBAQEBAQQBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQIBAQEBAQEBAQEBAQHGmogB)

### **6.2 Modelo lógico**

| Relación | Cardinalidad |
| :---- | :---- |
| Rol – Usuario | 1 a N |
| Punto de atención – Usuario | 1 a N (un usuario trabaja en un punto) |
| Comuna – Barrio | 1 a N |
| Barrio – Solicitante | 1 a N |
| Solicitante – Solicitud | 1 a N |
| Punto de atención – Solicitud | 1 a N (punto donde se radicó) |
| Usuario – Solicitud | 1 a N (usuario de ventanilla que la radicó) |
| Solicitud – Georreferenciación | 1 a 0..1 (llave única sobre la solicitud) |
| Carga – Solicitud | 1 a N (la solicitud queda sin carga hasta que se asigna) |
| Usuario – Carga | 1 a N, como encuestador y como enrutador |
| Solicitud – Encuesta | 1 a 0..1 |
| Encuesta – Menor y Encuesta – Revisión | 1 a N |
| Solicitud – Historial de estados | 1 a N |

**Diagrama de entidad-relación lógico con llaves primarias y foráneas:**

[https\://miro.com/app/board/uXjVEOjEfx8=/?moveToWidgets=AQADlPmRgZGFgIAwARI](https://miro.com/app/board/uXjVEOjEfx8=/?moveToWidgets=AQADlPmRgZGFgIAwARI)

### **6.3 Normalización**

* **Primera forma normal:** todos los campos guardan un solo valor (el barrio, la comuna y el teléfono van en columnas propias, sin listas).  
* **Segunda forma normal:** los datos del ciudadano están en solicitantes y los del trámite en solicitudes, así que ningún dato depende de solo una parte de la llave.  
* **Tercera forma normal:** la comuna no se guarda en la solicitud ni en el solicitante: se obtiene a través del barrio, y la zona (urbana o rural) a través de la comuna. Esto evita que un barrio quede asociado a una comuna equivocada, uno de los errores del formato impreso. Los roles y los puntos de atención son catálogos independientes.


### **6.4 Modelo físico**

Todas las tablas usan el motor InnoDB y el juego de caracteres utf8mb4.

| Tabla | Columnas principales | Llaves y restricciones |
| :---- | :---- | :---- |
| roles | id\_rol, nombre, descripcion | PK id\_rol; nombre único |
| puntos\_atencion | id\_punto, nombre, direccion, es\_principal, activo | PK id\_punto |
| usuarios | id\_usuario, id\_rol, id\_punto, nombres, apellidos, correo, password\_hash, estado, creado\_en | PK id\_usuario; correo único; FK a roles y a puntos\_atencion; estado activo o inactivo |
| comunas | id\_comuna, nombre, zona | PK id\_comuna; zona urbana o rural |
| barrios | id\_barrio, id\_comuna, nombre, ciudad | PK id\_barrio; FK a comunas; único por comuna, nombre y ciudad |
| solicitantes | id\_solicitante, tipo\_documento, numero\_documento, nombres, apellidos, telefono, correo, direccion, id\_barrio | PK id\_solicitante; FK a barrios |
| solicitudes | id\_solicitud, radicado, id\_solicitante, id\_punto, id\_usuario\_radica, id\_carga, fecha\_radicacion, fecha\_limite, estado | PK id\_solicitud; radicado único; FK a solicitantes, puntos\_atencion, usuarios y cargas; índices por estado y fecha límite y por fecha de radicación |
| georreferenciacion | id\_geo, id\_solicitud, latitud, longitud, fuente, registrada\_en | PK id\_geo; id\_solicitud único; FK a solicitudes con borrado en cascada |
| cargas | id\_carga, id\_encuestador, id\_enrutador, fecha\_visita, estado, creada\_en | PK id\_carga; FK a usuarios (encuestador y enrutador) |
| historial\_estados | id\_historial, id\_solicitud, estado, id\_usuario, cambiado\_en | PK id\_historial; FK a solicitudes y usuarios |
| encuestas | id\_encuesta, id\_solicitud, id\_encuestador, fecha\_visita, observaciones | PK id\_encuesta; id\_solicitud único; FK a solicitudes y usuarios |
| menores | id\_menor, id\_encuesta, nombre, fecha\_nacimiento, edad\_declarada | PK id\_menor; FK a encuestas con borrado en cascada |
| revisiones | id\_revision, id\_encuesta, id\_usuario\_calidad, resultado, observaciones, revisada\_en | PK id\_revision; FK a encuestas y usuarios |

La vista **v\_solicitudes\_por\_vencer** calcula los días que le quedan a cada solicitud abierta y su zona; se usará en el dashboard de la Entrega 2\.

El campo estado de solicitudes admite: radicada, asignada, visitada, en\_revision, apto, no\_apto, devuelta y cancelada.

## **7\. Sentencias DDL, DML, DCL y TCL por requerimiento**

### **7.1 Punto 1 — Login**

**DDL.** Se crean las tablas de perfiles y usuarios con llaves y restricciones.

CREATE TABLE roles (  
  id\_rol TINYINT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,  
  nombre VARCHAR(30) NOT NULL UNIQUE,  
  descripcion VARCHAR(120)  
);

CREATE TABLE usuarios (  
  id\_usuario INT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,  
  id\_rol TINYINT UNSIGNED NOT NULL,  
  id\_punto SMALLINT UNSIGNED,  
  nombres VARCHAR(80) NOT NULL,  
  apellidos VARCHAR(80) NOT NULL,  
  correo VARCHAR(120) NOT NULL UNIQUE,  
  password\_hash VARCHAR(255) NOT NULL,  
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',  
  creado\_en TIMESTAMP NOT NULL DEFAULT CURRENT\_TIMESTAMP,  
  FOREIGN KEY (id\_rol) REFERENCES roles (id\_rol),  
  FOREIGN KEY (id\_punto) REFERENCES puntos\_atencion (id\_punto)  
);  
Justificación: la llave foránea garantiza que todo usuario tenga uno de los 5 perfiles existentes; el correo único evita cuentas duplicadas y la columna password\_hash guarda solo el hash de la contraseña.

**DML.** Se insertan los perfiles y los usuarios de prueba (con la contraseña ya convertida en hash) y se consulta al usuario en cada ingreso con una consulta preparada.

INSERT INTO roles (nombre, descripcion) VALUES  
('enrutador','Perfil que ve la bitacora'), ('ventanilla','Perfil que registra solicitudes'),  
('encuestador','Perfil que realiza visitas en campo'), ('calidad','Perfil que revisa y aprueba encuestas'),  
('administrador','Administrador general del sistema');

SELECT id\_usuario, nombres, apellidos, password\_hash, id\_rol, id\_punto  
FROM usuarios  
WHERE correo \= :correo AND estado \= 'activo'  
LIMIT 1;  
Justificación: el parámetro :correo se vincula con PDO, lo que impide la inyección SQL; solo ingresan usuarios activos y la contraseña se compara en PHP con password\_verify.

**DCL.** Se crea un usuario de base de datos exclusivo para la aplicación, con permisos mínimos.

CREATE USER 'encuestas\_app'@'localhost' IDENTIFIED BY 'CLAVE\_SEGURA';  
GRANT SELECT, INSERT, UPDATE ON encuestas\_db.\* TO 'encuestas\_app'@'localhost';  
FLUSH PRIVILEGES;  
Justificación: la aplicación no se conecta como root. Con el principio de mínimo privilegio, si alguien lograra atacar la aplicación, no podría borrar datos ni modificar la estructura. La sentencia REVOKE permite retirar un permiso cuando ya no se necesite.

**TCL.** No se usa en el login. Justificación: el ingreso es una sola consulta de lectura que no modifica datos, así que no hay nada que confirmar o deshacer.

### **7.2 Punto 2 — Registro en ventanilla**

**DDL.** Tablas de la división sectorial y de la solicitud, con llaves foráneas que impiden barrios inexistentes.

CREATE TABLE barrios (  
  id\_barrio INT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,  
  id\_comuna SMALLINT UNSIGNED NOT NULL,  
  nombre VARCHAR(100) NOT NULL,  
  ciudad VARCHAR(80) NOT NULL DEFAULT 'Principal',  
  UNIQUE KEY uq\_barrio (id\_comuna, nombre, ciudad)  
);

ALTER TABLE barrios  
  ADD CONSTRAINT fk\_barrio\_comuna FOREIGN KEY (id\_comuna) REFERENCES comunas (id\_comuna);

ALTER TABLE solicitudes  
  ADD CONSTRAINT fk\_sol\_solicitante FOREIGN KEY (id\_solicitante) REFERENCES solicitantes (id\_solicitante),  
  ADD CONSTRAINT fk\_sol\_punto FOREIGN KEY (id\_punto) REFERENCES puntos\_atencion (id\_punto);  
Justificación: ALTER TABLE agrega las restricciones una vez creadas las tablas. Como el barrio se guarda por su identificador, la comuna siempre es la correcta, lo que elimina el error de barrios mal asignados.

**DML.** Se listan los barrios con su comuna para el formulario y se insertan el solicitante y la solicitud.

SELECT b.id\_barrio, b.nombre, c.nombre AS comuna  
FROM barrios b INNER JOIN comunas c ON b.id\_comuna \= c.id\_comuna  
ORDER BY c.nombre, b.nombre;

INSERT INTO solicitantes (tipo\_documento, numero\_documento, nombres, apellidos, telefono, direccion, id\_barrio)  
VALUES (:tipo\_doc, :num\_doc, :nombres, :apellidos, :telefono, :direccion, :id\_barrio);

INSERT INTO solicitudes (radicado, id\_solicitante, id\_punto, id\_usuario\_radica, fecha\_limite, estado)  
VALUES (:radicado, :id\_solicitante, :id\_punto, :id\_usuario, :fecha\_limite, 'radicada');  
Justificación: todos los valores entran como parámetros de PDO. El punto de atención sale del usuario que inició sesión y la fecha límite se calcula a 15 días de la radicación.

**DCL.** Se usa el mismo usuario encuestas\_app: el permiso INSERT le basta para registrar solicitudes y no necesita DELETE ni permisos de estructura.

**TCL.** El registro es una transacción:

START TRANSACTION;  
  INSERT INTO solicitantes (...) VALUES (...);  
  INSERT INTO solicitudes (...) VALUES (...);  
  INSERT INTO georreferenciacion (...) VALUES (...);  
COMMIT;   \-- si todo salió bien  
\-- ROLLBACK;  si algún INSERT falla  
Justificación: sin la transacción podría quedar un solicitante sin solicitud, o una solicitud sin ubicación. En PHP corresponde a beginTransaction, commit y rollBack de PDO.

### **7.3 Punto 3 — Georreferenciación**

**DDL.**

CREATE TABLE georreferenciacion (  
  id\_geo INT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,  
  id\_solicitud INT UNSIGNED NOT NULL UNIQUE,  
  latitud DECIMAL(10,8) NOT NULL,  
  longitud DECIMAL(11,8) NOT NULL,  
  fuente ENUM('ventanilla','encuestador','enrutador') NOT NULL DEFAULT 'ventanilla',  
  registrada\_en TIMESTAMP NOT NULL DEFAULT CURRENT\_TIMESTAMP,  
  FOREIGN KEY (id\_solicitud) REFERENCES solicitudes (id\_solicitud) ON DELETE CASCADE  
);  
Justificación: DECIMAL guarda las coordenadas sin pérdida de precisión; la llave única permite una ubicación por solicitud; el campo fuente deja claro quién la registró; y el borrado en cascada elimina la ubicación si se elimina la solicitud.

**DML.**

INSERT INTO georreferenciacion (id\_solicitud, latitud, longitud, fuente)  
VALUES (:id\_solicitud, :latitud, :longitud, 'ventanilla');

**DCL.** El permiso INSERT de encuestas\_app cubre esta operación.

**TCL.** Esta inserción es el tercer paso de la transacción del registro (sección 7.2): si falla, se deshace también la solicitud, por lo que nunca queda una solicitud sin su ubicación.

### **7.4 Punto 4 — Bitácora del enrutador**

**DDL.** Un índice acelera la búsqueda de las solicitudes de un día.

ALTER TABLE solicitudes ADD KEY idx\_fecha\_radicacion (fecha\_radicacion);  
Justificación: la bitácora siempre filtra por fecha de radicación; con el índice la consulta no recorre toda la tabla a medida que crecen las solicitudes.

**DML.**

SELECT s.radicado, s.fecha\_radicacion, p.nombre AS punto, so.nombres, so.apellidos,  
       so.direccion, b.nombre AS barrio, c.nombre AS comuna, s.estado, g.latitud, g.longitud  
FROM solicitudes s  
INNER JOIN solicitantes so ON s.id\_solicitante \= so.id\_solicitante  
INNER JOIN barrios b ON so.id\_barrio \= b.id\_barrio  
INNER JOIN comunas c ON b.id\_comuna \= c.id\_comuna  
INNER JOIN puntos\_atencion p ON s.id\_punto \= p.id\_punto  
LEFT JOIN georreferenciacion g ON s.id\_solicitud \= g.id\_solicitud  
WHERE s.fecha\_radicacion \>= :inicio AND s.fecha\_radicacion \< DATE\_ADD(:fin, INTERVAL 1 DAY)  
ORDER BY s.fecha\_radicacion DESC;  
Justificación: los INNER JOIN reúnen ciudadano, barrio, comuna y punto; el LEFT JOIN mantiene visibles las solicitudes que aún no tengan coordenadas.

**DCL.** Esta pantalla solo lee datos: le basta el permiso SELECT. El acceso del perfil enrutador se controla además en la aplicación, con el rol de la sesión.

**TCL.** No se usa. Justificación: es una consulta de solo lectura que no cambia datos.

## **8\. Pruebas**

### **8.1 Estrategia**

Se hicieron pruebas funcionales de caja negra en el navegador con los usuarios de prueba (contraseña de desarrollo) y se verificaron los datos guardados en phpMyAdmin. Cada caso indica la entrada, el resultado esperado y el resultado obtenido.

### **8.2 Pruebas unitarias**

| Id | Módulo | Caso | Resultado esperado | Resultado obtenido |
| :---- | :---- | :---- | :---- | :---- |
| P-01 | Login | Ingresar como ventanilla con credenciales correctas | Entra al formulario de registro | Aprobada |
| P-02 | Login | Ingresar con contraseña incorrecta | Mensaje “Usuario o contraseña incorrectos.” | Aprobada |
| P-03 | Login | Ingresar con cada uno de los 5 perfiles | Cada perfil llega a su pantalla sin volver al login | Aprobada |
| P-04 | Acceso | Ventanilla abre la bitácora del enrutador | Mensaje “No tienes permiso para ver esta página.” | Aprobada |
| P-05 | Acceso | Abrir una pantalla interna sin sesión | Redirige al login | Aprobada |
| P-06 | Ventanilla | Registrar una solicitud completa | Se guardan solicitante, solicitud y ubicación; el punto es el del usuario | Aprobada |
| P-07 | Ventanilla | Enviar el formulario con campos vacíos | El navegador no deja enviar | Aprobada |
| P-08 | Ventanilla | Forzar un error en el último INSERT (latitud inválida) | No queda solicitante ni solicitud (rollback) | Inconclusa |
| P-09 | Georreferenciación | Pulsar “Capturar Coordenadas Actuales” | Se llenan latitud y longitud | Aprobada |
| P-10 | Georreferenciación | Negar el permiso de ubicación | Aviso de error y campos vacíos | Reprobada |
| P-11 | Puntos de atención | Abrir el mapa de puntos | Se ven 8 marcadores | Aprobada |
| P-12 | Bitácora | Abrir la bitácora como enrutador | Solo solicitudes de hoy, con punto, barrio y comuna | Aprobada |
| P-13 | Bitácora | Elegir otra fecha | Solo solicitudes de ese día | Aprobada |
| P-14 | Seguridad | Escribir ' OR '1'='1 como correo | Acceso rechazado | Aprobada |
| P-15 | Seguridad | Revisar la columna password\_hash en la base | Contiene un hash, no la contraseña | Aprobada |

### **8.3 Datos de las pruebas**

| Prueba | Datos sugeridos | Verificación |
| :---- | :---- | :---- |
| Inicio de sesión | Usuario activo de prueba con rol Ventanilla. | Debe permitir el acceso con la contraseña correcta. |
| Contraseña incorrecta | Correo existente y contraseña diferente. | Debe rechazar el acceso. |
| Registro de solicitud | Datos ficticios completos de un solicitante. | Debe guardar solicitante, solicitud y ubicación según el flujo implementado. |
| Fecha límite | Fecha de radicación: 01/11/2026. | La fecha esperada, sumando 15 días calendario, es 16/11/2026. |
| Capacidad urbana | 10 solicitudes asignadas y luego intentar una adicional. | Debe respetar el máximo de 10 solicitudes urbanas. |
| Capacidad rural | 5 solicitudes asignadas y luego intentar una adicional. | Debe respetar el máximo de 5 solicitudes rurales. |

## **9\. Conclusion**

El desarrollo de ENCUESTAS360 permitió construir una solución web orientada a mejorar la organización y seguimiento de las solicitudes relacionadas con los procesos de encuestas.

La implementación de una arquitectura MVC facilitó la separación de responsabilidades entre la lógica del sistema, la presentación y el acceso a los datos.

La utilización de MariaDB permitió estructurar la información mediante tablas relacionadas, claves primarias y claves foráneas, favoreciendo la integridad de los datos.

La incorporación de herramientas de georreferenciación y distribución de visitas representa un avance importante para organizar las actividades de campo y aprovechar la ubicación de los puntos de atención.

Las pruebas funcionales realizadas permitieron verificar los componentes implementados y corregir dificultades encontradas durante el desarrollo.

Finalmente, el proyecto continuará evolucionando mediante la implementación de los módulos pendientes y la ampliación de las pruebas automatizadas.

## **10\. Trabajo Pendiente**

Para las siguientes etapas se contempla:

* Completar el panel del Encuestador.  
* Implementar el registro de resultados de las visitas.  
* Desarrollar las funcionalidades del perfil de Calidad.  
* Desarrollar el panel de Administración.  
* Completar las pruebas unitarias automatizadas.  
* Complementar la documentación técnica.  
* Elaborar el archivo readme.txt con las instrucciones de instalación y ejecución.  
* Realizar las pruebas de integración y aceptación correspondientes.

## **11\. Referencias tecnológicas**

* PHP: [https\://www\.php.net/](https://www.php.net/)  
* MariaDB: [https\://mariadb.org/](https://mariadb.org/)  
* MySQL: [https\://www\.mysql.com/](https://www.mysql.com/)  
* Apache Friends – XAMPP: [https\://www\.apachefriends.org/](https://www.apachefriends.org/)  
* Leaflet: [https\://leafletjs.com/](https://leafletjs.com/)  
* Scrum Guide: [https\://scrumguides.org/](https://scrumguides.org/)

