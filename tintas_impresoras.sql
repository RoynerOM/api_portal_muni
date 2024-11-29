-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS muni_stock_db;

-- Uso de la base de datos creada
USE muni_stock_db;


CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'admin',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS impresoras (
    serie VARCHAR(255) PRIMARY KEY NOT NULL,
    modelo VARCHAR(255) NOT NULL,
    tipo ENUM('Tóner', 'Inyección','Cartucho','Pigmento','Termica','Otros') NOT NULL,
    modeloTinta VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    disponible BOOLEAN DEFAULT TRUE
);


CREATE TABLE IF NOT EXISTS entradas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    impresoraId VARCHAR(255) NOT NULL,
    usuarioId INT NOT NULL,
    stock INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ubicacion VARCHAR(255) NOT NULL,
    nota TEXT NOT NULL,
    tipo ENUM('Negro', 'Amarillo','Magenta','Cyan','Cinta','Otros') NOT NULL,
    FOREIGN KEY (impresoraId) REFERENCES impresoras(serie),
    FOREIGN KEY (usuarioId) REFERENCES usuarios(id)
);


CREATE TABLE IF NOT EXISTS salidas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    impresoraId VARCHAR(255) NOT NULL,
    usuarioId INT NOT NULL,
    stock INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ubicacion VARCHAR(255) NOT NULL,
    nota TEXT NOT NULL,
    tipo ENUM('Negro', 'Amarillo','Magenta','Cyan','Cinta','Otros') NOT NULL,
    FOREIGN KEY (impresoraId) REFERENCES impresoras(serie),
    FOREIGN KEY (usuarioId) REFERENCES usuarios(id)
);