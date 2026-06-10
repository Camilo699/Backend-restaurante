-- =============================================
-- El Sazón de la Abuela - Base de datos
-- =============================================

-- Base de datos de autenticación
CREATE DATABASE IF NOT EXISTS db_auth;
USE db_auth;

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'empleado') NOT NULL,
    token VARCHAR(255) NULL,
    sesion_activa BOOLEAN DEFAULT FALSE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

INSERT IGNORE INTO usuarios (nombre, correo, usuario, contrasena, rol, estado, created_at, updated_at) VALUES
('Administrador General', 'admin@restaurante.com', 'admin', '$2y$10$j1uMY4.4zFqfKpxUrIBrYecRryd1nakn71QG12gaP7GZXJWCelKLO', 'administrador', 'activo', NOW(), NOW()),
('Empleado Restaurante', 'empleado@restaurante.com', 'empleado', '$2y$10$w.HpIrBKZDflaQb8mfcIjeBksx4x7POeZ.g/zk6AdL1kg032Gey5O', 'empleado', 'activo', NOW(), NOW());

-- Base de datos de reservas
CREATE DATABASE IF NOT EXISTS db_reservas;
USE db_reservas;

CREATE TABLE IF NOT EXISTS mesas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL UNIQUE,
    capacidad INT NOT NULL,
    estado ENUM('disponible', 'reservada', 'ocupada', 'fuera_servicio') DEFAULT 'disponible',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS reservas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(150) NOT NULL,
    telefono_cliente VARCHAR(30) NOT NULL,
    cantidad_personas INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    observaciones TEXT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'finalizada') DEFAULT 'pendiente',
    mesa_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_reservas_mesas FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

INSERT IGNORE INTO mesas (numero, capacidad, estado, created_at, updated_at) VALUES
('MESA-1', 2, 'disponible', NOW(), NOW()),
('MESA-2', 4, 'disponible', NOW(), NOW()),
('MESA-3', 6, 'disponible', NOW(), NOW()),
('MESA-4', 8, 'disponible', NOW(), NOW());

-- Base de datos de productos
CREATE DATABASE IF NOT EXISTS db_productos;
USE db_productos;

CREATE TABLE IF NOT EXISTS categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    categoria_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_productos_categorias FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

INSERT IGNORE INTO categorias (nombre, descripcion, created_at, updated_at) VALUES
('Entradas', 'Productos de entrada', NOW(), NOW()),
('Bebidas', 'Bebidas frías y calientes', NOW(), NOW()),
('Platos fuertes', 'Platos principales', NOW(), NOW()),
('Postres', 'Productos dulces', NOW(), NOW());

INSERT IGNORE INTO productos (nombre, descripcion, precio, disponible, categoria_id, created_at, updated_at) VALUES
('Hamburguesa Especial', 'Hamburguesa con queso y tocineta', 28000, TRUE, 3, NOW(), NOW()),
('Limonada Natural', 'Bebida natural de limón', 8000, TRUE, 2, NOW(), NOW()),
('Cheesecake', 'Postre de queso', 12000, TRUE, 4, NOW(), NOW());

-- Base de datos de pedidos
CREATE DATABASE IF NOT EXISTS db_pedidos;
USE db_pedidos;

CREATE TABLE IF NOT EXISTS pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mesa_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado ENUM('pendiente', 'en_preparacion', 'entregado', 'pagado', 'cancelado') DEFAULT 'pendiente',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS detalles_pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_detalles_pedidos_pedidos FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

INSERT IGNORE INTO productos (nombre, descripcion, precio, disponible, categoria_id, created_at, updated_at) VALUES
('Patacones con Hogao', 'Patacones fritos con salsa de tomate y cebolla', 9000, TRUE, 1, NOW(), NOW()),
('Empanadas de Pipián', 'Empanadas rellenas de papas con maní', 7000, TRUE, 1, NOW(), NOW()),
('Caldo de Costilla', 'Caldo tradicional con costilla de res', 12000, TRUE, 1, NOW(), NOW()),
('Arepa de Chócolo', 'Arepa dulce de maíz tierno con queso', 6000, TRUE, 1, NOW(), NOW()),
('Jugo de Lulo', 'Jugo natural de lulo', 7000, TRUE, 2, NOW(), NOW()),
('Jugo de Mora', 'Jugo natural de mora', 7000, TRUE, 2, NOW(), NOW()),
('Agua de Panela', 'Agua de panela caliente con limón', 5000, TRUE, 2, NOW(), NOW()),
('Chocolate Santafereño', 'Chocolate caliente con queso y pan', 8000, TRUE, 2, NOW(), NOW()),
('Mazamorra', 'Bebida tradicional de maíz', 6000, TRUE, 2, NOW(), NOW()),
('Bandeja Paisa', 'Frijoles, arroz, chicharrón, huevo, aguacate y chorizo', 32000, TRUE, 3, NOW(), NOW()),
('Ajiaco Bogotano', 'Sopa de pollo con papas y guascas', 28000, TRUE, 3, NOW(), NOW()),
('Sancocho de Gallina', 'Sancocho tradicional de gallina criolla', 26000, TRUE, 3, NOW(), NOW()),
('Trucha a la Plancha', 'Trucha fresca con arroz y ensalada', 30000, TRUE, 3, NOW(), NOW()),
('Cazuela de Mariscos', 'Cazuela con camarones y mariscos', 35000, TRUE, 3, NOW(), NOW()),
('Posta Negra', 'Carne de res en salsa negra con yuca', 28000, TRUE, 3, NOW(), NOW()),
('Arroz con Leche', 'Arroz con leche y canela', 8000, TRUE, 4, NOW(), NOW()),
('Natilla', 'Postre tradicional de maíz y panela', 7000, TRUE, 4, NOW(), NOW()),
('Buñuelos', 'Buñuelos esponjosos con miel', 6000, TRUE, 4, NOW(), NOW()),
('Flan de Coco', 'Flan suave de coco con caramelo', 9000, TRUE, 4, NOW(), NOW());