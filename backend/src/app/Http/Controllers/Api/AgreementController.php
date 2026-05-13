<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Gestión de Convenios
 * 
 * APIs para gestionar los convenios del sistema
 */
class AgreementController extends Controller
{
    /**
     * Listar todos los convenios
     * 
     * Obtiene una lista de todos los convenios disponibles en el sistema.
     * 
     * @queryParam is_active boolean Filtrar solo los activos (1) o inactivos (0). Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "code": "convenio_multilateral",
     *       "name": "Convenio Multilateral",
     *       "description": null,
     *       "is_active": true,
     *       "created_at": "2024-11-16T10:00:00.000000Z",
     *       "updated_at": "2024-11-16T10:00:00.000000Z"
     *     }
     *   ],
     *   "message": "Convenios obtenidos exitosamente"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Agreement::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $agreements = $query->orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $agreements,
            'message' => 'Convenios obtenidos exitosamente'
        ], 200);
    }

    /**
     * Crear un nuevo convenio
     * 
     * Crea y almacena un nuevo convenio en el sistema.
     * 
     * @bodyParam code string required Código único del convenio. Example: convenio_bilateral
     * @bodyParam name string required Nombre del convenio. Example: Convenio Bilateral
     * @bodyParam description string optional Descripción adicional. Example: Convenio entre dos jurisdicciones
     * @bodyParam is_active boolean optional Si está activo o no (por defecto true). Example: true
     * 
     * @response 201 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 2,
     *     "code": "convenio_bilateral",
     *     "name": "Convenio Bilateral",
     *     "description": "Convenio entre dos jurisdicciones",
     *     "is_active": true,
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Convenio creado exitosamente"
     * }
     * 
     * @response 422 scenario="error de validación" {
     *   "success": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "code": ["El código ya existe."],
     *     "name": ["El nombre es requerido."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:agreements,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $agreement = Agreement::create($validated);

            return response()->json([
                'success' => true,
                'data' => $agreement,
                'message' => 'Convenio creado exitosamente'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el convenio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un convenio específico
     * 
     * Obtiene información detallada de un convenio por su ID.
     * 
     * @urlParam id integer required ID del convenio. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "code": "convenio_multilateral",
     *     "name": "Convenio Multilateral",
     *     "description": null,
     *     "is_active": true,
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Convenio obtenido exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Convenio no encontrado"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $agreement = Agreement::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $agreement,
                'message' => 'Convenio obtenido exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Convenio no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un convenio
     * 
     * Actualiza la información de un convenio existente.
     * 
     * @urlParam id integer required ID del convenio. Example: 1
     * @bodyParam code string optional Código único. Example: convenio_multilateral
     * @bodyParam name string optional Nombre del convenio. Example: Convenio Multilateral Actualizado
     * @bodyParam description string optional Descripción. Example: Descripción actualizada
     * @bodyParam is_active boolean optional Estado activo/inactivo. Example: false
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "code": "convenio_multilateral",
     *     "name": "Convenio Multilateral Actualizado",
     *     "description": "Descripción actualizada",
     *     "is_active": false,
     *     "updated_at": "2024-11-16T11:00:00.000000Z"
     *   },
     *   "message": "Convenio actualizado exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Convenio no encontrado"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $agreement = Agreement::findOrFail($id);

            $validated = $request->validate([
                'code' => 'sometimes|required|string|max:50|unique:agreements,code,' . $id,
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $agreement->update($validated);

            return response()->json([
                'success' => true,
                'data' => $agreement->fresh(),
                'message' => 'Convenio actualizado exitosamente'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Convenio no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el convenio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un convenio
     * 
     * Elimina un convenio del sistema (soft delete).
     * 
     * @urlParam id integer required ID del convenio. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Convenio eliminado exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Convenio no encontrado"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $agreement = Agreement::findOrFail($id);
            $agreement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Convenio eliminado exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Convenio no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el convenio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

