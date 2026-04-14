<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderCoilSummaryResource;
use App\Http\Resources\ProviderCoilMovementResource;
use App\Models\Provider;
use App\Models\ProviderCoilMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Bobinas por Proveedor
 *
 * Endpoints para consultar el resumen de bobinas por proveedor
 * y el detalle de movimientos.
 */
class ProviderCoilController extends Controller
{
    /**
     * Resumen de bobinas por proveedor
     *
     * Listado paginado de proveedores que tienen inventario de bobinas,
     * con la cantidad actual de bobinas y las fechas del último movimiento
     * de entrada y salida.
     *
     * @queryParam page integer Número de página. Example: 1
     * @queryParam per_page integer Items por página (default: 15, max: 100). Example: 20
     * @queryParam search string Filtrar por nombre del proveedor (fantasy_name o business_name). Example: Rio Batel
     * @queryParam sort_by string Columna para ordenar: provider_name, coils_count, last_movement_in, last_movement_out. Example: coils_count
     * @queryParam sort_dir string Dirección: asc o desc (default: asc). Example: desc
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "provider_id": 5,
     *         "provider_name": "Rio Batel Trafilación Cobre",
     *         "coils_count": 42,
     *         "last_movement_in": "2026-04-10T23:45:00.000000Z",
     *         "last_movement_out": "2026-04-08T14:00:00.000000Z"
     *       },
     *       {
     *         "provider_id": 8,
     *         "provider_name": "Cobre del Litoral S.A.",
     *         "coils_count": 15,
     *         "last_movement_in": "2026-04-05T10:30:00.000000Z",
     *         "last_movement_out": null
     *       }
     *     ],
     *     "first_page_url": "http://localhost:8000/api/providers/coil-summary?page=1",
     *     "from": 1,
     *     "last_page": 2,
     *     "last_page_url": "http://localhost:8000/api/providers/coil-summary?page=2",
     *     "next_page_url": "http://localhost:8000/api/providers/coil-summary?page=2",
     *     "path": "http://localhost:8000/api/providers/coil-summary",
     *     "per_page": 15,
     *     "prev_page_url": null,
     *     "to": 15,
     *     "total": 28
     *   },
     *   "message": "Resumen de bobinas por proveedor obtenido exitosamente"
     * }
     */
    public function index(Request $request)
    {
        // Only providers present in provider_inventories table
        $query = Provider::query()
            ->join('provider_inventories', 'providers.id', '=', 'provider_inventories.provider_id')
            ->select([
                'providers.id',
                'providers.fantasy_name',
                'providers.business_name',
                'provider_inventories.coils_count',
            ])
            ->addSelect([
                'last_movement_in' => ProviderCoilMovement::select('created_at')
                    ->whereColumn('provider_id', 'providers.id')
                    ->where('coils_received', '>', 0)
                    ->orderByDesc('created_at')
                    ->limit(1),
                'last_movement_out' => ProviderCoilMovement::select('created_at')
                    ->whereColumn('provider_id', 'providers.id')
                    ->where('coils_returned', '>', 0)
                    ->orderByDesc('created_at')
                    ->limit(1),
            ]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('providers.fantasy_name', 'like', "%{$search}%")
                  ->orWhere('providers.business_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortableColumns = [
            'provider_name'     => 'providers.fantasy_name',
            'coils_count'       => 'provider_inventories.coils_count',
            'last_movement_in'  => 'last_movement_in',
            'last_movement_out' => 'last_movement_out',
        ];

        $sortBy  = $request->input('sort_by', 'provider_name');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $orderColumn = $sortableColumns[$sortBy] ?? 'providers.fantasy_name';
        $query->orderBy($orderColumn, $sortDir);

        // Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $providers = $query->paginate($perPage);

        return ProviderCoilSummaryResource::collection($providers)
            ->additional([
                'success' => true,
                'message' => 'Resumen de bobinas por proveedor obtenido exitosamente',
            ]);
    }

    /**
     * Movimientos de bobinas de un proveedor
     *
     * Listado paginado de movimientos de bobinas para un proveedor específico,
     * con opción de filtrar por rango de fechas y ordenar por columnas.
     *
     * @urlParam providerId integer required ID del proveedor. Example: 5
     * @queryParam page integer Número de página. Example: 1
     * @queryParam per_page integer Items por página (default: 15, max: 100). Example: 20
     * @queryParam date_from date Filtrar desde esta fecha (YYYY-MM-DD). Puede omitirse. Example: 2026-01-01
     * @queryParam date_to date Filtrar hasta esta fecha (YYYY-MM-DD). Puede omitirse. Example: 2026-04-14
     * @queryParam sort_by string Columna para ordenar: date, coils_received, coils_returned. Example: date
     * @queryParam sort_dir string Dirección: asc o desc (default: desc). Example: desc
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 12,
     *         "date": "2026-04-10T23:45:00+00:00",
     *         "coils_received": 10,
     *         "coils_returned": 2,
     *         "entry": {
     *           "id": 45,
     *           "entry_number": 71,
     *           "remito": "1-1925"
     *         }
     *       },
     *       {
     *         "id": 8,
     *         "date": "2026-04-05T10:30:00+00:00",
     *         "coils_received": 5,
     *         "coils_returned": 0,
     *         "entry": {
     *           "id": 38,
     *           "entry_number": 65,
     *           "remito": "1-1890"
     *         }
     *       }
     *     ],
     *     "first_page_url": "http://localhost:8000/api/providers/5/coil-movements?page=1",
     *     "from": 1,
     *     "last_page": 3,
     *     "last_page_url": "http://localhost:8000/api/providers/5/coil-movements?page=3",
     *     "next_page_url": "http://localhost:8000/api/providers/5/coil-movements?page=2",
     *     "path": "http://localhost:8000/api/providers/5/coil-movements",
     *     "per_page": 15,
     *     "prev_page_url": null,
     *     "to": 15,
     *     "total": 35
     *   },
     *   "message": "Movimientos de bobinas obtenidos exitosamente"
     * }
     *
     * @response 404 scenario="proveedor no encontrado" {
     *   "message": "No query results for model [App\\Models\\Provider] 999"
     * }
     */
    public function movements(Request $request, $providerId)
    {
        // Validate the provider exists
        Provider::findOrFail($providerId);

        $query = ProviderCoilMovement::with('entry')
            ->where('provider_id', $providerId);

        // Date range filter (either or both can be empty)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortableColumns = [
            'date'           => 'created_at',
            'coils_received' => 'coils_received',
            'coils_returned' => 'coils_returned',
        ];

        $sortBy  = $request->input('sort_by', 'date');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $orderColumn = $sortableColumns[$sortBy] ?? 'created_at';
        $query->orderBy($orderColumn, $sortDir);

        // Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $movements = $query->paginate($perPage);

        return ProviderCoilMovementResource::collection($movements)
            ->additional([
                'success' => true,
                'message' => 'Movimientos de bobinas obtenidos exitosamente',
            ]);
    }
}
