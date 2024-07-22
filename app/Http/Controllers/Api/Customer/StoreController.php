<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function index()
    {
        $stores = User::where('role', 'seller')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List Store retrieved successfully',
            'data' => $stores,
        ]);
    }

    public function productByStore($id)
    {
        $products = Product::where('seller_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List Product by Store retrieved successfully',
            'data' => $products,
        ]);
    }

    public function liveStreaming()
    {
        $stores = User::where('role', 'seller')->where('is_live_streaming', true)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List Store Livestreaming retrieved successfully',
            'data' => $stores,
        ]);
    }
}
