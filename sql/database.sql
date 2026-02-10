-- Crear base de datos
CREATE DATABASE IF NOT EXISTS crud_php;
USE crud_php;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    ciudad VARCHAR(50) NOT NULL,
    tipo_usuario ENUM('Estudiante', 'Profesor') DEFAULT 'Estudiante',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, apellido, edad, email, ciudad, tipo_usuario) VALUES
('Juan', 'Pérez', 25, 'juan@example.com', 'Bogotá', 'Estudiante'),
('María', 'Gómez', 30, 'maria@example.com', 'Medellín', 'Profesor'),
('Carlos', 'Rodríguez', 22, 'carlos@example.com', 'Cali', 'Estudiante');