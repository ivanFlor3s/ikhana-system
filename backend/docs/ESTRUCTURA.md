# 📁 Estructura del Proyecto

## 🎯 Organización

El proyecto está organizado para separar claramente el código de Laravel de la configuración de Docker:

```
ikhana_backend/
│
├── src/                              ← TODO el código de Laravel
│   ├── app/                         ← Código de la aplicación
│   │   ├── Enums/                   ← Enumeraciones (TaxStatus, Agreement)
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Controller.php
│   │   │   │   └── Api/
│   │   │   │       ├── ProviderController.php
│   │   │   │       └── EnumController.php
│   │   │   └── Middleware/
│   │   ├── Models/                  ← Modelos de Eloquent
│   │   │   ├── Provider.php
│   │   │   └── User.php
│   │   └── Providers/
│   │       └── AppServiceProvider.php
│   │
│   ├── bootstrap/                   ← Bootstrap de Laravel
│   │   ├── app.php
│   │   ├── providers.php
│   │   └── cache/
│   │
│   ├── config/                      ← Archivos de configuración
│   │   ├── app.php
│   │   ├── database.php
│   │   ├── cache.php
│   │   ├── logging.php
│   │   ├── scribe.php
│   │   └── ...
│   │
│   ├── database/                    ← Migraciones y seeders
│   │   ├── factories/
│   │   │   └── UserFactory.php
│   │   ├── migrations/
│   │   │   ├── 0001_01_01_000000_create_users_table.php
│   │   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   │   └── 2024_11_16_000000_create_providers_table.php
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php
│   │       └── ProviderSeeder.php
│   │
│   ├── public/                      ← Punto de entrada público
│   │   ├── index.php               ← Entry point de la aplicación
│   │   ├── .htaccess
│   │   └── vendor/                 ← Assets de Scribe (docs)
│   │
│   ├── routes/                      ← Definición de rutas
│   │   ├── api.php                 ← Rutas de la API
│   │   ├── web.php                 ← Rutas web
│   │   └── console.php             ← Comandos de consola
│   │
│   ├── storage/                     ← Almacenamiento temporal
│   │   ├── app/
│   │   │   ├── public/
│   │   │   └── scribe/             ← Docs generadas (Postman, OpenAPI)
│   │   ├── framework/
│   │   │   ├── cache/
│   │   │   ├── sessions/
│   │   │   └── views/
│   │   └── logs/
│   │       └── laravel.log
│   │
│   ├── vendor/                      ← Dependencias de Composer
│   ├── .env                         ← Configuración del entorno (NO subir a Git)
│   ├── .env.example                 ← Plantilla de configuración (SÍ subir a Git)
│   ├── artisan                      ← CLI de Laravel
│   ├── composer.json                ← Dependencias del proyecto
│   ├── composer.lock                ← Lock file de Composer
│   └── phpunit.xml                  ← Configuración de PHPUnit
│
├── docker/                           ← Configuración de Docker
│   ├── nginx/
│   │   └── default.conf             ← Config de Nginx
│   └── php/
│       └── local.ini                ← Config de PHP
│
├── docker-compose.yml               ← Orquestación de contenedores
├── Dockerfile                       ← Imagen de PHP customizada
├── .dockerignore                    ← Archivos ignorados en Docker build
├── .gitignore                       ← Archivos ignorados en Git
│
├── README.md                        ← Documentación principal
├── DOCUMENTACION_API.md             ← Guía de uso de la API
├── ENUMS_FRONTEND.md                ← Ejemplos para frontend
├── GITHUB_SETUP.md                  ← Guía para subir a GitHub
├── INICIO_RAPIDO.md                 ← Guía de inicio rápido
├── CHECKLIST_GITHUB.md              ← Checklist antes de subir
├── ESTRUCTURA.md                    ← Este archivo
│
├── Ikhana_API.postman_collection.json  ← Colección de Postman
└── setup.sh                         ← Script de setup automático
```

## 🎯 Ventajas de esta Estructura

### ✅ Separación de Responsabilidades
- **`/src`**: Todo el código de Laravel en un solo lugar
- **`/docker`**: Configuración de Docker separada
- **`/` (raíz)**: Solo archivos de configuración del proyecto y documentación

### ✅ Fácil de Entender
- Un desarrollador nuevo puede identificar rápidamente dónde está el código
- La documentación está claramente visible en la raíz
- Los archivos de Docker no se mezclan con el código de Laravel

### ✅ Fácil de Desplegar
- Puedes desplegar solo la carpeta `src/` a un servidor sin Docker
- La configuración de Docker queda separada para diferentes ambientes
- Facilita el CI/CD

### ✅ Compatible con Monorepos
- Si en el futuro agregas un frontend, puedes tener:
  ```
  proyecto/
  ├── backend/src/     ← Laravel
  ├── frontend/src/    ← React/Vue/Angular
  └── docker/          ← Configs compartidas
  ```

