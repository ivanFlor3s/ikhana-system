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
     * List all providers with filters and pagination
     * 
     * Get a paginated list of providers with optional search and category filter.
     * 
     * @queryParam page integer Page number for pagination. Example: 1
     * @queryParam per_page integer Items per page (default: 15, max: 100). Example: 20
     * @queryParam search string Search term to filter by fantasy_name or business_name. Example: Construcción
     * @queryParam category_id integer Filter by category ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "fantasy_name": "Proveedor Demo",
     *         "business_name": "Proveedor Demo S.A.",
     *         "cuit": "20-12345678-9",
     *         "category": {
     *           "id": 1,
     *           "name": "Construcción"
     *         },
     *         "tax_status": {
     *           "id": 1,
     *           "name": "Responsable Inscripto"
     *         },
     *         "phone_1": "+54 11 1234-5678",
     *         "email_1": "contacto@proveedor.com",
     *         "created_at": "2024-11-16T10:00:00.000000Z"
     *       }
     *     ],
     *     "first_page_url": "http://localhost:8000/api/providers?page=1",
     *     "from": 1,
     *     "last_page": 3,
     *     "last_page_url": "http://localhost:8000/api/providers?page=3",
     *     "next_page_url": "http://localhost:8000/api/providers?page=2",
     *     "path": "http://localhost:8000/api/providers",
     *     "per_page": 15,
     *     "prev_page_url": null,
     *     "to": 15,
     *     "total": 45
     *   },
     *   "message": "Proveedores obtenidos exitosamente"
     * }
     * 
     * @response 200 scenario="with search" {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 3,
     *         "fantasy_name": "TechProv",
     *         "business_name": "Proveedor Tecnología SRL"
     *       }
     *     ],
     *     "total": 1
     *   },
     *   "message": "Proveedores obtenidos exitosamente"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Provider::with(['taxStatus', 'agreement', 'category', 'broker']);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fantasy_name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        $query->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 15);
        $perPage = min($perPage, 100); // Max 100 items per page
        
        $providers = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $providers,
            'message' => 'Proveedores obtenidos exitosamente'
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
     * @bodyParam tax_status_id integer optional ID de la posición frente al IVA (usar GET /api/tax-statuses para obtener opciones). Example: 1
     * @bodyParam agreement_id integer optional ID del convenio (usar GET /api/agreements para obtener opciones). Example: 1
     * @bodyParam category_id integer optional ID del rubro/categoría (usar GET /api/categories para obtener opciones). Example: 1
     * @bodyParam broker_id integer optional ID del corredor/contacto interno (usar GET /api/brokers para obtener opciones). Example: 1
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
                'tax_status_id' => 'nullable|exists:tax_statuses,id',
                'agreement_id' => 'nullable|exists:agreements,id',
                'category_id' => 'nullable|exists:categories,id',
                'broker_id' => 'nullable|exists:brokers,id',
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
                'data' => $provider->load(['taxStatus', 'agreement', 'category', 'broker']),
                'message' => 'Proveedor creado exitosamente'
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
                'message' => 'Error al crear el proveedor',
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
            $provider = Provider::with(['taxStatus', 'agreement', 'category', 'broker'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $provider,
                'message' => 'Proveedor obtenido exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
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
     * @bodyParam category_id integer optional ID del rubro/categoría (usar GET /api/categories). Example: 2
     * @bodyParam broker_id integer optional ID del corredor (usar GET /api/brokers). Example: 1
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
                'tax_status_id' => 'nullable|exists:tax_statuses,id',
                'agreement_id' => 'nullable|exists:agreements,id',
                'category_id' => 'nullable|exists:categories,id',
                'broker_id' => 'nullable|exists:brokers,id',
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
                'data' => $provider->fresh()->load(['taxStatus', 'agreement', 'category', 'broker']),
                'message' => 'Proveedor actualizado exitosamente'
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
                'message' => 'Proveedor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor',
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
                'message' => 'Proveedor eliminado exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proveedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

