<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_endpoint_rejects_unauthenticated_requests(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_authenticated_user_can_access_products_with_token(): void
    {
        $password = '1234';

        User::factory()->create([
            'name' => 'Admin Prueba',
            'email' => '123@gmail.com',
            'password' => $password,
        ]);

        // Iniciar sesión y obtener un token real de Sanctum.
        $loginResponse = $this->postJson('/api/login', [
            'email' => '123@gmail.com',
            'password' => $password,
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $token = $loginResponse->json('data.token');

        // Consultar productos utilizando el token.
        $response = $this->withToken($token)
            ->getJson('/api/products');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }
}
