<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AfipPadronService;
use Illuminate\Http\Request;

/**
 * @group AFIP Integration
 * 
 * APIs for integrating with AFIP (Administración Federal de Ingresos Públicos).
 */
class TaxIdValidationController extends Controller
{
    public function __construct(
        protected AfipPadronService $afipPadronService
    ) {}

    /**
     * Validate Tax ID (CUIT/CUIL)
     * 
     * Validates if a CUIT/CUIL exists in AFIP's taxpayer registry.
     * This endpoint verifies both the format and existence of the tax ID in AFIP's database.
     * 
     * @bodyParam tax_id string required The CUIT/CUIL to validate. Can be with or without hyphens. Example: 20-41173228-3
     * 
     * @response 200 {
     *   "tax_id": "20-41173228-3",
     *   "exists": true
     * }
     * 
     * @response 200 {
     *   "tax_id": "20-99999999-9",
     *   "exists": false
     * }
     * 
     * @response 422 {
     *   "message": "The tax id field is required.",
     *   "errors": {
     *     "tax_id": [
     *       "The tax id field is required."
     *     ]
     *   }
     * }
     */
    public function validate(Request $request)
    {
        $request->validate([
            'tax_id' => ['required', 'string'],
        ]);

        $taxId = $request->input('tax_id');

        $exists = $this->afipPadronService->taxIdExists($taxId);

        return response()->json([
            'tax_id' => $taxId,
            'exists' => $exists,
        ]);
    }
}
