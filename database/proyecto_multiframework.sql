-- Base de datos del proyecto multi-framework
CREATE DATABASE IF NOT EXISTS proyecto_multiframework
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE proyecto_multiframework;

DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  categoria VARCHAR(60) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  usuario_id INT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_productos_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- usuario admin (clave: admin123)
INSERT INTO usuarios (nombre, email, password, created_at, updated_at)
VALUES ('Administrador', 'admin@correo.com', '$2y$12$WGnc/ONRtMtlMzCiIe4kyutHPoLRm7lrLlhXE2pmf0oLGCDsP3OYe', NOW(), NOW());

INSERT INTO productos (nombre, categoria, precio, stock, usuario_id, created_at, updated_at) VALUES
('Teclado mecanico', 'Perifericos', 125000.00, 25, 1, NOW(), NOW()),
('Mouse inalambrico', 'Perifericos', 48000.00, 40, 1, NOW(), NOW()),
('Monitor 24 pulgadas', 'Pantallas', 520000.00, 12, 1, NOW(), NOW()),
('Disco SSD 480GB', 'Almacenamiento', 195000.00, 18, 1, NOW(), NOW()),
('Memoria USB 64GB', 'Almacenamiento', 32000.00, 60, 1, NOW(), NOW()),
('Camara web HD', 'Perifericos', 89000.00, 9, 1, NOW(), NOW());
