# API REST de Productos — Laravel

API REST desarrollada con Laravel para administrar productos, monedas y precios asociados a diferentes monedas.

## Tecnologías

- PHP 8.4
- Laravel 13
- PostgreSQL
- Laravel Sanctum para autenticación mediante tokens
- PHPUnit para pruebas automatizadas

## Funcionalidades

- Autenticación mediante inicio y cierre de sesión.
- Operaciones CRUD de productos.
- Consulta y registro de monedas.
- Consulta y registro de precios por producto y moneda.
- Validación de datos de entrada.
- Protección de rutas mediante autenticación.
- Prevención de precios duplicados para un mismo producto y moneda.
- Pruebas automatizadas de endpoints y validaciones.

## Requisitos previos

- PHP compatible con Laravel 13.
- Composer.
- PostgreSQL.
- Un cliente HTTP, como Postman.

## Instalación

Clonar el repositorio y entrar en la carpeta del proyecto:

```bash
git clone <https://github.com/Anndrydar/prueba-backend-laravel.git>
cd prueba-backend-productos
```

Instalar las dependencias:

```bash
composer install
```

Crear el archivo de configuración local:

```bash
copy .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

## Configuración de PostgreSQL

Crear una base de datos en PostgreSQL y configurar las variables correspondientes en el archivo `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=prueba_backend_productos
DB_USERNAME=postgres
DB_PASSWORD=_CLAVE
```

Reemplazar `_CLAVE` por la contraseña local de PostgreSQL.

Ejecutar las migraciones:

```bash
php artisan migrate
```

## Ejecutar el proyecto

Iniciar el servidor de desarrollo:

```bash
php artisan serve
```

La API estará disponible normalmente en:

`http://127.0.0.1:8000`

## Autenticación

La mayoría de los endpoints están protegidos mediante Laravel Sanctum.


## Usuario de prueba

Para probar los endpoints protegidos de la API, primero debes crear un usuario de prueba.

Ejecuta el siguiente comando desde la raíz del proyecto:

```bash
php artisan db:seed --class=TestUserSeeder
```

Este comando crea el usuario de prueba o actualiza sus datos si ya existe.



### Iniciar sesión


**POST** `/api/login`

Enviar las credenciales del usuario en formato JSON:

```json
{
  "email": "usuario@gmail.com",
  "password": "1234"
}
```

La respuesta exitosa incluye un token de autenticación.

Para consultar las rutas protegidas, enviar el token en el encabezado:

```http
Authorization: Bearer _TOKEN
Accept: application/json
```

### Cerrar sesión

**POST** `/api/logout`

Requiere autenticación. Revoca el token actual de la sesión autenticada.

## Endpoints

Todas las solicitudes y respuestas de la API deben utilizar JSON.

| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/login` | Iniciar sesión |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/products` | Listar productos |
| POST | `/api/products` | Registrar producto |
| GET | `/api/products/{id}` | Consultar un producto |
| PUT/PATCH | `/api/products/{id}` | Actualizar producto |
| DELETE | `/api/products/{id}` | Eliminar producto |
| GET | `/api/currencies` | Consultar monedas |
| POST | `/api/currencies` | Registrar moneda |
| GET | `/api/products/{id}/prices` | Consultar precios del producto |
| POST | `/api/products/{id}/prices` | Registrar precio para el producto |

Excepto el inicio de sesión, los endpoints anteriores requieren un token válido.

## Pruebas automatizadas

Ejecutar todo el cuarto de pruebas:

```bash
php artisan test
```

Las pruebas cubren autenticación, productos, monedas, precios, validaciones y rechazo de solicitudes no autenticadas.

## Autor

Desarrollado como parte de una prueba técnica de backend con Laravel y PostgreSQL.
