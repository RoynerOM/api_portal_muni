-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS muni_stock_db;
USE muni_stock_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de impresoras
CREATE TABLE IF NOT EXISTS impresoras (
    serie VARCHAR(255) PRIMARY KEY NOT NULL,
    modelo VARCHAR(255) NOT NULL,
    tipo ENUM('Tóner', 'Inyección', 'Cartucho', 'Pigmento', 'Térmica', 'Otros') NOT NULL,
    modeloTinta VARCHAR(255) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE
);

-- Tabla de tintas para cada impresora, donde se guarda el stock por color
CREATE TABLE IF NOT EXISTS tintas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    impresoraId VARCHAR(255) NOT NULL,
    tipo ENUM('Negro', 'Cyan', 'Magenta', 'Amarillo', 'Cinta', 'Otros') NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (impresoraId) REFERENCES impresoras(serie)
);

-- Tabla de entradas para registrar las entradas de tintas (por tipo o color)
CREATE TABLE IF NOT EXISTS entradas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    impresoraId VARCHAR(255) NOT NULL,
    usuarioId INT NOT NULL,
    stock INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('Negro', 'Cyan', 'Magenta', 'Amarillo', 'Cinta', 'Otros') NOT NULL,
    nota TEXT NOT NULL,
    FOREIGN KEY (impresoraId) REFERENCES impresoras(serie),
    FOREIGN KEY (usuarioId) REFERENCES usuarios(id)
);

-- Tabla de salidas para registrar las salidas de tintas (por tipo o color)
CREATE TABLE IF NOT EXISTS salidas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    impresoraId VARCHAR(255) NOT NULL,
    usuarioId INT NOT NULL,
    stock INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('Negro', 'Cyan', 'Magenta', 'Amarillo', 'Cinta', 'Otros') NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    nota TEXT NOT NULL,
    FOREIGN KEY (impresoraId) REFERENCES impresoras(serie),
    FOREIGN KEY (usuarioId) REFERENCES usuarios(id)
);
