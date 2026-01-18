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
}
