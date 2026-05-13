# 📚 Documentación API - Ikhana Backend

## 🌐 Acceso a la Documentación

### Documentación Interactiva (Scribe)
**URL Principal:** http://localhost:8000/docs

Esta es la documentación completa y visual de tu API, generada automáticamente con **Scribe**.

### Características de la Documentación:

✅ **Interfaz Visual Moderna**
- Navegación por grupos de endpoints
- Búsqueda instantánea
- Sidebar colapsable con todos los endpoints

✅ **Ejemplos de Código**
- Bash (curl)
- JavaScript (fetch)
- Código listo para copiar y pegar

✅ **Try It Out**
- Botón para probar cada endpoint directamente desde el navegador
- Formularios pre-rellenados con datos de ejemplo
- Ver respuestas en tiempo real

✅ **Información Detallada**
- Todos los parámetros (requeridos y opcionales)
- Tipos de datos
- Validaciones
- Ejemplos de respuestas exitosas
- Ejemplos de respuestas de error
- Códigos de estado HTTP

## 📦 Exportar Documentación

### 1. Colección de Postman
Ubicación: `storage/app/scribe/collection.json`

Para importar en Postman:
1. Abre Postman
2. Click en "Import"
3. Selecciona el archivo `collection.json`
4. ¡Listo! Todos los endpoints estarán disponibles

### 2. Especificación OpenAPI (Swagger)
Ubicación: `storage/app/scribe/openapi.yaml`

Puedes usar este archivo con:
- Swagger UI
- Redoc
- Postman
- Insomnia
- Cualquier herramienta compatible con OpenAPI 3.0

## 🔄 Regenerar Documentación

Cuando agregues o modifiques endpoints:

```bash
# Desde el host
docker-compose exec app php artisan scribe:generate

# Desde dentro del contenedor
php artisan scribe:generate
```

## 📝 Cómo Documentar Nuevos Endpoints

### 1. Agregar Anotaciones al Controlador

```php
/**
 * @group Nombre del Grupo
 * 
 * Descripción del grupo de endpoints
 */
class TuController extends Controller
{
    /**
     * Título del endpoint
     * 
     * Descripción detallada de lo que hace el endpoint.
     * 
     * @urlParam id integer required El ID del recurso. Example: 1
     * @bodyParam nombre string required El nombre. Example: Juan
     * @bodyParam email string optional El email. Example: juan@ejemplo.com
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "nombre": "Juan"
     *   }
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "No encontrado"
     * }
     */
    public function tuMetodo()
    {
        // tu código
    }
}
```

### 2. Regenerar la Documentación

```bash
docker-compose exec app php artisan scribe:generate
```

### 3. Verificar los Cambios

Visita `http://localhost:8000/docs` y verás tus cambios reflejados.

## 🎨 Personalizar la Documentación

El archivo de configuración está en: `config/scribe.php`

### Cambiar el título:
```php
'title' => 'Mi API Documentation',
```

### Cambiar la descripción:
```php
'description' => 'API para gestión de proveedores',
```

### Cambiar el logo:
```php
'logo' => 'ruta/a/tu/logo.png',
```

### Cambiar colores:
```php
'theme' => 'default', // o 'green', 'blue', 'orange'
```

## 🔍 Endpoints Actuales Documentados

### Provider Management

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/providers` | Listar todos los proveedores |
| POST | `/api/providers` | Crear un nuevo proveedor |
| GET | `/api/providers/{id}` | Obtener un proveedor específico |
| PUT | `/api/providers/{id}` | Actualizar un proveedor |
| DELETE | `/api/providers/{id}` | Eliminar un proveedor (soft delete) |

## 💡 Tips para el Frontend

### 1. Usar la Documentación Interactiva
Los desarrolladores frontend pueden:
- Ver todos los campos disponibles
- Entender las validaciones
- Probar los endpoints sin código
- Copiar ejemplos directos

### 2. Descargar la Colección de Postman
```bash
# Copiar del contenedor al host
docker-compose exec app cat storage/app/scribe/collection.json > postman_collection.json
```

### 3. Integrar con Herramientas
El archivo OpenAPI puede usarse con:
- **Swagger UI** - Para documentación interactiva
- **Code Generators** - Para generar SDKs automáticamente
- **API Testing** - Para pruebas automatizadas

## 🚀 Ventajas para el Equipo

### Para Backend:
- ✅ Documentación siempre actualizada
- ✅ No necesitas escribir documentación manualmente
- ✅ Anotaciones en el código fuente (fácil de mantener)
- ✅ Genera múltiples formatos (HTML, Postman, OpenAPI)

### Para Frontend:
- ✅ Interfaz visual clara y navegable
- ✅ Ejemplos de código listos para usar
- ✅ Pruebas directas desde el navegador
- ✅ Especificación técnica completa (OpenAPI)

### Para QA/Testing:
- ✅ Colección de Postman lista para usar
- ✅ Todos los casos de éxito y error documentados
- ✅ Ejemplos de datos válidos e inválidos

## 🔗 Enlaces Útiles

- **Documentación Scribe:** https://scribe.knuckles.wtf/laravel/
- **OpenAPI Specification:** https://swagger.io/specification/
- **Postman:** https://www.postman.com/

## 📞 Soporte

Si necesitas agregar más información a la documentación o tienes dudas, consulta:
- Archivo de configuración: `config/scribe.php`
- Documentación oficial: https://scribe.knuckles.wtf/laravel/

