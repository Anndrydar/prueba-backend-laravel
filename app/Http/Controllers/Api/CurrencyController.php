<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(): JsonResponse
    {
        $currencies = Currency::query()
            ->orderBy('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Divisas obtenidas correctamente.',
            'data' => $currencies,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:10',
                'unique:currencies,name',
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
            ],
            'exchange_rate' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,6',
            ],
        ]);

        $currency = Currency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Divisa creada correctamente.',
            'data' => $currency,
        ], 201);
    }
}

