# 🤝 Guía de Contribución

## 📋 Convenciones del Proyecto

### Nomenclatura

#### Modelos
- **Singular**, PascalCase
- Ejemplo: `Customer.php`, `Order.php`, `Product.php`

#### Controladores
- **Singular** + `Controller`, PascalCase
- API: dentro de `app/Http/Controllers/Api/`
- Ejemplo: `CustomerController.php`, `OrderController.php`

#### Rutas API
- **Plural**, kebab-case
- Ejemplo: `/api/customers`, `/api/orders`, `/api/products`

#### Migraciones
- Snake_case con prefijo descriptivo
- Ejemplo: `create_customers_table`, `add_status_to_orders_table`

#### Enums
- PascalCase, valores en SCREAMING_SNAKE_CASE
- Ejemplo: `OrderStatus.php` con valores `PENDING`, `CONFIRMED`, `DELIVERED`

### Estructura de Base de Datos

#### Tablas
- **Plural**, snake_case
- Ejemplo: `customers`, `orders`, `products`

#### Columnas
- Snake_case
- Ejemplo: `first_name`, `created_at`, `is_active`

#### Foreign Keys
- Singular + `_id`
- Ejemplo: `user_id`, `customer_id`, `order_id`

## 🏗️ Agregar una Nueva Entidad

### 1. Crear el Modelo y Migración

```bash
docker-compose exec app php artisan make:model NombreModelo -m
```

Esto crea:
- `src/app/Models/NombreModelo.php`
- `src/database/migrations/YYYY_MM_DD_HHMMSS_create_nombre_modelos_table.php`

### 2. Definir la Migración

Edita el archivo de migración en `src/database/migrations/`:

```php
public function up(): void
{
    Schema::create('nombre_modelos', function (Blueprint $table) {
        $table->id();
        $table->string('campo1');
        $table->text('campo2')->nullable();
        $table->timestamps();
        $table->softDeletes(); // Si usas soft delete
    });
}
```

### 3. Configurar el Modelo

Edita `src/app/Models/NombreModelo.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NombreModelo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campo1',
        'campo2',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
```

### 4. Crear el Controlador API

```bash
docker-compose exec app php artisan make:controller Api/NombreModeloController --api
```

Esto crea: `src/app/Http/Controllers/Api/NombreModeloController.php`

### 5. Implementar CRUD en el Controlador

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NombreModelo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Nombre del Módulo
 * 
 * APIs para gestionar [descripción]
 */
class NombreModeloController extends Controller
{
    /**
     * Listar todos los registros
     */
    public function index(): JsonResponse
    {
        $records = NombreModelo::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $records,
            'message' => 'Registros obtenidos exitosamente'
        ], 200);
    }

    /**
     * Crear un nuevo registro
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campo1' => 'required|string|max:255',
            'campo2' => 'nullable|string',
        ]);

        $record = NombreModelo::create($validated);

        return response()->json([
            'success' => true,
            'data' => $record,
            'message' => 'Registro creado exitosamente'
        ], 201);
    }

    /**
     * Obtener un registro específico
     */
    public function show(string $id): JsonResponse
    {
        $record = NombreModelo::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $record,
            'message' => 'Registro obtenido exitosamente'
        ], 200);
    }

    /**
     * Actualizar un registro
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $record = NombreModelo::findOrFail($id);

        $validated = $request->validate([
            'campo1' => 'sometimes|required|string|max:255',
            'campo2' => 'nullable|string',
        ]);

        $record->update($validated);

        return response()->json([
            'success' => true,
            'data' => $record->fresh(),
            'message' => 'Registro actualizado exitosamente'
        ], 200);
    }

    /**
     * Eliminar un registro
     */
    public function destroy(string $id): JsonResponse
    {
        $record = NombreModelo::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente'
        ], 200);
    }
}
```

### 6. Registrar las Rutas

Edita `src/routes/api.php`:

```php
use App\Http\Controllers\Api\NombreModeloController;

Route::prefix('nombre-modelos')->group(function () {
    Route::get('/', [NombreModeloController::class, 'index']);
    Route::post('/', [NombreModeloController::class, 'store']);
    Route::get('/{id}', [NombreModeloController::class, 'show']);
    Route::put('/{id}', [NombreModeloController::class, 'update']);
    Route::delete('/{id}', [NombreModeloController::class, 'destroy']);
});
```

### 7. Ejecutar la Migración

```bash
docker-compose exec app php artisan migrate
```

### 8. Regenerar la Documentación

```bash
docker-compose exec app php artisan scribe:generate
```

### 9. Probar los Endpoints

Visita `http://localhost:8000/docs` para ver tu nueva entidad documentada.

## 📝 Agregar un Enum

### 1. Crear el Enum

Crea el archivo en `src/app/Enums/NombreEnum.php`:

```php
<?php

namespace App\Enums;

enum NombreEnum: string
{
    case OPCION_1 = 'opcion_1';
    case OPCION_2 = 'opcion_2';
    case OPCION_3 = 'opcion_3';

    public function label(): string
    {
        return match($this) {
            self::OPCION_1 => 'Opción 1',
            self::OPCION_2 => 'Opción 2',
            self::OPCION_3 => 'Opción 3',
        };
    }

    public static function toArray(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label()
            ],
            self::cases()
        );
    }
}
```

