# Backend Restaurante - Microservicios PHP

Sistema de gestión de restaurante desarrollado con microservicios en PHP usando Slim Framework y Eloquent ORM.

## Microservicios

| Microservicio | Puerto | Base de datos |
|---|---|---|
| ms-auth | 8001 | db_auth |
| ms-reservas | 8002 | db_reservas |
| ms-productos | 8003 | db_productos |
| ms-pedidos | 8004 | db_pedidos |

## Requisitos

- PHP 8.0 o superior
- Composer
- XAMPP (Apache + MySQL)

## Pasos para correr en la U

### 1. Abrir XAMPP
- Iniciar Apache
- Iniciar MySQL

### 2. Clonar el repositorio
git clone https://github.com/Camilo699/Backend-restaurante.git
cd Backend-restaurante

### 3. Instalar dependencias en cada microservicio
cd ms-auth
composer install
cd ../ms-reservas
composer install
cd ../ms-productos
composer install
cd ../ms-pedidos
composer install

### 4. Crear las bases de datos en phpMyAdmin
- Abrir http://localhost/phpmyadmin
- Crear las 4 bases de datos:
  - db_auth
  - db_reservas
  - db_productos
  - db_pedidos
- Ejecutar el SQL de cada base de datos

### 5. Correr los microservicios
Abrir 4 terminales distintas y correr:

Terminal 1:
cd ms-auth
php -S 127.0.0.1:8001 -t public

Terminal 2:
cd ms-reservas
php -S 127.0.0.1:8002 -t public

Terminal 3:
cd ms-productos
php -S 127.0.0.1:8003 -t public

Terminal 4:
cd ms-pedidos
php -S 127.0.0.1:8004 -t public

## Tecnologías
- Slim Framework 4
- Eloquent ORM
- PSR-7
- MySQL