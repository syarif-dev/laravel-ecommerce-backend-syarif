<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('seller_id', $request->user()->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Categories',
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->user()->role !== 'seller') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to create category',
            ], 403);
        }

        try {
            $category = Category::create([
                'seller_id' => $request->user()->id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }

    }
}
