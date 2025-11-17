<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Gestión de Proveedores
 * 
 * APIs para gestionar proveedores (suppliers/vendors)
 */
class ProviderController extends Controller
{
    /**
     * Listar todos los proveedores
     * 
     * Obtiene una lista de todos los proveedores del sistema.
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
     *   "message": "Proveedores obtenidos exitosamente"
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
     * Crear un nuevo proveedor
     * 
     * Crea y almacena un nuevo proveedor en la base de datos.
     * 
     * @bodyParam business_name string required Razón social del proveedor. Example: Proveedor Demo S.A.
     * @bodyParam fantasy_name string optional Nombre de fantasía. Example: Proveedor Demo
     * @bodyParam cuit string required CUIT único (número de identificación tributaria). Example: 20-12345678-9
     * @bodyParam iibb string optional Ingresos Brutos. Example: 901-123456-7
     * @bodyParam tax_status string optional Posición frente al IVA (usar GET /api/tax-statuses para obtener opciones). Example: 1
     * @bodyParam agreement string optional Convenio (usar GET /api/agreements para obtener opciones). Example: convenio_multilateral
     * @bodyParam phone_1 string optional Teléfono principal. Example: +54 11 1234-5678
     * @bodyParam phone_2 string optional Teléfono secundario. Example: +54 11 8765-4321
     * @bodyParam email_1 string optional Email principal. Example: contacto@proveedor.com
     * @bodyParam email_2 string optional Email secundario. Example: ventas@proveedor.com
     * @bodyParam address string optional Dirección física. Example: Av. Corrientes 1234, CABA
     * @bodyParam website string optional Sitio web. Example: https://proveedor.com
     * @bodyParam contact_name string optional Nombre de contacto. Example: Juan Pérez
     * @bodyParam observations string optional Observaciones adicionales. Example: Cliente preferencial
     * @bodyParam business_hours_start string optional Horario de apertura (HH:MM). Example: 09:00
     * @bodyParam business_hours_end string optional Horario de cierre (HH:MM). Example: 18:00
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
     *   "message": "Proveedor creado exitosamente"
     * }
     * 
     * @response 422 scenario="error de validación" {
     *   "success": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "cuit": ["El campo CUIT es requerido."],
     *     "business_name": ["El campo razón social es requerido."]
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
     * Obtener un proveedor específico
     * 
     * Obtiene información detallada de un proveedor específico por su ID.
     * 
     * @urlParam id integer required ID del proveedor. Example: 1
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
     *   "message": "Proveedor obtenido exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Proveedor no encontrado"
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
     * Actualizar un proveedor
     * 
     * Actualiza la información de un proveedor existente. Solo envía los campos que deseas actualizar.
     * 
     * @urlParam id integer required ID del proveedor. Example: 1
     * @bodyParam business_name string optional Razón social. Example: Proveedor Actualizado S.A.
     * @bodyParam fantasy_name string optional Nombre de fantasía. Example: Proveedor Actualizado
     * @bodyParam phone_2 string optional Teléfono secundario. Example: +54 11 9999-8888
     * @bodyParam email_2 string optional Email secundario. Example: nuevo@proveedor.com
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
     *   "message": "Proveedor actualizado exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Proveedor no encontrado"
     * }
     * 
     * @response 422 scenario="error de validación" {
     *   "success": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "email_2": ["El email 2 debe ser una dirección de correo válida."]
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
     * Eliminar un proveedor
     * 
     * Elimina un proveedor del sistema (soft delete). El proveedor será marcado como eliminado pero no se borrará permanentemente.
     * 
     * @urlParam id integer required ID del proveedor. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Proveedor eliminado exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Proveedor no encontrado"
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

