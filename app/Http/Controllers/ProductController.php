<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Store a post request to the Products table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $product = Product::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'price' => $request->price
        ]);

        return response()->json(new ProductResource($product), 201);
    }

    /**
     * Show a product from the Product DB
     *
     * @param int $id, product Id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
    
        return response()->json(new ProductResource($product));
    }

    /**
     * Update a product already in the Product DB
     *
     * @param Request $request the updated product data
     * @param integer $id the ProductId of the product to be updated
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'price' => $request->price,
        ]);
    
        return response()->json(new ProductResource($product));
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([], 204);
    }
}
