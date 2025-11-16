<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Provider Management
 * 
 * APIs for managing providers (suppliers/vendors)
 */
class ProviderController extends Controller
{
    /**
     * List all providers
     * 
     * Get a paginated list of all providers in the system.
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "fantasy_name": "Proveedor Demo",
     *       "business_name": "Proveedor Demo S.A.",
     *       "cuit": "20-12345678-9",
     *       "iibb": "901-123456-7",
     *       "tax_status": "Responsable Inscripto",
     *       "agreement": "Convenio Multilateral",
     *       "phone_1": "+54 11 1234-5678",
     *       "phone_2": null,
     *       "email_1": "contacto@proveedor.com",
     *       "address": "Av. Corrientes 1234, CABA",
     *       "website": "https://proveedor.com",
     *       "contact_name": "Juan Pérez",
     *       "observations": "Cliente preferencial",
     *       "business_hours_start": "09:00",
     *       "business_hours_end": "18:00",
     *       "created_at": "2024-11-16T10:00:00.000000Z",
     *       "updated_at": "2024-11-16T10:00:00.000000Z",
     *       "deleted_at": null
     *     }
     *   ],
     *   "message": "Providers retrieved successfully"
     * }
     */
    public function index(): JsonResponse
    {
        $providers = Provider::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $providers,
            'message' => 'Providers retrieved successfully'
        ], 200);
    }

    /**
     * Create a new provider
     * 
     * Store a newly created provider in the database.
     * 
     * @bodyParam business_name string required The legal business name. Example: Proveedor Demo S.A.
     * @bodyParam fantasy_name string optional The fantasy/trade name. Example: Proveedor Demo
     * @bodyParam cuit string required Unique tax identification number (CUIT). Example: 20-12345678-9
     * @bodyParam iibb string optional Provincial tax identification. Example: 901-123456-7
     * @bodyParam tax_status string optional Tax status value (use GET /api/tax-statuses to get options). Example: 1
     * @bodyParam agreement string optional Agreement value (use GET /api/agreements to get options). Example: convenio_multilateral
     * @bodyParam phone_1 string optional Primary phone number. Example: +54 11 1234-5678
     * @bodyParam phone_2 string optional Secondary phone number. Example: +54 11 8765-4321
     * @bodyParam email_1 string optional Primary email address. Example: contacto@proveedor.com
     * @bodyParam email_2 string optional Secondary email address. Example: ventas@proveedor.com
     * @bodyParam address string optional Physical address. Example: Av. Corrientes 1234, CABA
     * @bodyParam website string optional Website URL. Example: https://proveedor.com
     * @bodyParam contact_name string optional Contact person name. Example: Juan Pérez
     * @bodyParam observations string optional Additional notes. Example: Cliente preferencial
     * @bodyParam business_hours_start string optional Opening time (HH:MM). Example: 09:00
     * @bodyParam business_hours_end string optional Closing time (HH:MM). Example: 18:00
     * 
     * @response 201 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "business_name": "Proveedor Demo S.A.",
     *     "fantasy_name": "Proveedor Demo",
     *     "cuit": "20-12345678-9",
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Provider created successfully"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "cuit": ["The cuit field is required."],
     *     "business_name": ["The business name field is required."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'fantasy_name' => 'nullable|string|max:255',
                'cuit' => 'required|string|max:20|unique:providers,cuit',
                'iibb' => 'nullable|string|max:50',
                'tax_status' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14',
                'agreement' => 'nullable|in:convenio_multilateral',
                'phone_1' => 'nullable|string|max:50',
                'phone_2' => 'nullable|string|max:50',
                'phone_3' => 'nullable|string|max:50',
                'phone_4' => 'nullable|string|max:50',
                'phone_5' => 'nullable|string|max:50',
                'email_1' => 'nullable|email|max:255',
                'email_2' => 'nullable|email|max:255',
                'email_3' => 'nullable|email|max:255',
                'email_4' => 'nullable|email|max:255',
                'email_5' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'contact_name' => 'nullable|string|max:255',
                'observations' => 'nullable|string',
                'business_hours_start' => 'nullable|date_format:H:i',
                'business_hours_end' => 'nullable|date_format:H:i',
            ]);

            $provider = Provider::create($validated);

            return response()->json([
                'success' => true,
                'data' => $provider,
                'message' => 'Provider created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single provider
     * 
     * Retrieve detailed information about a specific provider by ID.
     * 
     * @urlParam id integer required The provider ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "fantasy_name": "Proveedor Demo",
     *     "business_name": "Proveedor Demo S.A.",
     *     "cuit": "20-12345678-9",
     *     "iibb": "901-123456-7",
     *     "tax_status": "Responsable Inscripto",
     *     "agreement": "Convenio Multilateral",
     *     "phone_1": "+54 11 1234-5678",
     *     "email_1": "contacto@proveedor.com",
     *     "address": "Av. Corrientes 1234, CABA",
     *     "website": "https://proveedor.com",
     *     "contact_name": "Juan Pérez",
     *     "observations": "Cliente preferencial",
     *     "business_hours_start": "09:00",
     *     "business_hours_end": "18:00",
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Provider retrieved successfully"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Provider not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $provider = Provider::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $provider,
                'message' => 'Provider retrieved successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not found'
            ], 404);
        }
    }

    /**
     * Update a provider
     * 
     * Update an existing provider's information. Only send the fields you want to update.
     * 
     * @urlParam id integer required The provider ID. Example: 1
     * @bodyParam business_name string optional The legal business name. Example: Proveedor Actualizado S.A.
     * @bodyParam fantasy_name string optional The fantasy/trade name. Example: Proveedor Actualizado
     * @bodyParam phone_2 string optional Secondary phone number. Example: +54 11 9999-8888
     * @bodyParam email_2 string optional Secondary email address. Example: nuevo@proveedor.com
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "fantasy_name": "Proveedor Actualizado",
     *     "business_name": "Proveedor Actualizado S.A.",
     *     "cuit": "20-12345678-9",
     *     "updated_at": "2024-11-16T11:00:00.000000Z"
     *   },
     *   "message": "Provider updated successfully"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Provider not found"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "email_2": ["The email 2 must be a valid email address."]
     *   }
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $provider = Provider::findOrFail($id);

            $validated = $request->validate([
                'business_name' => 'sometimes|required|string|max:255',
                'fantasy_name' => 'nullable|string|max:255',
                'cuit' => 'sometimes|required|string|max:20|unique:providers,cuit,' . $id,
                'iibb' => 'nullable|string|max:50',
                'tax_status' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14',
                'agreement' => 'nullable|in:convenio_multilateral',
                'phone_1' => 'nullable|string|max:50',
                'phone_2' => 'nullable|string|max:50',
                'phone_3' => 'nullable|string|max:50',
                'phone_4' => 'nullable|string|max:50',
                'phone_5' => 'nullable|string|max:50',
                'email_1' => 'nullable|email|max:255',
                'email_2' => 'nullable|email|max:255',
                'email_3' => 'nullable|email|max:255',
                'email_4' => 'nullable|email|max:255',
                'email_5' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'contact_name' => 'nullable|string|max:255',
                'observations' => 'nullable|string',
                'business_hours_start' => 'nullable|date_format:H:i',
                'business_hours_end' => 'nullable|date_format:H:i',
            ]);

            $provider->update($validated);

            return response()->json([
                'success' => true,
                'data' => $provider->fresh(),
                'message' => 'Provider updated successfully'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a provider
     * 
     * Soft delete a provider from the system. The provider will be marked as deleted but not permanently removed.
     * 
     * @urlParam id integer required The provider ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Provider deleted successfully"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Provider not found"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $provider = Provider::findOrFail($id);
            $provider->delete();

            return response()->json([
                'success' => true,
                'message' => 'Provider deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

