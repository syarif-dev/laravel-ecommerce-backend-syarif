<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('seller')
            ->where('seller_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required'],
            'stock' => ['required', 'integer'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        try {
            $image = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('assets/product/' . $request->user()->id, 'public');
            }

            $product = Product::create([
                'seller_id' => $request->user()->id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $image,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('seller_id', $request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required'],
            'stock' => ['required', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        try {
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('assets/product/' . $request->user()->id, 'public');
                $product->image = $image;
                $product->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }

    public function destroy($id)
    {
        $product = Product::where('seller_id', auth()->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }
}
