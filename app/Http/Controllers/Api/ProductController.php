<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with(['currency', 'prices.currency'])
            ->orderBy('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Productos obtenidos correctamente.',
            'data' => $products,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
            ],
            'tax_cost' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'manufacturing_cost' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ]);

        $product = Product::create($validated);

        $product->load(['currency', 'prices.currency']);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente.',
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['currency', 'prices.currency']);

        return response()->json([
            'success' => true,
            'message' => 'Producto obtenido correctamente.',
            'data' => $product,
        ], 200);
    }

    public function update(
        Request $request,
        Product $product
    ): JsonResponse {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'required', 'string'],
            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'currency_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:currencies,id',
            ],
            'tax_cost' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'manufacturing_cost' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ]);

        $product->update($validated);

        $product->load(['currency', 'prices.currency']);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente.',
            'data' => $product,
        ], 200);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.',
        ], 200);
    }
}
