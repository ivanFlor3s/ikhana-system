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
    /**
     * Crear Entrada de Materia Prima
     * 
     * Registra una nueva entrada de materia prima junto con su ensayo de calidad obligatorio.
     * 
     * @bodyParam raw_material_type_id integer required ID del tipo de materia prima. Example: 1
     * @bodyParam provider_id integer required ID del proveedor. Example: 5
     * @bodyParam raw_material_characteristic_id integer required ID de la característica (ej: diámetro). Example: 3
     * @bodyParam entry_date date required Fecha de ingreso/remito. Example: 2024-09-04
     * @bodyParam remito string required Número de remito. Example: R-12345
     * @bodyParam batch string required Número de lote. Example: L-9876
     * @bodyParam quantity_kg number required Peso en Kg. Example: 1050.5
     * @bodyParam coils_count integer required Cantidad de bobinas. Example: 10
     * @bodyParam observations string optional Observaciones generales. Example: Todo en orden
     * 
     * @bodyParam test object required Datos del ensayo de calidad.
     * @bodyParam test.resistance_ohm_km number required Resistencia medida. Example: 180.5
     * @bodyParam test.check_winding boolean required Check bobinado. Example: true
     * @bodyParam test.check_cleanliness boolean required Check limpieza. Example: true
     * @bodyParam test.check_packaging boolean required Check acondicionado. Example: true
     * @bodyParam test.check_identification boolean required Check identificación. Example: true
     * @bodyParam test.conducted_by string required Nombre de quien realizó el ensayo. Example: Juan Perez
     * 
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "remito": "R-12345",
     *     "status": "approved",
     *     "test": {
     *       "result": "OK"
     *     }
     *   },
     *   "message": "Entrada y ensayo registrados exitosamente"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Datos de Entrada
            'raw_material_type_id' => 'required|exists:raw_material_types,id',
            'provider_id' => 'required|exists:providers,id',
            'raw_material_characteristic_id' => 'required|exists:raw_material_characteristics,id',
            'entry_date' => 'required|date',
            'remito' => 'required|string|max:50',
            'batch' => 'required|string|max:50',
            'quantity_kg' => 'required|numeric|min:0',
            'coils_count' => 'required|integer|min:1',
            'observations' => 'nullable|string',

            // Datos del Ensayo (Obligatorios)
            'test' => 'required|array',
            'test.resistance_ohm_km' => 'required|numeric|min:0',
            'test.check_winding' => 'required|boolean',
            'test.check_cleanliness' => 'required|boolean',
            'test.check_packaging' => 'required|boolean',
            'test.check_identification' => 'required|boolean',
            'test.conducted_by' => 'required|string|max:120',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                // 1. Validar Lógica IRAM vs Resistencia para determinar Resultado
                $characteristic = \App\Models\RawMaterialCharacteristic::with('iramCopperMaxResistance')
                    ->find($validated['raw_material_characteristic_id']);

                $rule = $characteristic->iramCopperMaxResistance;
                $resistance = $validated['test']['resistance_ohm_km'];

                $isResistanceOk = true;
                if ($rule) {
                    $isResistanceOk = $resistance <= $rule->max_resistance_ohm_km;
                }

                // El resultado global depende de la resistencia Y los checks visuales
                // Si alguno de los checks visuales es falso, el resultado es NO_OK? 
                // Asumiremos por ahora que TODO debe estar OK.
                $visualChecksOk = $validated['test']['check_winding']
                    && $validated['test']['check_cleanliness']
                    && $validated['test']['check_packaging']
                    && $validated['test']['check_identification'];

                $finalResult = ($isResistanceOk && $visualChecksOk) ? 'OK' : 'NO_OK';

                // Definir estado de la entrada basado en el ensayo
                $entryStatus = $finalResult === 'OK' ? 'approved' : 'rejected';

                // 2. Crear Entrada
                $entry = RawMaterialEntry::create([
                    'raw_material_type_id' => $validated['raw_material_type_id'],
                    'provider_id' => $validated['provider_id'],
                    'raw_material_characteristic_id' => $validated['raw_material_characteristic_id'],
                    'entry_date' => $validated['entry_date'],
                    'remito' => $validated['remito'],
                    'batch' => $validated['batch'],
                    'quantity_kg' => $validated['quantity_kg'],
                    'coils_count' => $validated['coils_count'],
                    'observations' => $validated['observations'] ?? null,
                    'status' => $entryStatus,
                ]);

                // 3. Crear Ensayo
                $entry->test()->create([
                    'test_date' => $validated['entry_date'], // Asumimos misma fecha que ingreso, o hoy? Usamos entry_date por consistencia reporte
                    'resistance_ohm_km' => $resistance,
                    'check_winding' => $validated['test']['check_winding'],
                    'check_cleanliness' => $validated['test']['check_cleanliness'],
                    'check_packaging' => $validated['test']['check_packaging'],
                    'check_identification' => $validated['test']['check_identification'],
                    'conducted_by' => $validated['test']['conducted_by'],
                    'result' => $finalResult,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $entry->load(['type', 'provider', 'characteristic', 'test']),
                    'message' => 'Entrada y ensayo registrados exitosamente'
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la entrada: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Ver Detalles de Entrada (Protocolo de Ensayo)
     * 
     * Obtiene todos los detalles de una entrada, incluyendo datos del proveedor, características,
     * valores de norma IRAM asociados y los resultados del ensayo realizado.
     * Ideal para generar el "Protocolo de Ensayo".
     * 
     * @urlParam id integer required ID de la entrada. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "remito": "1-1764",
     *     "batch": "1764",
     *     "entry_date": "2025-03-28",
     *     "quantity_kg": 1010.700,
     *     "status": "approved",
     *     "type": {
     *       "id": 1,
     *       "name": "Cobre"
     *     },
     *     "provider": {
     *       "id": 5,
     *       "business_name": "RIO BATEL TRAFILACION COBRE"
     *     },
     *     "characteristic": {
     *       "id": 2,
     *       "name": "Diámetro",
     *       "description": "0.38 mm",
     *       "decimal_value": 0.38,
     *       "unit": "mm",
     *       "iram_copper_max_resistance": {
     *         "max_resistance_ohm_km": 155.20
     *       }
     *     },
     *     "test": {
     *       "id": 10,
     *       "test_date": "2025-03-28",
     *       "conducted_by": "Juan Perez",
     *       "result": "OK",
     *       "resistance_ohm_km": 155.20,
     *       "check_winding": true,
     *       "check_cleanliness": true,
     *       "check_packaging": true,
     *       "check_identification": true
     *     }
     *   },
     *   "message": "Detalles de entrada obtenidos exitosamente"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $entry = RawMaterialEntry::with([
            'type',
            'provider',
            'characteristic.iramCopperMaxResistance', // Para mostrar valores a cumplir (IRAM)
            'test'
        ])->findOrFail($id);

        if ($entry->characteristic) {
            $entry->characteristic->append('description');
        }

        return response()->json([
            'success' => true,
            'data' => $entry,
            'message' => 'Detalles de entrada obtenidos exitosamente'
        ], 200);
    }
}
