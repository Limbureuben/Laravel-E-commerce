<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function store (Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discription' => 'required|string',
            'price' => 'required|string|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|url'
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function index()
    {
        $product = Product::all();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

}
