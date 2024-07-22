<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer'],
            'seller_id' => ['required', 'integer'],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer'],
            'shipping_cost' => ['required', 'integer'],
            'shipping_service' => ['required', 'string'],
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $totalPrice += $item['quantity'] * $product->price;
            }

            $grandTotal = $totalPrice + $request->shipping_cost;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'seller_id' => $request->seller_id,
                'shipping_price' => $request->shipping_cost,
                'shipping_service' => $request->shipping_service,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'grand_total' => $grandTotal,
                'transaction_number' => 'TRX-' . time(),
            ]);

            foreach ($request->items as $item) {
                // $order->items()->create([
                //     'product_id' => $item['product_id'],
                //     'price' => $item['price'],
                //     'quantity' => $item['quantity'],
                // ]);
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created',
                'data' => $order,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }

    public function historyOrderBuyer(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Order history retrieved successfully',
            'data' => $orders,
        ], 200);
    }
}
