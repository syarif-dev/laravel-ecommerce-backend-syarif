<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses;

        return response()->json([
            'status' => 'success',
            'message' => 'Addresses retrieved successfully',
            'data' => $addresses,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'country' => ['required', 'string'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'district' => ['required', 'string'],
            'postal_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'is_default' => ['required', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            // if using auto db transaction
            /*
            DB::transaction(function () use ($request) {
                $user = $request->user();
                $isDefault = $request->is_default;
                if ($user->addresses()->exists()) {
                    if ($isDefault) {
                        $user->addresses()->update([
                            'is_default' => false,
                        ]);
                    }
                } else {
                    $isDefault = true;
                }

                $address = Address::create([
                    'user_id' => $request->user()->id,
                    'country' => $request->country,
                    'province' => $request->province,
                    'city' => $request->city,
                    'district' => $request->district,
                    'postal_code' => $request->postal_code,
                    'address' => $request->address,
                    'is_default' => $request->is_default,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Address created successfully',
                    'data' => $address,
                ], 201);
            });
            */

            // using manual db transaction
            $user = $request->user();
            $isDefault = $request->is_default;
            if ($user->addresses()->exists()) {
                if ($isDefault) {
                    $user->addresses()->update([
                        'is_default' => false,
                    ]);
                }
            } else {
                $isDefault = true;
            }

            $address = Address::create([
                'user_id' => $request->user()->id,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'is_default' => $isDefault,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Address created successfully',
                'data' => $address,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $e->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'country' => ['required', 'string'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'district' => ['required', 'string'],
            'postal_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'is_default' => ['required', 'boolean'],
        ]);

        $address = Address::find($id);

        if (!$address || $address->user_id != $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found',
            ], 404);
        }

        try {
            $user = $request->user();
            $isDefault = $request->is_default;
            if ($user->addresses()->exists()) {
                if ($isDefault) {
                    $user->addresses()->update([
                        'is_default' => false,
                    ]);
                }
            } else {
                $isDefault = true;
            }

            $address->update([
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'is_default' => $request->is_default,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Address updated',
                'data' => $address,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => !app()->isProduction() ? $th->getMessage() : "Upps, something error was happen, please try again",
            ], 400);
        }
    }

    public function destroy($id)
    {
        $address = Address::find($id);
        if (!$address || $address->user_id != auth()->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found',
            ], 404);
        }
        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted',
        ], 200);
    }
}
