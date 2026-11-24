# Proyecto Multi-Framework: Laravel + Slim + Bootstrap/Fetch + MySQL

Sistema de inventario con tres aplicaciones independientes que comparten la misma
base de datos MySQL `proyecto_multiframework`.

| Carpeta | Tecnología | Función | Puerto |
|---|---|---|---|
| `admin-core/` | Laravel (MVC) | Login y CRUD de productos | 8000 |
| `api-servicios/` | Slim 4 + PDO + JWT | API REST en JSON | 8081 |
| `frontend/` | Bootstrap 5 + Fetch API | Vista pública con filtros y descuento de stock | 5500 |

```
Navegador ──► frontend (5500) ──fetch──► API Slim (8081) ──PDO──┐
                                                                 ├──► MySQL: proyecto_multiframework
Navegador ──► Laravel admin-core (8000) ──Eloquent──────────────┘
```

## Requisitos
- PHP 8.2 o superior
- Composer
- MySQL o MariaDB (XAMPP o Laragon)

## 1. Base de datos
Elige una de las dos opciones.

**Opción A (rápida):** en phpMyAdmin, pestaña *Importar*, selecciona
`database/proyecto_multiframework.sql`. Crea la base, las tablas `usuarios` y
`productos` (con clave foránea) y los datos de ejemplo.

**Opción B:** crea una base vacía llamada `proyecto_multiframework` y ejecuta las
migraciones de Laravel (paso 2, punto 4).

## 2. Laravel (admin-core)
`admin-core/` es el proyecto Laravel completo. No hace falta `composer create-project`.

```bash
cd admin-core
composer install
copy .env.example .env        (en Linux/Mac: cp .env.example .env)
php artisan key:generate
```

1. Revisa en `.env` los datos de la base:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=proyecto_multiframework
   DB_USERNAME=root
   DB_PASSWORD=
   SESSION_DRIVER=file
   CACHE_STORE=file
   ```
2. Si usaste la opción B del paso 1: `php artisan migrate --seed`
3. Inicia el servidor:
   ```bash
   php artisan serve
   ```
4. Abre http://127.0.0.1:8000

Usuario de prueba: `admin@correo.com`  Contraseña: `admin123`

## 3. API Slim (api-servicios)
```bash
cd api-servicios
composer install
php -S localhost:8081 -t public
```
La conexión a MySQL y la clave JWT están en `api-servicios/config.php`.

| Método | Ruta | Descripción | Token |
|---|---|---|---|
| POST | `/api/login` | Devuelve un token JWT (dura 1 hora) | No |
| GET | `/api/productos` | Lista productos (`?buscar=` y `?categoria=` opcionales) | No |
| GET | `/api/productos/{id}` | Un producto | No |
| GET | `/api/categorias` | Lista de categorías | No |
| POST | `/api/productos/descontar` | Descuenta stock. Cuerpo: `{"id":1,"cantidad":2}` | Sí (`Authorization: Bearer <token>`) |

Códigos HTTP usados: 200, 400 (datos inválidos), 401 (sin token o token vencido),
404 (no existe), 409 (stock insuficiente). CORS se configura en `public/index.php`.

## 4. Frontend
Con el servidor de PHP, desde la carpeta raíz del proyecto:
```bash
php -S localhost:5500 -t frontend
```
Abre http://localhost:5500. También sirve Live Server de VS Code.
Si la API corre en otra dirección, cambia la constante `API` al inicio del script
de `frontend/index.html`.

Para descontar stock hay que iniciar sesión con `admin@correo.com` / `admin123`.

## 5. Orden para encender todo
Tres terminales abiertas a la vez:
1. `cd admin-core` → `php artisan serve`
2. `cd api-servicios` → `php -S localhost:8081 -t public`
3. (raíz del proyecto) `php -S localhost:5500 -t frontend`

MySQL debe estar encendido (XAMPP).

## 6. Pruebas de la API
En `pruebas/` hay dos colecciones con las mismas 9 peticiones:
- `coleccion_thunder_client.json`: para Thunder Client (VS Code).
- `coleccion_postman.json`: para Postman.

En Thunder Client crea un entorno con las variables `base = http://localhost:8081`
y `token`. Ejecuta primero *Login*, copia el token a la variable `token` y luego
las demás. Códigos esperados, en orden: 200, 200, 200, 200, 404, 200, 401, 409, 400.

Nota: *Descontar stock* resta 2 unidades del producto 1 cada vez que se envía.

## 7. Prueba de integración
1. Descuenta stock desde el frontend (o desde la API).
2. Abre el listado de productos en Laravel: el stock aparece actualizado.
3. Crea un producto en Laravel y recarga el frontend: aparece en la tabla.

Esto demuestra que los dos frameworks trabajan sobre la misma base de datos.
