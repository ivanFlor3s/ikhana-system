<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaxStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Gestión de Posiciones frente al IVA
 * 
 * APIs para gestionar las posiciones frente al IVA del sistema
 */
class TaxStatusController extends Controller
{
    /**
     * Listar todas las posiciones frente al IVA
     * 
     * Obtiene una lista de todas las posiciones frente al IVA disponibles en el sistema.
     * 
     * @queryParam is_active boolean Filtrar solo las activas (1) o inactivas (0). Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "code": "1",
     *       "name": "IVA Responsable Inscripto",
     *       "description": null,
     *       "is_active": true,
     *       "created_at": "2024-11-16T10:00:00.000000Z",
     *       "updated_at": "2024-11-16T10:00:00.000000Z"
     *     }
     *   ],
     *   "message": "Posiciones frente al IVA obtenidas exitosamente"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxStatus::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $taxStatuses = $query->orderBy('code')->get();
        
        return response()->json([
            'success' => true,
            'data' => $taxStatuses,
            'message' => 'Posiciones frente al IVA obtenidas exitosamente'
        ], 200);
    }

    /**
     * Crear una nueva posición frente al IVA
     * 
     * Crea y almacena una nueva posición frente al IVA en el sistema.
     * 
     * @bodyParam code string required Código único de la posición (ej: 1, 2, 3). Example: 15
     * @bodyParam name string required Nombre de la posición. Example: Nuevo Régimen IVA
     * @bodyParam description string optional Descripción adicional. Example: Descripción del nuevo régimen
     * @bodyParam is_active boolean optional Si está activo o no (por defecto true). Example: true
     * 
     * @response 201 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 15,
     *     "code": "15",
     *     "name": "Nuevo Régimen IVA",
     *     "description": "Descripción del nuevo régimen",
     *     "is_active": true,
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Posición frente al IVA creada exitosamente"
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
                'code' => 'required|string|max:10|unique:tax_statuses,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $taxStatus = TaxStatus::create($validated);

            return response()->json([
                'success' => true,
                'data' => $taxStatus,
                'message' => 'Posición frente al IVA creada exitosamente'
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
                'message' => 'Error al crear la posición frente al IVA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una posición frente al IVA específica
     * 
     * Obtiene información detallada de una posición frente al IVA por su ID.
     * 
     * @urlParam id integer required ID de la posición. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "code": "1",
     *     "name": "IVA Responsable Inscripto",
     *     "description": null,
     *     "is_active": true,
     *     "created_at": "2024-11-16T10:00:00.000000Z",
     *     "updated_at": "2024-11-16T10:00:00.000000Z"
     *   },
     *   "message": "Posición frente al IVA obtenida exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Posición frente al IVA no encontrada"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $taxStatus = TaxStatus::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $taxStatus,
                'message' => 'Posición frente al IVA obtenida exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Posición frente al IVA no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar una posición frente al IVA
     * 
     * Actualiza la información de una posición frente al IVA existente.
     * 
     * @urlParam id integer required ID de la posición. Example: 1
     * @bodyParam code string optional Código único. Example: 1
     * @bodyParam name string optional Nombre de la posición. Example: IVA Responsable Inscripto Actualizado
     * @bodyParam description string optional Descripción. Example: Descripción actualizada
     * @bodyParam is_active boolean optional Estado activo/inactivo. Example: false
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "code": "1",
     *     "name": "IVA Responsable Inscripto Actualizado",
     *     "description": "Descripción actualizada",
     *     "is_active": false,
     *     "updated_at": "2024-11-16T11:00:00.000000Z"
     *   },
     *   "message": "Posición frente al IVA actualizada exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Posición frente al IVA no encontrada"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $taxStatus = TaxStatus::findOrFail($id);

            $validated = $request->validate([
                'code' => 'sometimes|required|string|max:10|unique:tax_statuses,code,' . $id,
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $taxStatus->update($validated);

            return response()->json([
                'success' => true,
                'data' => $taxStatus->fresh(),
                'message' => 'Posición frente al IVA actualizada exitosamente'
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
                'message' => 'Posición frente al IVA no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la posición frente al IVA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una posición frente al IVA
     * 
     * Elimina una posición frente al IVA del sistema (soft delete).
     * 
     * @urlParam id integer required ID de la posición. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Posición frente al IVA eliminada exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Posición frente al IVA no encontrada"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $taxStatus = TaxStatus::findOrFail($id);
            $taxStatus->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posición frente al IVA eliminada exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Posición frente al IVA no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la posición frente al IVA',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

