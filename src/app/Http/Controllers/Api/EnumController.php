<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\TaxStatus;
use App\Enums\Agreement;
use Illuminate\Http\JsonResponse;

/**
 * @group Opciones del Sistema
 * 
 * APIs para obtener enumeraciones y opciones para campos select
 */
class EnumController extends Controller
{
    /**
     * Obtener opciones de posición frente al IVA
     * 
     * Obtiene todas las opciones disponibles para el campo "Posición frente al IVA".
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "value": "1",
     *       "label": "IVA Responsable Inscripto"
     *     },
     *     {
     *       "value": "2",
     *       "label": "IVA Responsable no Inscripto"
     *     },
     *     {
     *       "value": "3",
     *       "label": "IVA no Responsable"
     *     }
     *   ],
     *   "message": "Opciones de posición frente al IVA obtenidas exitosamente"
     * }
     */
    public function getTaxStatuses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => TaxStatus::toArray(),
            'message' => 'Opciones de posición frente al IVA obtenidas exitosamente'
        ], 200);
    }

    /**
     * Obtener opciones de convenio
     * 
     * Obtiene todas las opciones disponibles para el campo "Convenio".
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "value": "convenio_multilateral",
     *       "label": "Convenio Multilateral"
     *     }
     *   ],
     *   "message": "Opciones de convenio obtenidas exitosamente"
     * }
     */
    public function getAgreements(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Agreement::toArray(),
            'message' => 'Opciones de convenio obtenidas exitosamente'
        ], 200);
    }
}

