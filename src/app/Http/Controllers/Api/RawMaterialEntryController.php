<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Entradas de Materia Prima
 * 
 * APIs para gestión de ingresos de materia prima.
 */
class RawMaterialEntryController extends Controller
{
    /**
     * Listar Entradas
     * 
     * Obtiene un listado paginado de las entradas de materia prima con opciones de filtrado.
     * 
     * @queryParam page integer Número de página. Example: 1
     * @queryParam per_page integer Items por página (default: 15). Example: 20
     * @queryParam search string Buscar por número de remito, lote o nombre del proveedor. Example: 123456
     * @queryParam raw_material_type_id integer Filtrar por tipo de material. Example: 1
     * @queryParam raw_material_characteristic_id integer Filtrar por característica específica (ej: Diámetro 0.35mm). Example: 2
     * @queryParam date_from date Filtrar desde esta fecha de ingreso (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Filtrar hasta esta fecha de ingreso (YYYY-MM-DD). Example: 2024-12-31
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "entry_number": 71,
     *         "remito": "1-1925",
     *         "batch": "Lote25",
     *         "entry_date": "2024-09-04",
     *         "quantity_kg": 1028.000,
     *         "type": {
     *           "id": 1,
     *           "name": "Cobre"
     *         },
     *         "provider": {
     *           "id": 5,
     *           "business_name": "Proveedor Cobre S.A."
     *         },
     *         "characteristic": {
     *           "id": 2,
     *           "name": "Diámetro",
     *           "description": "0.35 mm"
     *         }
     *       }
     *     ],
     *     "total": 50,
     *     "per_page": 15
     *   },
     *   "message": "Entradas obtenidas exitosamente"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        // Eager load 'test' para mostrar los resultados del ensayo en el listado
        $query = RawMaterialEntry::with(['type', 'provider', 'characteristic', 'test']);

        // Filtro de búsqueda general
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('remito', 'like', "%{$search}%")
                    ->orWhere('batch', 'like', "%{$search}%")
                    ->orWhereHas('provider', function ($qProvider) use ($search) {
                        $qProvider->where('business_name', 'like', "%{$search}%")
                            ->orWhere('fantasy_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por Tipo de Material
        if ($request->filled('raw_material_type_id')) {
            $query->where('raw_material_type_id', $request->raw_material_type_id);
        }

        // Filtro por Característica (Diámetro, etc.)
        if ($request->filled('raw_material_characteristic_id')) {
            $query->where('raw_material_characteristic_id', $request->raw_material_characteristic_id);
        }

        // Filtro por Rango de Fechas (entry_date)
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        // Ordenamiento por defecto: más reciente primero
        $query->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        $entries = $query->paginate($perPage);

        // Agregar descripción computada a la característica en cada resultado para facilitar el front
        $entries->getCollection()->transform(function ($entry) {
            if ($entry->characteristic) {
                $entry->characteristic->append('description');
            }
            return $entry;
        });

        return response()->json([
            'success' => true,
            'data' => $entries,
            'message' => 'Entradas obtenidas exitosamente'
        ], 200);
    }
}
