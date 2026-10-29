<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): void
{
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');
}

    public function test_products_endpoint_returns_a_list(): void
    {
        $this->authenticate();

        $currency = Currency::create([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
        ]);

        Product::create([
            'name' => 'Laptop de prueba',
            'description' => 'Producto para pruebas automatizadas',
            'price' => 650,
            'currency_id' => $currency->id,
            'tax_cost' => 75,
            'manufacturing_cost' => 400,
        ]);

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath(
                'data.data.0.name',
                'Laptop de prueba'
            );
    }

    public function test_product_can_be_created(): void
    {
        $this->authenticate();
        $currency = Currency::create([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Mouse de prueba',
            'description' => 'Mouse USB',
            'price' => 20,
            'currency_id' => $currency->id,
            'tax_cost' => 2,
            'manufacturing_cost' => 10,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Mouse de prueba');

        $this->assertDatabaseHas('products', [
            'name' => 'Mouse de prueba',
        ]);
    }


    public function test_product_cannot_be_created_with_invalid_currency(): void
{
    $this->authenticate();
    $response = $this->postJson('/api/products', [
        'name' => 'Producto inválido',
        'description' => 'Prueba de validación',
        'price' => 100,
        'currency_id' => 999999,
        'tax_cost' => 10,
        'manufacturing_cost' => 50,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['currency_id']);

    $this->assertDatabaseMissing('products', [
        'name' => 'Producto inválido',
    ]);
}

public function test_product_can_be_updated(): void
{
    $this->authenticate();
    $currency = Currency::create([
        'name' => 'USD',
        'symbol' => '$',
        'exchange_rate' => 1,
    ]);

    $product = Product::create([
        'name' => 'Laptop original',
        'description' => 'Descripción original',
        'price' => 650,
        'currency_id' => $currency->id,
        'tax_cost' => 75,
        'manufacturing_cost' => 400,
    ]);

    $response = $this->putJson(
        "/api/products/{$product->id}",
        [
            'name' => 'Laptop actualizado',
            'price' => 680,
        ]
    );

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Laptop actualizado')
        ->assertJsonPath('data.price', '680.00');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Laptop actualizado',
    ]);
}


}
