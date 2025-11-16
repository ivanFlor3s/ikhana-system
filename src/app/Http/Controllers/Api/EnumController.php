<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\TaxStatus;
use App\Enums\Agreement;
use Illuminate\Http\JsonResponse;

/**
 * @group System Enums
 * 
 * APIs for getting system enumerations and select options
 */
class EnumController extends Controller
{
    /**
     * Get tax status options
     * 
     * Get all available tax status options for the "Posición frente al IVA" field.
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
     *   "message": "Tax status options retrieved successfully"
     * }
     */
    public function getTaxStatuses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => TaxStatus::toArray(),
            'message' => 'Tax status options retrieved successfully'
        ], 200);
    }

    /**
     * Get agreement options
     * 
     * Get all available agreement options for the "Convenio" field.
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "value": "convenio_multilateral",
     *       "label": "Convenio Multilateral"
     *     }
     *   ],
     *   "message": "Agreement options retrieved successfully"
     * }
     */
    public function getAgreements(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Agreement::toArray(),
            'message' => 'Agreement options retrieved successfully'
        ], 200);
    }
}

