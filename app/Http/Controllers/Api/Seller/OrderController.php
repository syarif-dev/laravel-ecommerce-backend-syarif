<?php

namespace App\Http\Controllers\Api\Seller;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function updateShippingNumber(Request $request, $id)
    {
        $request->validate([
            'shipping_number' => 'required|string',
        ]);

        $order = Order::find($id);
        $order->update([
            'shipping_number' => $request->shipping_number,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping number updated',
            'data' => $order,
        ]);
    }

    public function historyOrderSeller(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('seller_id', $user->id)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List History Order Seller',
            'data' => $orders,
        ]);
    }
}
