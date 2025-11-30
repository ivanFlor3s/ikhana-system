<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Brokers Management
 * 
 * APIs for managing brokers (corredores/contactos internos)
 */
class BrokerController extends Controller
{
    /**
     * List all brokers
     * 
     * Get a list of all brokers in the system. Useful for populating select/dropdown fields.
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "first_name": "Juan",
     *       "last_name": "Pérez",
     *       "full_name": "Juan Pérez",
     *       "email": "juan.perez@example.com",
     *       "phone": "+54 11 1234-5678",
     *       "created_at": "2024-11-30T10:00:00.000000Z",
     *       "updated_at": "2024-11-30T10:00:00.000000Z",
     *       "deleted_at": null
     *     }
     *   ],
     *   "message": "Corredores obtenidos exitosamente"
     * }
     */
    public function index(): JsonResponse
    {
        $brokers = Broker::orderBy('first_name', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $brokers,
            'message' => 'Corredores obtenidos exitosamente'
        ], 200);
    }

    /**
     * Create a new broker
     * 
     * Create and store a new broker in the database.
     * 
     * @bodyParam first_name string required First name. Example: Juan
     * @bodyParam last_name string required Last name. Example: Pérez
     * @bodyParam email string required Unique email address. Example: juan.perez@example.com
     * @bodyParam phone string optional Phone number. Example: +54 11 1234-5678
     * 
     * @response 201 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "first_name": "Juan",
     *     "last_name": "Pérez",
     *     "full_name": "Juan Pérez",
     *     "email": "juan.perez@example.com",
     *     "phone": "+54 11 1234-5678",
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T10:00:00.000000Z"
     *   },
     *   "message": "Corredor creado exitosamente"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:brokers,email',
                'phone' => 'nullable|string|max:50',
            ]);

            $broker = Broker::create($validated);

            return response()->json([
                'success' => true,
                'data' => $broker,
                'message' => 'Corredor creado exitosamente'
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
                'message' => 'Error al crear el corredor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific broker
     * 
     * Get detailed information about a specific broker by its ID.
     * 
     * @urlParam id integer required Broker ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "first_name": "Juan",
     *     "last_name": "Pérez",
     *     "full_name": "Juan Pérez",
     *     "email": "juan.perez@example.com",
     *     "phone": "+54 11 1234-5678",
     *     "provider": {
     *       "id": 1,
     *       "business_name": "Proveedor Demo SA",
     *       "fantasy_name": "ProvDemo"
     *     },
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T10:00:00.000000Z"
     *   },
     *   "message": "Corredor obtenido exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Corredor no encontrado"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $broker = Broker::with('provider')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $broker,
                'message' => 'Corredor obtenido exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Corredor no encontrado'
            ], 404);
        }
    }

    /**
     * Update a broker
     * 
     * Update an existing broker's information. Only send the fields you want to update.
     * 
     * @urlParam id integer required Broker ID. Example: 1
     * @bodyParam first_name string optional First name. Example: Juan Carlos
     * @bodyParam last_name string optional Last name. Example: Pérez González
     * @bodyParam email string optional Email address. Example: juancarlos.perez@example.com
     * @bodyParam phone string optional Phone number. Example: +54 11 9999-8888
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "first_name": "Juan Carlos",
     *     "last_name": "Pérez González",
     *     "full_name": "Juan Carlos Pérez González",
     *     "email": "juancarlos.perez@example.com",
     *     "phone": "+54 11 9999-8888",
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T11:00:00.000000Z"
     *   },
     *   "message": "Corredor actualizado exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Corredor no encontrado"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $broker = Broker::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255|unique:brokers,email,' . $id,
                'phone' => 'nullable|string|max:50',
            ]);

            $broker->update($validated);

            return response()->json([
                'success' => true,
                'data' => $broker->fresh(),
                'message' => 'Corredor actualizado exitosamente'
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
                'message' => 'Corredor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el corredor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a broker
     * 
     * Delete a broker from the system (soft delete). The broker will be marked as deleted but not permanently removed.
     * If a provider is using this broker, the relationship will be set to null automatically.
     * 
     * @urlParam id integer required Broker ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Corredor eliminado exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Corredor no encontrado"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $broker = Broker::findOrFail($id);
            $broker->delete();

            return response()->json([
                'success' => true,
                'message' => 'Corredor eliminado exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Corredor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el corredor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

