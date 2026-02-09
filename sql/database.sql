-- Script SQL para crear la base de datos del proyecto

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS proyecto_php;
USE proyecto_php;

-- Crear tabla de ejemplo (usuarios)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    teléfono VARCHAR(15),
    dirección TEXT,
    fecha_creación TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualización TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email, teléfono, dirección) VALUES
('Juan Pérez', 'juan@example.com', '123456789', 'Calle Principal 123'),
('María García', 'maria@example.com', '987654321', 'Avenida Central 456'),
('Carlos López', 'carlos@example.com', '555666777', 'Carrera 789');
