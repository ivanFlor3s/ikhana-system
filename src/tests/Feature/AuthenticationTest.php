<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrador del sistema'
        ]);
    }

    /**
     * Test: Login exitoso devuelve token
     */
    public function test_login_returns_token_on_success(): void
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'email' => 'test@ikhana.com',
            'password' => bcrypt('password123'),
            'role_id' => 1
        ]);

        // Hacer login
        $response = $this->postJson('/api/login', [
            'email' => 'test@ikhana.com',
            'password' => 'password123'
        ]);

        // Verificar respuesta exitosa
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login exitoso'
            ]);

        // Verificar que el token existe y no está vacío
        $this->assertNotEmpty($response->json('data.token'));

        // Verificar que el token tiene el formato correcto (número|string)
        $this->assertMatchesRegularExpression('/^\d+\|.+$/', $response->json('data.token'));
    }

    /**
     * Test: Login con credenciales inválidas
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@ikhana.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ]);
    }

    /**
     * Test: Endpoint protegido sin token devuelve error 401
     */
    public function test_protected_endpoint_without_token_returns_unauthorized(): void
    {
        // Intentar acceder a un endpoint protegido sin token
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    /**
     * Test: Endpoint protegido con autenticación funciona
     */
    public function test_protected_endpoint_with_authentication_works(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'role_id' => 1
        ]);

        // Usar Sanctum::actingAs para simular autenticación
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email
                ]
            ]);
    }

    /**
     * Test: Acceder a providers sin autenticación devuelve 401
     */
    public function test_providers_endpoint_without_token_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/providers');

        $response->assertStatus(401);
    }

    /**
     * Test: Acceder a providers con autenticación funciona
     */
    public function test_providers_endpoint_with_authentication_works(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'role_id' => 1
        ]);

        // Simular autenticación con Sanctum
        Sanctum::actingAs($user);

        // Acceder a endpoint protegido
        $response = $this->getJson('/api/providers');

        // Debe devolver 200 (aunque la lista esté vacía)
        $response->assertStatus(200);
    }

    /**
     * Test: Verificar que se puede crear un token
     */
    public function test_user_can_create_token(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'role_id' => 1
        ]);

        // Generar token
        $token = $user->createToken('test_token');

        // Verificar que el token se creó
        $this->assertNotNull($token);
        $this->assertNotEmpty($token->plainTextToken);

        // Verificar que el token está en la base de datos
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'test_token'
        ]);
    }

    /**
     * Test: Flujo completo de login
     */
    public function test_complete_login_flow(): void
    {
        // 1. Crear usuario
        $user = User::factory()->create([
            'email' => 'flow@ikhana.com',
            'password' => bcrypt('password123'),
            'role_id' => 1
        ]);

        // 2. Login y obtener token
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'flow@ikhana.com',
            'password' => 'password123'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');
        $this->assertNotEmpty($token);

        // 3. Verificar que el token se guardó en la base de datos
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);

        // 4. Verificar que el usuario puede acceder a endpoints protegidos
        // (usando Sanctum::actingAs para simular el uso del token)
        Sanctum::actingAs($user);

        $meResponse = $this->getJson('/api/me');
        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'flow@ikhana.com'
                ]
            ]);
    }
}