## 📂 Carpetas Importantes

### `src/app/`
Aquí vive toda la lógica de negocio:
- **Enums**: Enumeraciones (TaxStatus, Agreement)
- **Http/Controllers**: Controladores de la API
- **Models**: Modelos de Eloquent
- **Providers**: Service Providers de Laravel

### `src/database/`
Gestión de base de datos:
- **migrations**: Esquema de la base de datos
- **seeders**: Datos de prueba
- **factories**: Factories para testing

### `src/routes/`
Definición de endpoints:
- **api.php**: Rutas de la API (prefijo `/api`)
- **web.php**: Rutas web (si las necesitas)

### `src/storage/`
Almacenamiento temporal:
- **logs**: Logs de la aplicación
- **app/scribe**: Documentación generada (Postman, OpenAPI)
- **framework**: Cache, sessions, views compiladas

### `src/public/`
Punto de entrada público:
- Solo esta carpeta debería ser accesible vía web
- Contiene `index.php` (entry point)
- Assets públicos (docs de Scribe en `/vendor`)

## 🔒 Archivos Sensibles

### NO subir a Git:
- ❌ `src/.env` - Contiene credenciales
- ❌ `src/vendor/` - Dependencias (se instalan con composer)
- ❌ `src/storage/logs/*` - Logs
- ❌ `src/bootstrap/cache/*` - Cache

### SÍ subir a Git:
- ✅ `src/.env.example` - Plantilla sin credenciales
- ✅ `src/composer.json` - Definición de dependencias
- ✅ Todo el código en `src/app/`
- ✅ Migraciones en `src/database/migrations/`
- ✅ Configuraciones en `src/config/`

## 🐳 Volúmenes de Docker

El `docker-compose.yml` monta:
```yaml
volumes:
  - ./src:/var/www        # Todo Laravel
```

Esto significa que:
- Los cambios en `src/` se reflejan inmediatamente en el contenedor
- No necesitas rebuilder para cambios en el código
- Solo rebuilder si cambias dependencias (composer.json)

## 📝 Archivos de Configuración

### En la raíz:
- `docker-compose.yml`: Define los servicios (app, nginx, db)
- `Dockerfile`: Define la imagen de PHP
- `.gitignore`: Qué no subir a Git
- `.dockerignore`: Qué no incluir en la imagen Docker

### En `src/`:
- `.env`: Variables de entorno (local, no subir)
- `.env.example`: Plantilla de variables (sí subir)
- `composer.json`: Dependencias PHP
- `phpunit.xml`: Configuración de tests

## 🔄 Flujo de Trabajo

### Desarrollo:
1. Trabajas en `src/app/`, `src/routes/`, etc.
2. Los cambios se ven inmediatamente (hot reload)
3. Si agregas dependencias: `docker-compose exec app composer install`
4. Si agregas migraciones: `docker-compose exec app php artisan migrate`

### Documentación:
1. Modificas anotaciones en controladores
2. Regeneras docs: `docker-compose exec app php artisan scribe:generate`
3. Las docs se actualizan en `http://localhost:8000/docs`

### Deploy:
1. Subes cambios a Git (solo `src/` y configs)
2. En producción: `git pull`
3. `docker-compose up -d --build`
4. `docker-compose exec app php artisan migrate`

## 🎓 Convenciones

### Nombres de Archivos:
- **Modelos**: Singular, PascalCase (`Provider.php`)
- **Controladores**: PascalCase + Controller (`ProviderController.php`)
- **Migraciones**: Snake_case con timestamp (`2024_11_16_000000_create_providers_table.php`)
- **Enums**: PascalCase (`TaxStatus.php`)

### Rutas API:
- **Plural**: `/api/providers` (no `/api/provider`)
- **RESTful**: GET, POST, PUT, DELETE
- **Versioning**: Considera `/api/v1/providers` para futuro

### Base de Datos:
- **Tablas**: Plural, snake_case (`providers`)
- **Columnas**: Snake_case (`business_name`)
- **Foreign keys**: Singular_id (`user_id`)

## 🚀 Comandos Útiles

```bash
# Ver estructura de src/
tree src/ -L 2 -I vendor

# Ver rutas registradas
docker-compose exec app php artisan route:list

# Ver migraciones pendientes
docker-compose exec app php artisan migrate:status

# Crear nuevo controlador
docker-compose exec app php artisan make:controller Api/NombreController --api

# Crear nuevo modelo
docker-compose exec app php artisan make:model NombreModelo -m

# Regenerar docs
docker-compose exec app php artisan scribe:generate
```

---

Esta estructura te permite:
- ✅ Escalar el proyecto fácilmente
- ✅ Agregar más servicios (frontend, workers, etc.)
- ✅ Mantener el código organizado y limpio
- ✅ Facilitar el onboarding de nuevos desarrolladores

