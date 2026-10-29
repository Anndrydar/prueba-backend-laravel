<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductPriceController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $prices = $product->prices()
            ->with('currency')
            ->orderBy('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Precios del producto obtenidos correctamente.',
            'data' => $prices,
        ], 200);
    }

    public function store(
        Request $request,
        Product $product
    ): JsonResponse {
        $validated = $request->validate([
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::unique('product_prices', 'currency_id')
                    ->where('product_id', $product->id),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ]);

        $price = $product->prices()->create($validated);

        $price->load('currency');

        return response()->json([
            'success' => true,
            'message' => 'Precio del producto creado correctamente.',
            'data' => $price,
        ], 201);
    }
}
