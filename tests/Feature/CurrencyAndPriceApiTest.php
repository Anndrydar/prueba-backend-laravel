<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyAndPriceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    private function createCurrency(array $attributes = []): Currency
    {
        return Currency::create(array_merge([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
        ], $attributes));
    }

    private function createProduct(Currency $currency): Product
    {
        return Product::create([
            'name' => 'Laptop de prueba',
            'description' => 'Producto para pruebas automatizadas',
            'price' => 680,
            'currency_id' => $currency->id,
            'tax_cost' => 75,
            'manufacturing_cost' => 400,
        ]);
    }

    public function test_currencies_endpoint_returns_a_list(): void
    {
        $this->authenticate();

        $this->createCurrency();

        $response = $this->getJson('/api/currencies');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_currency_can_be_created(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/currencies', [
            'name' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 1.08,
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('currencies', [
            'name' => 'EUR',
            'symbol' => '€',
        ]);
    }

    public function test_product_prices_endpoint_returns_a_list(): void
    {
        $this->authenticate();

        $currency = $this->createCurrency();
        $product = $this->createProduct($currency);

        ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 680,
        ]);

        $response = $this->getJson(
            "/api/products/{$product->id}/prices"
        );

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_price_can_be_added_to_a_product(): void
    {
        $this->authenticate();

        $usd = $this->createCurrency();

        $eur = $this->createCurrency([
            'name' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 1.08,
        ]);

        $product = $this->createProduct($usd);

        $response = $this->postJson(
            "/api/products/{$product->id}/prices",
            [
                'currency_id' => $eur->id,
                'price' => 625,
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 625,
        ]);
    }



    
public function test_currency_cannot_be_created_with_invalid_data(): void
{
    $this->authenticate();

    $response = $this->postJson('/api/currencies', [
        'name' => '',
        'symbol' => '',
        'exchange_rate' => -1,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name',
            'symbol',
            'exchange_rate',
        ]);
}

public function test_price_cannot_be_created_with_nonexistent_currency(): void
{
    $this->authenticate();

    $currency = $this->createCurrency();
    $product = $this->createProduct($currency);

    $response = $this->postJson(
        "/api/products/{$product->id}/prices",
        [
            'currency_id' => 999999,
            'price' => 100,
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['currency_id']);

    $this->assertDatabaseMissing('product_prices', [
        'product_id' => $product->id,
        'currency_id' => 999999,
    ]);
}

public function test_duplicate_price_for_same_product_and_currency_is_rejected(): void
{
    $this->authenticate();

    $currency = $this->createCurrency();
    $product = $this->createProduct($currency);

    ProductPrice::create([
        'product_id' => $product->id,
        'currency_id' => $currency->id,
        'price' => 680,
    ]);

    $response = $this->postJson(
        "/api/products/{$product->id}/prices",
        [
            'currency_id' => $currency->id,
            'price' => 700,
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['currency_id']);

    $this->assertDatabaseHas('product_prices', [
        'product_id' => $product->id,
        'currency_id' => $currency->id,
        'price' => 680,
    ]);
}



public function test_currency_and_price_endpoints_reject_unauthenticated_requests(): void
{
    $currency = $this->createCurrency();
    $product = $this->createProduct($currency);

    // Consultar monedas sin login
    $this->getJson('/api/currencies')
        ->assertUnauthorized();

    // Crear una moneda sin  login
    $this->postJson('/api/currencies', [
        'name' => 'EUR',
        'symbol' => '€',
        'exchange_rate' => 1.08,
    ])->assertUnauthorized();

    // Consultar precios sin login
    $this->getJson("/api/products/{$product->id}/prices")
        ->assertUnauthorized();

    // Registrar un precio sin login
    $this->postJson("/api/products/{$product->id}/prices", [
        'currency_id' => $currency->id,
        'price' => 700,
    ])->assertUnauthorized();
}





}
