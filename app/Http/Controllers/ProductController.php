<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * List of all products
     */
    public function index(): ProductCollection
    {
        return new ProductCollection(Product::paginate());
    }

    /**
     * Store a post request to the Products table
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validate($request, $this->criteria());

        $data['slug'] = Str::slug($data['name']);

        $product = Product::create($data);

        return response()->json(new ProductResource($product), 201);
    }

    /**
     * Show a product from the Product DB
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
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validate($request, $this->criteria());

        $product = Product::findOrFail($id);

        $product->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'price' => $request->price,
        ]);

        return response()->json(new ProductResource($product));
    }

    /**
     * Delete a product already in the Product DB
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([], 204);
    }

    private function criteria(): array
    {
        return [
            'name'  => 'required|unique:products|max:255',
            'price' => 'required|integer',
        ];
    }
}