### 2. Agregar Endpoint para el Enum

En `src/app/Http/Controllers/Api/EnumController.php`:

```php
public function getNombreEnums(): JsonResponse
{
    return response()->json([
        'success' => true,
        'data' => NombreEnum::toArray(),
        'message' => 'Opciones obtenidas exitosamente'
    ], 200);
}
```

### 3. Registrar la Ruta

En `src/routes/api.php`:

```php
Route::get('/nombre-enums', [EnumController::class, 'getNombreEnums']);
```

## 🧪 Testing

### Crear un Test

```bash
docker-compose exec app php artisan make:test NombreModeloTest
```

### Ejecutar Tests

```bash
docker-compose exec app php artisan test
```

## 📚 Documentación

### Agregar Documentación a un Endpoint

Usa anotaciones PHPDoc en los controladores:

```php
/**
 * Título del endpoint
 * 
 * Descripción detallada.
 * 
 * @urlParam id integer required El ID del recurso. Example: 1
 * @bodyParam campo1 string required Descripción. Example: valor
 * 
 * @response 200 scenario="success" {
 *   "success": true,
 *   "data": {},
 *   "message": "Mensaje de éxito"
 * }
 */
public function metodo()
{
    // código
}
```

## 🔄 Workflow de Git

### Crear una rama

```bash
git checkout -b feature/nombre-de-la-feature
```

### Hacer commits

```bash
git add .
git commit -m "feat: agregar módulo de clientes"
```

### Tipos de commits (Conventional Commits)

- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Cambios en documentación
- `refactor:` Refactorización de código
- `test:` Agregar tests
- `chore:` Cambios en configuración
- `style:` Cambios de formato (sin afectar lógica)
- `perf:` Mejoras de rendimiento

### Push y Pull Request

```bash
git push origin feature/nombre-de-la-feature
```

Luego crea un Pull Request en GitHub.

## ✅ Checklist antes de hacer PR

- [ ] El código sigue las convenciones del proyecto
- [ ] Tests agregados/actualizados y pasando
- [ ] Documentación regenerada (`php artisan scribe:generate`)
- [ ] Migraciones probadas y funcionando
- [ ] No hay errores de linting
- [ ] Commit messages son descriptivos
- [ ] Las variables de entorno necesarias están documentadas
- [ ] Se actualizó el README si es necesario

## 📝 Buenas Prácticas

### Validaciones
- Siempre validar inputs en los controladores
- Usar Form Requests para validaciones complejas
- Definir reglas de validación claras y específicas

### Modelos
- Definir `$fillable` o `$guarded` explícitamente
- Usar `$casts` para tipos de datos
- Implementar relaciones de forma clara
- Usar Soft Deletes cuando sea apropiado

### Controladores
- Mantenerlos simples y enfocados (Single Responsibility)
- No incluir lógica de negocio compleja
- Usar Services para lógica compleja
- Retornar respuestas JSON consistentes

### Base de Datos
- Usar migraciones para TODOS los cambios de esquema
- Nunca modificar migraciones ya ejecutadas en producción
- Crear seeders para datos de prueba
- Usar índices apropiadamente

### Seguridad
- Nunca commitear archivos `.env`
- Validar TODOS los inputs del usuario
- Usar prepared statements (Eloquent lo hace por defecto)
- Sanitizar datos antes de mostrarlos

## 🆘 Ayuda y Recursos

### Documentación del Proyecto
- `README.md` - Documentación general e instalación
- `ESTRUCTURA.md` - Estructura detallada del proyecto
- `DOCUMENTACION_API.md` - Guía de la API
- `ENUMS_FRONTEND.md` - Ejemplos para integración frontend

### Recursos Externos
- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Docker Documentation](https://docs.docker.com/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [REST API Best Practices](https://restfulapi.net/)

### Comandos Útiles

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Reiniciar contenedores
docker-compose restart

# Acceder a MySQL
docker-compose exec db mysql -u ikhana -proot ikhana

# Limpiar todo y empezar de cero
docker-compose down -v
docker-compose up -d --build
docker-compose exec app bash /usr/local/bin/setup.sh

# Ver rutas disponibles
docker-compose exec app php artisan route:list

# Crear migración
docker-compose exec app php artisan make:migration nombre_descriptivo

# Rollback última migración
docker-compose exec app php artisan migrate:rollback

# Refrescar base de datos (⚠️ borra todos los datos)
docker-compose exec app php artisan migrate:fresh
```

## 🐛 Troubleshooting

### El contenedor no arranca
```bash
docker-compose down
docker-compose up -d --build
```

### Error de permisos en storage/
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### La documentación no se genera
```bash
docker-compose exec app composer require knuckleswtf/scribe --dev
docker-compose exec app php artisan scribe:generate
```

### No conecta a la base de datos
- Verifica que el contenedor de MySQL esté corriendo: `docker-compose ps`
- Revisa el `.env`: host debe ser `db` (nombre del servicio en docker-compose)
- Espera unos segundos, MySQL tarda en iniciar la primera vez

---

Si tienes dudas o encuentras problemas, no dudes en preguntar al equipo o abrir un issue en GitHub.
