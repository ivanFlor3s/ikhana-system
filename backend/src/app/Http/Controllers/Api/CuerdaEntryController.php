<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RawMaterialEntryResource;
use App\Models\CuerdaEntryDetail;
use App\Models\CuerdaTest;
use App\Models\RawMaterialEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Entradas de Materia Prima (Cuerda)
 *
 * APIs para gestion de ingresos de materia prima de cuerda.
 */
class CuerdaEntryController extends Controller
{
    /**
     * Listar Entradas de Cuerda
     *
     * Obtiene un listado paginado de las entradas de cuerda con opciones de filtrado.
     *
     * @queryParam page integer Numero de pagina. Example: 1
     * @queryParam per_page integer Items por pagina (default: 15). Example: 20
     * @queryParam search string Buscar por numero de remito, lote o nombre del proveedor. Example: 123456
     * @queryParam raw_material_characteristic_id integer Filtrar por caracteristica especifica (ej: Diametro 0.35mm). Example: 2
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
     *         "type": { "id": 5, "name": "Cuerda" },
     *         "provider": { "id": 5, "business_name": "Proveedor Cuerda S.A." },
     *         "characteristic": { "id": 2, "name": "Diametro", "description": "0.35 mm" }
     *       }
     *     ],
     *     "total": 50,
     *     "per_page": 15
     *   },
     *   "message": "Entradas de cuerda obtenidas exitosamente"
     * }
     */
    public function index(Request $request)
    {
        $cuerdaType = \App\Models\RawMaterialType::where('name', 'Cuerda')->firstOrFail();
        $query = RawMaterialEntry::with(['type', 'provider', 'cuerdaDetail.characteristic', 'cuerdaTest'])
            ->where('raw_material_type_id', $cuerdaType->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('remito', 'like', "%{$search}%")
                    ->orWhere('batch', is_numeric($search) ? '=' : '<>', $search)
                    ->orWhereHas('provider', function ($qProvider) use ($search) {
                        $qProvider->where('business_name', 'like', "%{$search}%")
                            ->orWhere('fantasy_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('raw_material_characteristic_id')) {
            $query->whereHas('cuerdaDetail', function ($q) use ($request) {
                $q->where('raw_material_characteristic_id', $request->raw_material_characteristic_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $query->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        $entries = $query->paginate($perPage);

        return RawMaterialEntryResource::collection($entries)
            ->additional([
                'success' => true,
                'message' => 'Entradas de cuerda obtenidas exitosamente',
            ]);
    }

    /**
     * Crear Entrada de Cuerda
     *
     * Registra una nueva entrada de cuerda junto con su ensayo de calidad obligatorio.
     * El resultado del ensayo determina si la entrada es aprobada (approved) o rechazada (rejected).
     *
     * @bodyParam provider_id integer required ID del proveedor. Example: 5
     * @bodyParam raw_material_characteristic_id integer required ID de la caracteristica (ej: diametro). Example: 3
     * @bodyParam entry_date date required Fecha de ingreso/remito. Example: 2024-09-04
     * @bodyParam remito string required Numero de remito. Example: R-12345
     * @bodyParam quantity_kg number required Peso en Kg. Example: 1050.5
     * @bodyParam coils_count integer required Cantidad de bobinas. Example: 10
     * @bodyParam returned_coils_count integer optional Cantidad de bobinas a devolver. Example: 2
     * @bodyParam observations string optional Observaciones generales.
     *
     * @bodyParam test object required Datos del ensayo de calidad.
     * @bodyParam test.resistance_ohm_km number required Resistencia medida. Example: 180.5
     * @bodyParam test.check_winding boolean required Check bobinado. Example: true
     * @bodyParam test.check_cleanliness boolean required Check limpieza. Example: true
     * @bodyParam test.check_packaging boolean required Check acondicionado. Example: true
     * @bodyParam test.check_identification boolean required Check identificacion. Example: true
     * @bodyParam test.conducted_by string required Nombre de quien realizo el ensayo. Example: Juan Perez
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "remito": "R-12345",
     *     "status": "approved",
     *     "test": { "result": "OK" }
     *   },
     *   "message": "Entrada y ensayo de cuerda registrados exitosamente"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'raw_material_characteristic_id' => 'required|exists:raw_material_characteristics,id',
            'entry_date' => 'required|date',
            'remito' => 'required|string|max:50',
            'quantity_kg' => 'required|numeric|min:0',
            'coils_count' => 'required|integer|min:1',
            'returned_coils_count' => 'nullable|integer|min:0',
            'observations' => 'nullable|string',

            'test' => 'required|array',
            'test.resistance_ohm_km' => 'required|numeric|min:0',
            'test.check_winding' => 'required|boolean',
            'test.check_cleanliness' => 'required|boolean',
            'test.check_packaging' => 'required|boolean',
            'test.check_identification' => 'required|boolean',
            'test.conducted_by' => 'required|string|max:120',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $cuerdaType = \App\Models\RawMaterialType::where('name', 'Cuerda')->firstOrFail();

                $characteristic = \App\Models\RawMaterialCharacteristic::with('iramCuerdaMaxResistance')
                    ->find($validated['raw_material_characteristic_id']);

                $rule = $characteristic->iramCuerdaMaxResistance;
                $resistance = $validated['test']['resistance_ohm_km'];

                $isResistanceOk = true;
                if ($rule) {
                    $isResistanceOk = $resistance <= $rule->max_resistance_ohm_km;
                }

                $visualChecksOk = $validated['test']['check_winding']
                    && $validated['test']['check_cleanliness']
                    && $validated['test']['check_packaging']
                    && $validated['test']['check_identification'];

                $finalResult = ($isResistanceOk && $visualChecksOk) ? 'OK' : 'NO_OK';
                $entryStatus = $finalResult === 'OK' ? 'approved' : 'rejected';

                $nextBatch = (RawMaterialEntry::max('batch') ?? 0) + 1;

                $entry = RawMaterialEntry::create([
                    'raw_material_type_id' => $cuerdaType->id,
                    'provider_id' => $validated['provider_id'],
                    'entry_date' => $validated['entry_date'],
                    'remito' => $validated['remito'],
                    'batch' => $nextBatch,
                    'observations' => $validated['observations'] ?? null,
                    'status' => $entryStatus,
                ]);

                CuerdaEntryDetail::create([
                    'raw_material_entry_id' => $entry->id,
                    'raw_material_characteristic_id' => $validated['raw_material_characteristic_id'],
                    'quantity_kg' => $validated['quantity_kg'],
                    'coils_count' => $validated['coils_count'],
                ]);

                CuerdaTest::create([
                    'raw_material_entry_id' => $entry->id,
                    'test_date' => $validated['entry_date'],
                    'resistance_ohm_km' => $resistance,
                    'check_winding' => $validated['test']['check_winding'],
                    'check_cleanliness' => $validated['test']['check_cleanliness'],
                    'check_packaging' => $validated['test']['check_packaging'],
                    'check_identification' => $validated['test']['check_identification'],
                    'conducted_by' => $validated['test']['conducted_by'],
                    'result' => $finalResult,
                ]);

                $coilsReceived = $validated['coils_count'];
                $coilsReturned = $validated['returned_coils_count'] ?? 0;

                if ($coilsReceived > 0 || $coilsReturned > 0) {
                    $inventory = \App\Models\ProviderInventory::firstOrCreate(
                        ['provider_id' => $validated['provider_id']],
                        ['coils_count' => 0]
                    );

                    $inventory->coils_count += $coilsReceived;
                    $inventory->coils_count -= $coilsReturned;
                    $inventory->save();

                    \App\Models\ProviderCoilMovement::create([
                        'provider_id' => $validated['provider_id'],
                        'raw_material_entry_id' => $entry->id,
                        'type' => 'entry',
                        'coils_received' => $coilsReceived,
                        'coils_returned' => $coilsReturned,
                    ]);
                }

                return (new RawMaterialEntryResource($entry->load(['type', 'provider', 'cuerdaDetail.characteristic', 'cuerdaTest'])))
                    ->additional([
                        'success' => true,
                        'message' => 'Entrada y ensayo de cuerda registrados exitosamente',
                    ])
                    ->response()
                    ->setStatusCode(201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al registrar la entrada: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ver Detalles de Entrada de Cuerda (Protocolo de Ensayo)
     *
     * Obtiene todos los detalles de una entrada, incluyendo datos del proveedor, caracteristicas,
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
     *     "type": { "id": 5, "name": "Cuerda" },
     *     "provider": { "id": 5, "business_name": "Proveedor Cuerda S.A." },
     *     "characteristic": {
     *       "id": 2,
     *       "name": "Diametro",
     *       "description": "0.38 mm",
     *       "decimal_value": 0.38,
     *       "unit": "mm",
     *       "iram_cuerda_max_resistance": { "max_resistance_ohm_km": 155.20 }
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
    public function show(string $id)
    {
        $entry = RawMaterialEntry::with([
            'type',
            'provider',
            'cuerdaDetail.characteristic.iramCuerdaMaxResistance',
            'cuerdaTest'
        ])->findOrFail($id);

        return (new RawMaterialEntryResource($entry))
            ->additional([
                'success' => true,
                'message' => 'Detalles de entrada obtenidos exitosamente',
            ]);
    }

    /**
     * Descargar Etiqueta de Ingreso (PDF)
     *
     * Genera una etiqueta en formato PDF para imprimir y pegar en la bobina/lote.
     * Incluye codigo de barras, datos del ingreso y resultados del ensayo.
     *
     * @urlParam id integer required ID de la entrada. Example: 1
     *
     * @response 200 Binary PDF Content
     */
    public function downloadLabel(string $id)
    {
        $entry = RawMaterialEntry::with(['type', 'provider', 'cuerdaDetail.characteristic', 'cuerdaTest'])->findOrFail($id);

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($entry->batch, $generator::TYPE_CODE_128);
        $barcodeBase64 = base64_encode($barcodeData);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.label', [
            'entry' => $entry,
            'barcode' => $barcodeBase64
        ]);

        $customPaper = [0, 0, 425.20, 226.77];
        $pdf->setPaper($customPaper, 'landscape');

        return $pdf->download("Etiqueta_Entrada_{$entry->entry_number}.pdf");
    }

    /**
     * Descargar Reporte de Ensayo (PDF)
     *
     * Genera un reporte PDF con los datos completos del ingreso y ensayo de calidad.
     * Incluye los valores medidos vs valores de norma IRAM a cumplir.
     *
     * @urlParam id integer required ID de la entrada. Example: 1
     *
     * @response 200 Binary PDF Content
     */
    public function downloadTestReport(string $id)
    {
        $entry = RawMaterialEntry::with(['type', 'provider', 'cuerdaDetail.characteristic.iramCuerdaMaxResistance', 'cuerdaTest'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.report', [
            'entry' => $entry,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Reporte_Ingreso_{$entry->entry_number}.pdf");
    }

    /**
     * Obtener ultimo numero de lote
     *
     * Devuelve el ultimo (mayor) numero de lote registrado en el sistema.
     * Util para auto-incrementar el campo batch en el frontend.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "last_batch": 42
     *   },
     *   "message": "Ultimo lote obtenido exitosamente"
     * }
     */
    public function lastBatch(): JsonResponse
    {
        $lastBatch = RawMaterialEntry::max('batch') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'last_batch' => (int) $lastBatch,
            ],
            'message' => 'Ultimo lote obtenido exitosamente',
        ]);
    }

    /**
     * Obtener diametros con resistencia IRAM maxima
     *
     * Devuelve el listado completo de diametros de cuerda con su resistencia
     * ohmica maxima segun norma IRAM asociada.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "name": "Diametro", "description": "0.30 mm", "decimal_value": "0,30", "iram_copper_ohm_max_resistance": "256,00" }
     *   ],
     *   "message": "Diametros y resistencia IRAM obtenidos exitosamente"
     * }
     */
    public function getAllDiameterAndIramOhmResistance(): JsonResponse
    {
        $cuerdaType = \App\Models\RawMaterialType::where('name', 'Cuerda')->firstOrFail();

        $diameters = \App\Models\RawMaterialCharacteristic::where('raw_material_type_id', $cuerdaType->id)
            ->where('name', 'Diametro')
            ->get();

        $diametersWithOhmResistance = $diameters->map(fn($diameter) => [
            'name' => $diameter->name,
            'description' => $diameter->description,
            'decimal_value' => number_format($diameter->decimal_value, 2, ',', '.'),
            'iram_copper_ohm_max_resistance' => number_format(
                $diameter->iramCuerdaMaxResistance?->max_resistance_ohm_km ?? 0,
                2, ',', '.'
            ),
        ]);

        return response()->json([
            'success' => true,
            'data' => $diametersWithOhmResistance,
            'message' => 'Diametros y resistencia IRAM obtenidos exitosamente'
        ]);
    }

    /**
     * Actualizar Entrada de Cuerda
     *
     * Permite editar una entrada existente junto con su ensayo. Recalcula el inventario
     * de bobinas y registra un movimiento de tipo 'correction' si las cantidades cambian.
     *
     * El proveedor no se puede modificar. Si se envia un provider_id distinto al original,
     * la solicitud sera rechazada con un error 422.
     *
     * @urlParam id integer required ID de la entrada a editar. Example: 1
     *
     * @bodyParam provider_id integer required ID del proveedor. Debe coincidir con el proveedor original de la entrada, no se permite cambiarlo. Example: 5
     * @bodyParam raw_material_characteristic_id integer required ID de la caracteristica. Example: 3
     * @bodyParam entry_date date required Fecha de ingreso. Example: 2024-09-04
     * @bodyParam remito string required Numero de remito. Example: R-12345
     * @bodyParam quantity_kg number required Peso en Kg. Example: 1050.5
     * @bodyParam coils_count integer required Cantidad de bobinas. Example: 10
     * @bodyParam returned_coils_count integer optional Cantidad de bobinas a devolver. Example: 2
     * @bodyParam observations string optional Observaciones generales.
     *
     * @bodyParam test object required Datos del ensayo de calidad.
     * @bodyParam test.resistance_ohm_km number required Resistencia medida. Example: 180.5
     * @bodyParam test.check_winding boolean required Check bobinado. Example: true
     * @bodyParam test.check_cleanliness boolean required Check limpieza. Example: true
     * @bodyParam test.check_packaging boolean required Check acondicionado. Example: true
     * @bodyParam test.check_identification boolean required Check identificacion. Example: true
     * @bodyParam test.conducted_by string required Nombre de quien realizo el ensayo. Example: Juan Perez
     *
     * @response 200 {
     *   "success": true,
     *   "data": { "id": 1, "status": "approved", "coils_count": 10, "returned_coils_count": 2 },
     *   "message": "Entrada y ensayo actualizados exitosamente"
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "No se permite cambiar el proveedor de una entrada existente."
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'provider_id' => [
                'required',
                'exists:providers,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($id) {
                    $entry = RawMaterialEntry::find($id);
                    if ($entry && $entry->provider_id != (int) $value) {
                        $fail('No se permite cambiar el proveedor de una entrada existente.');
                    }
                },
            ],
            'raw_material_characteristic_id' => 'required|exists:raw_material_characteristics,id',
            'entry_date' => 'required|date',
            'remito' => 'required|string|max:50',
            'quantity_kg' => 'required|numeric|min:0',
            'coils_count' => 'required|integer|min:1',
            'returned_coils_count' => 'nullable|integer|min:0',
            'observations' => 'nullable|string',

            'test' => 'required|array',
            'test.resistance_ohm_km' => 'required|numeric|min:0',
            'test.check_winding' => 'required|boolean',
            'test.check_cleanliness' => 'required|boolean',
            'test.check_packaging' => 'required|boolean',
            'test.check_identification' => 'required|boolean',
            'test.conducted_by' => 'required|string|max:120',
        ]);

        try {
            return DB::transaction(function () use ($validated, $id) {
                $entry = RawMaterialEntry::with(['cuerdaDetail', 'cuerdaTest'])->findOrFail($id);

                $characteristic = \App\Models\RawMaterialCharacteristic::with('iramCuerdaMaxResistance')
                    ->find($validated['raw_material_characteristic_id']);

                $rule = $characteristic->iramCuerdaMaxResistance;
                $resistance = $validated['test']['resistance_ohm_km'];

                $isResistanceOk = true;
                if ($rule) {
                    $isResistanceOk = $resistance <= $rule->max_resistance_ohm_km;
                }

                $visualChecksOk = $validated['test']['check_winding']
                    && $validated['test']['check_cleanliness']
                    && $validated['test']['check_packaging']
                    && $validated['test']['check_identification'];

                $finalResult = ($isResistanceOk && $visualChecksOk) ? 'OK' : 'NO_OK';
                $entryStatus = $finalResult === 'OK' ? 'approved' : 'rejected';

                $entry->update([
                    'entry_date' => $validated['entry_date'],
                    'remito' => $validated['remito'],
                    'observations' => $validated['observations'] ?? null,
                    'status' => $entryStatus,
                ]);

                $entry->cuerdaDetail->update([
                    'raw_material_characteristic_id' => $validated['raw_material_characteristic_id'],
                    'quantity_kg' => $validated['quantity_kg'],
                    'coils_count' => $validated['coils_count'],
                ]);

                $entry->cuerdaTest->update([
                    'test_date' => $validated['entry_date'],
                    'resistance_ohm_km' => $resistance,
                    'check_winding' => $validated['test']['check_winding'],
                    'check_cleanliness' => $validated['test']['check_cleanliness'],
                    'check_packaging' => $validated['test']['check_packaging'],
                    'check_identification' => $validated['test']['check_identification'],
                    'conducted_by' => $validated['test']['conducted_by'],
                    'result' => $finalResult,
                ]);

                $oldNet = $entry->coilMovements()->sum('coils_received')
                    - $entry->coilMovements()->sum('coils_returned');

                $newCoilsReceived = $validated['coils_count'];
                $newCoilsReturned = $validated['returned_coils_count'] ?? 0;
                $newNet = $newCoilsReceived - $newCoilsReturned;

                $delta = $newNet - $oldNet;

                if ($delta !== 0) {
                    $inventory = \App\Models\ProviderInventory::firstOrCreate(
                        ['provider_id' => $validated['provider_id']],
                        ['coils_count' => 0]
                    );

                    $inventory->coils_count += $delta;
                    $inventory->save();

                    \App\Models\ProviderCoilMovement::create([
                        'provider_id' => $validated['provider_id'],
                        'raw_material_entry_id' => $entry->id,
                        'type' => 'correction',
                        'coils_received' => max(0, $delta),
                        'coils_returned' => max(0, -$delta),
                    ]);
                }

                return (new RawMaterialEntryResource($entry->fresh(['type', 'provider', 'cuerdaDetail.characteristic', 'cuerdaTest'])))
                    ->additional([
                        'success' => true,
                        'message' => 'Entrada y ensayo actualizados exitosamente',
                    ])
                    ->response()
                    ->setStatusCode(200);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al actualizar la entrada: ' . $e->getMessage(),
            ], 500);
        }
    }
}
