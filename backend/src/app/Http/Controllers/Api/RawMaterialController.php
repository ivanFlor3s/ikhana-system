<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Materias Primas
 * 
 * APIs para gestión de catálogo de materias primas.
 */
class RawMaterialController extends Controller
{
    /**
     * Listar Tipos de Materia Prima
     * 
     * Obtiene el listado de todos los tipos de materia prima disponibles (ej: Cobre, PVC, Master, Varios).
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Cobre"
     *     },
     *     {
     *       "id": 2,
     *       "name": "PVC"
     *     }
     *   ],
     *   "message": "Tipos de materia prima obtenidos exitosamente"
     * }
     */
    public function indexTypes(): JsonResponse
    {
        $types = RawMaterialType::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'data' => $types,
            'message' => 'Tipos de materia prima obtenidos exitosamente'
        ], 200);
    }

    /**
     * Listar Características por Tipo
     * 
     * Obtiene las características configuradas para un tipo de materia prima específico (ej: Diámetros para Cobre).
     * 
     * @urlParam type_id integer required ID del tipo de materia prima. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Diámetro",
     *       "description": "0.30 mm",
     *       "decimal_value": 0.30,
     *       "unit": "mm"
     *     }
     *   ],
     *   "message": "Características obtenidas exitosamente"
     * }
     */
    public function indexCharacteristics(string $typeId): JsonResponse
    {
        $characteristics = RawMaterialType::findOrFail($typeId)
            ->characteristics()
            ->select('id', 'name', 'decimal_value', 'text_value', 'unit')
            ->get()
            ->each(function ($char) {
                $char->append('description');
            });

        return response()->json([
            'success' => true,
            'data' => $characteristics,
            'message' => 'Características obtenidas exitosamente'
        ], 200);
    }

    /**
     * Validar Resistencia vs Norma IRAM
     * 
     * Valida si un valor de resistencia medido cumple con la norma IRAM para una característica específica (ej: diámetro).
     * 
     * @bodyParam raw_material_characteristic_id integer required ID de la característica. Example: 1
     * @bodyParam resistance_ohm_km number required Valor medido en Ohm/km. Example: 180.5
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "valid": true,
     *     "max_allowed": 184.60,
     *     "measured": 180.5,
     *     "has_rule": true
     *   },
     *   "message": "Valor dentro de la norma"
     * }
     * 
     * @response 200 scenario="invalid" {
     *   "success": true,
     *   "data": {
     *     "valid": false,
     *     "max_allowed": 184.60,
     *     "measured": 190.0,
     *     "has_rule": true
     *   },
     *   "message": "Valor excede el máximo permitido por norma IRAM"
     * }
     */
    public function validateResistance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'raw_material_characteristic_id' => 'required|exists:raw_material_characteristics,id',
            'resistance_ohm_km' => 'required|numeric|min:0',
            'raw_material_type_id' => 'nullable|exists:raw_material_types,id',
        ]);

        $characteristic = \App\Models\RawMaterialCharacteristic::findOrFail(
            $validated['raw_material_characteristic_id']
        );

        $type = null;
        if ($request->filled('raw_material_type_id')) {
            $type = RawMaterialType::find($validated['raw_material_type_id']);
        } else {
            $type = $characteristic->type;
        }

        $rule = null;
        if ($type && $type->name === 'Cuerda') {
            $rule = $characteristic->iramCuerdaMaxResistance;
        } else {
            $rule = $characteristic->iramCopperMaxResistance;
        }

        if (!$rule) {
            return response()->json([
                'success' => true,
                'data' => [
                    'valid' => true,
                    'max_allowed' => null,
                    'measured' => $validated['resistance_ohm_km'],
                    'has_rule' => false
                ],
                'message' => 'No hay regla IRAM asociada a esta característica'
            ]);
        }

        $isValid = $validated['resistance_ohm_km'] <= $rule->max_resistance_ohm_km;

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => $isValid,
                'max_allowed' => (float) $rule->max_resistance_ohm_km,
                'measured' => (float) $validated['resistance_ohm_km'],
                'has_rule' => true
            ],
            'message' => $isValid ? 'Valor dentro de la norma' : 'Valor excede el máximo permitido por norma IRAM'
        ]);
    }

    /**
     * Obtiene todos los diámetros con su resistencia IRAM
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "name": "Diámetro",
     *       "description": "0.30 mm",
     *       "decimal_value": "0,30",
     *       "iram_copper_ohm_max_resistance": "184,60"
     *     }
     *   ],
     *   "message": "Diámetros y resistencia IRAM obtenidos exitosamente"
     * }
     */
    public function getAllDiameterAndIramOhmResistance(Request $request): JsonResponse
    {
        $typeId = $request->input('raw_material_type_id');

        if (!$typeId) {
            $cobreType = \App\Models\RawMaterialType::where('name', 'Cobre')->first();
            $typeId = $cobreType?->id;
        }

        $query = \App\Models\RawMaterialCharacteristic::where('name', 'Diametro')
            ->where('raw_material_type_id', $typeId);

        $diameters = $query->get();

        $diametersWithOhmResistance = $diameters->map(function ($diameter) {
            $type = $diameter->type;
            $iramMax = null;

            if ($type && $type->name === 'Cuerda') {
                $iramMax = $diameter->iramCuerdaMaxResistance?->max_resistance_ohm_km;
            } else {
                $iramMax = $diameter->iramCopperMaxResistance?->max_resistance_ohm_km;
            }

            return [
                'name' => $diameter->name,
                'description' => $diameter->description,
                'decimal_value' => number_format($diameter->decimal_value, 2, ',', '.'),
                'iram_copper_ohm_max_resistance' => number_format($iramMax ?? 0, 2, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $diametersWithOhmResistance,
            'message' => 'Diámetros y resistencia IRAM obtenidos exitosamente'
        ]);
    }
}
