# 🚀 Guía de Inicio Rápido - Ikhana Backend

## Para ti que tienes este proyecto creado manualmente

Dado que creé todos los archivos manualmente (en lugar de usar `laravel new`), aquí está exactamente lo que necesitas hacer:

### Paso 1: Levantar Docker 🐳

```bash
docker-compose up -d --build
```

Esto va a:
- Construir las imágenes de Docker
- Levantar MySQL en el puerto 3306
- Levantar Nginx en el puerto 8000
- Levantar PHP-FPM

### Paso 2: Entrar al contenedor e instalar Laravel 📦

```bash
docker-compose exec app bash
```

Una vez dentro del contenedor, ejecuta:

```bash
# Instalar todas las dependencias de Laravel con Composer
composer install

# Generar la clave de la aplicación
php artisan key:generate

# Esperar a que MySQL esté listo (puede tomar unos segundos)
sleep 10

# Ejecutar las migraciones para crear todas las tablas
php artisan migrate

# Salir del contenedor
exit
```

### Paso 3: Probar la API ✅

Abre tu navegador o Postman y prueba:

**1. Verificar que la API esté corriendo:**
```
GET http://localhost:8000
```

Deberías ver:
```json
{
  "message": "Ikhana API",
  "version": "1.0.0",
  "documentation": "/api/documentation"
}
```

**2. Crear un proveedor de prueba:**
```bash
curl -X POST http://localhost:8000/api/providers \
  -H "Content-Type: application/json" \
  -d '{
    "business_name": "Proveedor Test S.A.",
    "fantasy_name": "Proveedor Test",
    "cuit": "20-12345678-9",
    "email_1": "contacto@proveedor.com",
    "phone_1": "+54 11 1234-5678"
  }'
```

**3. Listar todos los proveedores:**
```
GET http://localhost:8000/api/providers
```

## 🎯 Endpoints Disponibles

| Método | URL | Descripción |
|--------|-----|-------------|
| GET | `/api/providers` | Listar todos los proveedores |
| POST | `/api/providers` | Crear un nuevo proveedor |
| GET | `/api/providers/{id}` | Ver un proveedor específico |
| PUT | `/api/providers/{id}` | Actualizar un proveedor |
| DELETE | `/api/providers/{id}` | Eliminar un proveedor |

## 🔍 Comandos Útiles

### Ver logs en tiempo real
```bash
docker-compose logs -f app
```

### Reiniciar la base de datos
```bash
docker-compose exec app php artisan migrate:fresh
```

### Acceder a MySQL directamente
```bash
docker-compose exec db mysql -u ikhana -proot ikhana
```

### Ver rutas disponibles
```bash
docker-compose exec app php artisan route:list
```

### Detener todo
```bash
docker-compose down
```

### Detener y borrar TODO (incluyendo la base de datos)
```bash
docker-compose down -v
```

## ⚠️ Solución de Problemas

### Error: "Connection refused" en MySQL
Espera unos segundos más y vuelve a intentar. MySQL puede tardar en iniciarse.

### Error: "APP_KEY is missing"
Ejecuta: `docker-compose exec app php artisan key:generate`

### Error al hacer migrate
Verifica que MySQL esté corriendo: `docker-compose ps`

### Permisos en storage/
Si tienes problemas de permisos:
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

## 📝 Próximos Pasos

1. ✅ Ya tienes el ABM de Providers funcionando
2. Si quieres agregar más entidades, usa:
   ```bash
   docker-compose exec app php artisan make:model NombreModelo -mcr
   ```
   Esto crea: Modelo, Migración, Controller y Resource

3. Si quieres seeders para datos de prueba:
   ```bash
   docker-compose exec app php artisan make:seeder ProviderSeeder
   ```

## 🎨 Testing con Postman

Importa esta colección en Postman para probar todos los endpoints:

```json
{
  "info": {
    "name": "Ikhana API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "List Providers",
      "request": {
        "method": "GET",
        "url": "http://localhost:8000/api/providers"
      }
    },
    {
      "name": "Create Provider",
      "request": {
        "method": "POST",
        "header": [{"key": "Content-Type", "value": "application/json"}],
        "url": "http://localhost:8000/api/providers",
        "body": {
          "mode": "raw",
          "raw": "{\n  \"business_name\": \"Test Provider\",\n  \"cuit\": \"20-12345678-9\"\n}"
        }
      }
    }
  ]
}
```

¡Listo! Ya tienes tu API funcionando 🎉

