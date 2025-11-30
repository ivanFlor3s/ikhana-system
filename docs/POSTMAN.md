# Guía de Postman Collection

## Qué es

La **Postman Collection** es un archivo JSON que contiene todos los endpoints de la API con:
- URLs configuradas
- Métodos HTTP (GET, POST, PUT, DELETE)
- Headers necesarios
- Ejemplos de body/payloads
- Ejemplos de respuestas

## Cómo usar

### 1. Importar en Postman

**Opción A: Desde archivo**
1. Abrir Postman
2. Click en "Import" (esquina superior izquierda)
3. Seleccionar el archivo `Ikhana_API.postman_collection.json`
4. Click "Import"

**Opción B: Desde URL** (si está en GitHub)
1. Abrir Postman
2. Click en "Import"
3. Pegar la URL del archivo raw en GitHub
4. Click "Import"

### 2. Configurar Variables

Después de importar, configura la variable de entorno base:

1. Click en el ícono de "⚙️" (Settings) en Postman
2. Ir a "Variables" o crear un "Environment"
3. Agregar variable:
   - **Variable:** `baseUrl`
   - **Value:** `http://localhost:8000/api`

### 3. Probar Endpoints

Ahora puedes:
- Ver todos los endpoints organizados por carpetas
- Click en cualquier endpoint para verlo
- Click en "Send" para ejecutar la petición
- Ver la respuesta en tiempo real

## Estructura de la Colección

```
Ikhana API Documentation/
├── Endpoints/
├── AFIP Integration/
│   └── POST Validate Tax ID (CUIT/CUIL)
├── Brokers Management/
│   ├── GET List all brokers
│   ├── POST Create a new broker
│   ├── GET Get a specific broker
│   ├── PUT Update a broker
│   └── DELETE Delete a broker
├── Business Categories/
│   ├── GET List all categories
│   ├── POST Create a new category
│   ├── GET Get a specific category
│   ├── PUT Update a category
│   └── DELETE Delete a category
├── Gestión de Acuerdos/
│   ├── GET Listar todos los acuerdos
│   ├── POST Crear un nuevo acuerdo
│   ├── GET Obtener un acuerdo específico
│   ├── PUT Actualizar un acuerdo
│   └── DELETE Eliminar un acuerdo
├── Gestión de Posiciones IVA/
│   ├── GET Listar todas las posiciones
│   ├── POST Crear una nueva posición
│   ├── GET Obtener una posición específica
│   ├── PUT Actualizar una posición
│   └── DELETE Eliminar una posición
└── Gestión de Proveedores/
    ├── GET List all providers with filters and pagination
    ├── POST Create a new provider
    ├── GET Get a specific provider
    ├── PUT Update a provider
    └── DELETE Delete a provider
```

## Regenerar la Colección

Si agregas nuevos endpoints o modificas la API, regenera la colección:

```bash
# Regenerar documentación (incluye Postman collection)
docker-compose exec app php artisan scribe:generate

# Copiar a raíz del proyecto
cp src/storage/app/scribe/collection.json Ikhana_API.postman_collection.json
```

## Ventajas de usar Postman Collection

1. **Testing rápido** - Prueba endpoints sin escribir código
2. **Documentación visual** - Ve todos los endpoints organizados
3. **Ejemplos incluidos** - Cada endpoint tiene ejemplos de request/response
4. **Compartible** - Comparte con el equipo fácilmente
5. **Versionable** - Se puede versionar en Git

## Alternativas

Si no usas Postman, también puedes:
- **OpenAPI/Swagger**: `src/storage/app/scribe/openapi.yaml`
- **Documentación web**: `http://localhost:8000/docs`
- **Insomnia**: Puede importar la colección de Postman
- **Thunder Client** (VSCode): Puede importar colecciones

## Total de Endpoints

Esta colección incluye **27 endpoints** completamente documentados.

