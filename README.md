# Ikhana Backend API

API RESTful desarrollada con Laravel 11 y Docker. Backend modular y escalable con documentación automática.

## 🚀 Requisitos Previos

- Docker
- Docker Compose

## 📦 Instalación y Configuración

### 1. Clonar el repositorio

```bash
git clone https://github.com/mfloreslucas/ikhana_backend.git
cd ikhana_backend
```

### 2. Copiar el archivo de configuración

```bash
cp src/.env.example src/.env
```

⚠️ **IMPORTANTE:** El archivo `.env` contiene la configuración del proyecto. Revisa y ajusta los valores si es necesario.

### 3. Levantar los contenedores Docker

```bash
docker-compose up -d --build
```

Esto va a:
- ✅ Construir la imagen PHP con todas las extensiones
- ✅ Levantar MySQL en el puerto 3306
- ✅ Levantar Nginx en el puerto 8000

### 4. Instalar dependencias y configurar Laravel

```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Dentro del contenedor, ejecutar:
composer install
php artisan key:generate
php artisan migrate
php artisan scribe:generate

# Salir del contenedor
exit
```

**O usar el script de setup (todo en uno):**

```bash
docker-compose exec app bash setup.sh
```

### 5. Verificar que todo esté funcionando

Visita: `http://localhost:8000`

Deberías ver un JSON con información de la API:

```json
{
  "message": "Ikhana API",
  "version": "1.0.0",
  "documentation": "/api/documentation"
}
```

### 6. Ver la documentación interactiva

**¡IMPORTANTE!** Documentación completa de la API disponible en:

👉 **http://localhost:8000/docs**

La documentación incluye:
- ✅ Todos los endpoints con ejemplos
- ✅ Parámetros requeridos y opcionales
- ✅ Respuestas de éxito y error
- ✅ Ejemplos de código en múltiples lenguajes (bash, JavaScript)
- ✅ Botón "Try it out" para probar los endpoints directamente
- ✅ Exportación automática a Postman y OpenAPI

## 📚 API Endpoints

**Base URL:** `http://localhost:8000/api`

**Documentación Completa:** `http://localhost:8000/docs`

La API sigue principios RESTful con las siguientes convenciones:
- ✅ Respuestas en formato JSON
- ✅ Códigos HTTP estándar (200, 201, 404, 422, 500)
- ✅ Validaciones automáticas
- ✅ Soft deletes en recursos principales
- ✅ Paginación automática
- ✅ Filtrado y búsqueda

### Estructura de Respuesta Estándar

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": { /* datos del recurso */ },
  "message": "Descripción del resultado"
}
```

**Respuesta de Error:**
```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": { /* detalles si aplica */ }
}
```

### Endpoints Disponibles

Para ver todos los endpoints disponibles con ejemplos completos, visita la documentación interactiva:

👉 **http://localhost:8000/docs**

La documentación incluye:
- Lista completa de endpoints por módulo
- Parámetros requeridos y opcionales
- Ejemplos de requests y responses
- Códigos de error posibles
- Botón "Try it out" para probar directamente

## 📖 Documentación de la API

### 🌐 Documentación Interactiva (Recomendado)

Accede a la documentación completa en:
```
http://localhost:8000/docs
```

**Características:**
- 📝 Todos los endpoints documentados
- 🎯 Ejemplos de request/response
- 🧪 Probar endpoints directamente (Try it out)
- 📦 Exportar a Postman
- 📄 Especificación OpenAPI

### 📦 Exportar Documentación

**Colección de Postman:**
```bash
docker-compose exec app cat src/storage/app/scribe/collection.json > ikhana_api.postman_collection.json
```

**Especificación OpenAPI/Swagger:**
```bash
docker-compose exec app cat src/storage/app/scribe/openapi.yaml > openapi.yaml
```

### 🔄 Regenerar Documentación

Después de agregar o modificar endpoints:
```bash
docker-compose exec app php artisan scribe:generate
```

## 🛠️ Comandos Útiles

### Acceder al contenedor de la aplicación
```bash
docker-compose exec app bash
```

### Ver logs
```bash
docker-compose logs -f app
```

### Ejecutar migraciones
```bash
docker-compose exec app php artisan migrate
```

### Crear un nuevo módulo completo
```bash
# Modelo + Migración + Controller + Resource
php artisan make:model NombreModelo -mcr

# Solo Controller API
php artisan make:controller Api/NombreController --api

# Solo Migración
php artisan make:migration create_nombre_table

# Seeder
php artisan make:seeder NombreSeeder
```

### Limpiar caché
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Detener los contenedores
```bash
docker-compose down
```

### Detener y eliminar volúmenes (⚠️ esto borrará la base de datos)
```bash
docker-compose down -v
```

## 🗄️ Base de Datos

- **Motor:** MySQL 8.0
- **Host:** localhost
- **Puerto:** 3306
- **Base de datos:** ikhana
- **Usuario:** ikhana
- **Contraseña:** root

Puedes conectarte desde tu host usando cualquier cliente MySQL:

```bash
mysql -h 127.0.0.1 -P 3306 -u ikhana -proot ikhana
```

## 📁 Estructura del Proyecto

```
ikhana_backend/
├── src/                          ← Código de Laravel
│   ├── app/
│   │   ├── Http/Controllers/Api/ ← Controladores de la API
│   │   ├── Models/               ← Modelos de Eloquent
│   │   └── Enums/                ← Enumeraciones
│   ├── database/
│   │   ├── migrations/           ← Esquema de base de datos
│   │   └── seeders/              ← Datos de prueba
│   ├── routes/
│   │   └── api.php               ← Rutas de la API
│   ├── config/                   ← Configuración
│   ├── storage/                  ← Logs y archivos temporales
│   ├── composer.json
│   └── .env                      ← Variables de entorno
├── docker/                       ← Configuración Docker
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── local.ini
├── docker-compose.yml           ← Orquestación de contenedores
├── Dockerfile                   ← Imagen de PHP
├── README.md                    ← Este archivo
├── DOCUMENTACION_API.md         ← Guía de la API
├── ENUMS_FRONTEND.md           ← Ejemplos para frontend
├── GITHUB_SETUP.md             ← Guía de GitHub
└── setup.sh                    ← Script de instalación
```

## 🔧 Stack Tecnológico

| Componente | Tecnología | Versión |
|------------|------------|---------|
| Framework | Laravel | 11.x |
| Lenguaje | PHP | 8.2 |
| Base de Datos | MySQL | 8.0 |
| Servidor Web | Nginx | Alpine |
| Documentación | Scribe | 5.x |
| Containerización | Docker & Docker Compose | Latest |

## 🎯 Características

- ✅ **API RESTful** con convenciones estándar
- ✅ **Documentación automática** con Scribe
- ✅ **Validaciones robustas** en todos los endpoints
- ✅ **Soft deletes** para datos sensibles
- ✅ **Enums dinámicos** para selects
- ✅ **Docker** para desarrollo y producción
- ✅ **Migraciones** para control de base de datos
- ✅ **Seeders** para datos de prueba
- ✅ **Testing** preparado con PHPUnit
