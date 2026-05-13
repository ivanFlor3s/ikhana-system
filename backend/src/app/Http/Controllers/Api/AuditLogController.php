<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Auditoría
 * 
 * APIs para consultar logs de auditoría del sistema
 */
class AuditLogController extends Controller
{
    /**
     * Listar logs de auditoría
     * 
     * Obtiene una lista paginada de logs de auditoría con filtros opcionales.
     * 
     * @queryParam page integer Número de página para paginación. Example: 1
     * @queryParam per_page integer Cantidad de registros por página (default: 15). Example: 15
     * @queryParam user_id integer Filtrar por ID de usuario. Example: 1
     * @queryParam action string Filtrar por tipo de acción (created, updated, deleted). Example: created
     * @queryParam model_type string Filtrar por tipo de modelo (Provider, Agreement, etc.). Example: Provider
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2025-12-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2025-12-31
     * @queryParam search string Buscar en mensaje (nombre de usuario o modelo). Example: Administrador
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 1,
     *       "action": "created",
     *       "model_type": "Provider",
     *       "model_id": 5,
     *       "model_name": "Proveedor Demo S.A.",
     *       "old_values": null,
     *       "new_values": {
     *         "business_name": "Proveedor Demo S.A.",
     *         "cuit": "20-12345678-9"
     *       },
     *       "created_at": "2025-12-17T19:30:00.000000Z",
     *       "message": "Administrador realizó una alta en Proveedor: Proveedor Demo S.A.",
     *       "user": {
     *         "id": 1,
     *         "name": "Administrador",
     *         "email": "admin@ikhana.com",
     *         "role": {
     *           "id": 1,
     *           "name": "Admin"
     *         }
     *       }
     *     }
     *   ],
     *   "pagination": {
     *     "total": 100,
     *     "per_page": 15,
     *     "current_page": 1,
     *     "last_page": 7,
     *     "from": 1,
     *     "to": 15
     *   },
     *   "message": "Logs obtenidos exitosamente"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);

            $query = AuditLog::with(['user.role'])
                ->orderBy('created_at', 'desc');

            // Aplicar filtros
            if ($request->has('user_id')) {
                $query->byUser($request->user_id);
            }

            if ($request->has('action')) {
                $query->byAction($request->action);
            }

            if ($request->has('model_type')) {
                $query->byModelType($request->model_type);
            }

            if ($request->has('date_from') || $request->has('date_to')) {
                $query->byDateRange($request->date_from, $request->date_to);
            }

            if ($request->has('search')) {
                $query->search($request->search);
            }

            $logs = $query->paginate($perPage);

            // Agregar el atributo message a cada log
            $logs->getCollection()->transform(function ($log) {
                //$log->message = $log->message;
                return $log;
            });

            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                ],
                'message' => 'Logs obtenidos exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un log específico
     * 
     * Obtiene información detallada de un log de auditoría específico por su ID.
     * 
     * @urlParam id integer required ID del log. Example: 1
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "action": "updated",
     *     "model_type": "Provider",
     *     "model_id": 5,
     *     "model_name": "Proveedor Demo S.A.",
     *     "old_values": {
     *       "business_name": "Proveedor Viejo"
     *     },
     *     "new_values": {
     *       "business_name": "Proveedor Demo S.A."
     *     },
     *     "created_at": "2025-12-17T19:30:00.000000Z",
     *     "message": "Administrador realizó una modificación en Proveedor: Proveedor Demo S.A.",
     *     "user": {
     *       "id": 1,
     *       "name": "Administrador",
     *       "email": "admin@ikhana.com",
     *       "role": {
     *         "id": 1,
     *         "name": "Admin"
     *       }
     *     }
     *   },
     *   "message": "Log obtenido exitosamente"
     * }
     * 
     * @response 404 scenario="no encontrado" {
     *   "success": false,
     *   "message": "Log no encontrado"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $log = AuditLog::with(['user.role'])->findOrFail($id);

            // Agregar el atributo message
            //$log->message = $log->message;

            return response()->json([
                'success' => true,
                'data' => $log,
                'message' => 'Log obtenido exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Log no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener log',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
