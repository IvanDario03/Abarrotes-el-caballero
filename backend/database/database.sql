-- Script de base de datos para Abarrotes El Caballero

CREATE DATABASE IF NOT EXISTS abarrotes_el_caballero
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE abarrotes_penaloza;

DROP TABLE IF EXISTS venta_detalle;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','cajero') NOT NULL DEFAULT 'cajero',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255),
  estado TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo_barras VARCHAR(50) UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  precio_compra DECIMAL(10,2) NOT NULL DEFAULT 0,
  precio_venta DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  id_categoria INT,
  ruta_imagen VARCHAR(255),
  estado TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_productos_categorias
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
      ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2) NOT NULL,
  usuario_id INT,
  metodo_pago ENUM('efectivo','tarjeta','transferencia') DEFAULT 'efectivo',
  CONSTRAINT fk_ventas_usuarios
    FOREIGN KEY (usuario_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE venta_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_venta
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detalle_producto
    FOREIGN KEY (producto_id) REFERENCES productos(id)
      ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO users (nombre, email, password, rol)
VALUES ('Administrador', 'admin@abarrotes.com', '12345', 'admin');

INSERT INTO categorias (nombre, descripcion) VALUES
('Abarrotes', 'Productos de despensa en general'),
('Lácteos', 'Leche, yogurt, quesos, etc.'),
('Bebidas', 'Refrescos, jugos, agua, etc.'),
('Limpieza', 'Detergentes, cloro, jabones, etc.'),
('Botanas', 'Papas, frituras, dulces, etc.');
