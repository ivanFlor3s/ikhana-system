<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 * 
 * APIs para autenticación de usuarios (login, logout, perfil)
 */
class AuthController extends Controller
{
    /**
     * Login de usuario
     * 
     * Autentica un usuario y crea una sesión.
     * 
     * @bodyParam email string required Email del usuario. Example: admin@ikhana.com
     * @bodyParam password string required Contraseña del usuario. Example: admin123
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Administrador",
     *     "email": "admin@ikhana.com",
     *     "role": {
     *       "id": 1,
     *       "name": "Admin",
     *       "description": "Administrador del sistema"
     *     }
     *   },
     *   "message": "Login exitoso"
     * }
     * 
     * @response 401 scenario="credenciales inválidas" {
     *   "success": false,
     *   "message": "Credenciales inválidas"
     * }
     * 
     * @response 422 scenario="error de validación" {
     *   "success": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "email": ["El campo email es requerido."],
     *     "password": ["El campo password es requerido."]
     *   }
     * }
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            $request->session()->regenerate();

            $user = Auth::user();
            $user->load('role');

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Login exitoso'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout de usuario
     * 
     * Cierra la sesión del usuario autenticado.
     * 
     * @authenticated
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "message": "Logout exitoso"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ], 200);
    }

    /**
     * Obtener usuario autenticado
     * 
     * Obtiene la información del usuario actualmente autenticado con su rol.
     * 
     * @authenticated
     * 
     * @response 200 scenario="success" {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Administrador",
     *     "email": "admin@ikhana.com",
     *     "email_verified_at": null,
     *     "role_id": 1,
     *     "created_at": "2024-12-17T12:00:00.000000Z",
     *     "updated_at": "2024-12-17T12:00:00.000000Z",
     *     "role": {
     *       "id": 1,
     *       "name": "Admin",
     *       "description": "Administrador del sistema"
     *     }
     *   },
     *   "message": "Usuario autenticado"
     * }
     * 
     * @response 401 scenario="no autenticado" {
     *   "message": "Unauthenticated."
     * }
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('role');

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Usuario autenticado'
        ], 200);
    }
}
