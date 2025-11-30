<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Business Categories
 * 
 * APIs for managing business categories (rubros)
 */
class CategoryController extends Controller
{
    /**
     * List all categories
     * 
     * Get a list of all business categories in the system.
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Construcción",
     *       "description": "Materiales y servicios de construcción",
     *       "created_at": "2024-11-30T10:00:00.000000Z",
     *       "updated_at": "2024-11-30T10:00:00.000000Z",
     *       "deleted_at": null
     *     },
     *     {
     *       "id": 2,
     *       "name": "Tecnología",
     *       "description": "Equipos y servicios tecnológicos",
     *       "created_at": "2024-11-30T10:00:00.000000Z",
     *       "updated_at": "2024-11-30T10:00:00.000000Z",
     *       "deleted_at": null
     *     }
     *   ],
     *   "message": "Categorías obtenidas exitosamente"
     * }
     */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categorías obtenidas exitosamente'
        ], 200);
    }

    /**
     * Create a new category
     * 
     * Create and store a new business category in the database.
     * 
     * @bodyParam name string required Category name. Must be unique. Example: Construcción
     * @bodyParam description string optional Category description. Example: Materiales y servicios de construcción
     * 
     * @response 201 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Construcción",
     *     "description": "Materiales y servicios de construcción",
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T10:00:00.000000Z"
     *   },
     *   "message": "Categoría creada exitosamente"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "name": ["The name field is required."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría creada exitosamente'
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
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific category
     * 
     * Get detailed information about a specific category by its ID.
     * 
     * @urlParam id integer required Category ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Construcción",
     *     "description": "Materiales y servicios de construcción",
     *     "providers_count": 5,
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T10:00:00.000000Z"
     *   },
     *   "message": "Categoría obtenida exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Categoría no encontrada"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::withCount('providers')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría obtenida exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }
    }

    /**
     * Update a category
     * 
     * Update an existing category's information. Only send the fields you want to update.
     * 
     * @urlParam id integer required Category ID. Example: 1
     * @bodyParam name string optional Category name. Example: Construcción y Arquitectura
     * @bodyParam description string optional Category description. Example: Materiales, servicios de construcción y arquitectura
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Construcción y Arquitectura",
     *     "description": "Materiales, servicios de construcción y arquitectura",
     *     "created_at": "2024-11-30T10:00:00.000000Z",
     *     "updated_at": "2024-11-30T11:00:00.000000Z"
     *   },
     *   "message": "Categoría actualizada exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Categoría no encontrada"
     * }
     * 
     * @response 422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "name": ["The name has already been taken."]
     *   }
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(),
                'message' => 'Categoría actualizada exitosamente'
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
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a category
     * 
     * Delete a category from the system (soft delete). The category will be marked as deleted but not permanently removed.
     * 
     * @urlParam id integer required Category ID. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Categoría eliminada exitosamente"
     * }
     * 
     * @response 404 scenario="not found" {
     *   "success": false,
     *   "message": "Categoría no encontrada"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

