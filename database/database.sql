-- =====================================================================
-- 1. DCL (Data Control Language) - Control de Acceso
-- Justificación: Por seguridad, la aplicación no debe usar el usuario 'root'.
-- Creamos un usuario específico para el MVC con permisos limitados.
-- =====================================================================
CREATE USER 'encuestas_user'@'localhost' IDENTIFIED BY 'V4ll3_2026*';
GRANT SELECT, INSERT, UPDATE, DELETE ON bd_encuestas.* TO 'encuestas_user'@'localhost';
FLUSH PRIVILEGES;

-- Creamos la base de datos y la usamos
CREATE DATABASE IF NOT EXISTS bd_encuestas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_encuestas;

-- =====================================================================
-- 2. DDL (Data Definition Language) - Creación de Estructuras
-- Justificación: Definimos las tablas, tipos de datos y restricciones (Primary y Foreign Keys) 
-- para garantizar la integridad referencial y evitar datos huérfanos.
-- =====================================================================

-- Tabla para el Punto 1: Login de perfiles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Guardará el hash, no texto plano
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- Tablas paramétricas para el Punto 2: División territorial de Cali
CREATE TABLE comunas (
    id_comuna INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE barrios (
    id_barrio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_comuna INT NOT NULL,
    FOREIGN KEY (id_comuna) REFERENCES comunas(id_comuna)
);

-- Tabla para el Punto 2: Registro de Solicitud en Ventanilla
CREATE TABLE solicitudes (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_documento VARCHAR(10) NOT NULL,
    numero_documento VARCHAR(20) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    celular VARCHAR(15) NOT NULL,
    direccion VARCHAR(150) NOT NULL,
    id_barrio INT NOT NULL,
    id_usuario_ventanilla INT NOT NULL, -- Quién registró la solicitud
    FOREIGN KEY (id_barrio) REFERENCES barrios(id_barrio),
    FOREIGN KEY (id_usuario_ventanilla) REFERENCES usuarios(id_usuario)
);

-- =====================================================================
-- 3. DML (Data Manipulation Language) - Inserción de Datos Iniciales
-- Justificación: Necesitamos datos semilla (catálogos) para poder 
-- probar el Login y el formulario de Ventanilla.
-- =====================================================================

-- Insertar los roles exigidos
INSERT INTO roles (nombre) VALUES 
('Administrador'), ('Ventanilla'), ('Encuestador'), ('Calidad'), ('Enrutador');

-- Insertar usuarios de prueba. 
-- NOTA: Las contraseñas están encriptadas con BCRYPT. La contraseña de ambos es: 123456
INSERT INTO usuarios (documento, nombres, apellidos, username, password, id_rol) VALUES 
('1111', 'Juan', 'Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('2222', 'Maria', 'Ventanilla', 'maria_v', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- Insertar algunas comunas y barrios para probar el select de ventanilla
INSERT INTO comunas (nombre) VALUES ('Comuna 1'), ('Comuna 17');
INSERT INTO barrios (nombre, id_comuna) VALUES 
('Terrón Colorado', 1), ('Vista Hermosa', 1),
('Valle del Lili', 2), ('Ciudad Capitol', 2);