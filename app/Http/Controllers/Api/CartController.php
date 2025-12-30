<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // GET /api/cart
    public function index()
    {
        $user = Auth::user();

        $order = Order::with('items.item')
            ->where('user_id', $user->id)
            ->where('status', 'draft')
            ->first();

        if (! $order) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Cart is empty'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart has items',
            'data' => $order
        ], 200);
    }


    // POST /api/cart/add-item
    public function addItem(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Validation
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'estimated_quantity' => 'required|numeric|min:0.1',
            'image' => 'nullable|string' // لو حابب تبعت صورة لكل item
        ]);

        // احصل على draft order أو أنشئ واحدة
        $order = Order::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'draft'
            ],
            [
                'total_quantity' => 0,
                'total_points' => 0
            ]
        );

        // جلب الـ Item
        $item = Item::findOrFail($data['item_id']);

        // تحقق لو الـ item موجود بالفعل في الكارت
        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('item_id', $item->id)
            ->first();

        try {
            if ($orderItem) {
                // لو موجود → تحديث الكمية
                $orderItem->update([
                    'estimated_quantity' => $orderItem->estimated_quantity + $data['estimated_quantity'],
                    'image' => $data['image'] ?? $orderItem->image
                ]);
            } else {
                // إنشاء جديد
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'estimated_quantity' => $data['estimated_quantity'],
                    'price' => $item->price,
                    'points_earned' => 0,
                    'image' => $data['image'] ?? null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart successfully',
            'data' => $orderItem
        ], 201);
    }



    // PUT /api/cart/update-item/{id}
    public function updateItem(Request $request, $id)
    {
        $data = $request->validate([
            'estimated_quantity' => 'required|numeric|min:0.1'
        ]);

        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($data);

        return response()->json($orderItem);
    }

    // DELETE /api/cart/remove-item/{id}
    public function removeItem($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();

        return response()->json([
            'message' => 'Item removed from cart'
        ]);
    }

    // POST /api/cart/confirm
    public function confirm(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $validated = $request->validate([
            'address' => 'required|string',
            'latitude' => 'numeric|between:-90,90|nullable',
            'longitude' => 'numeric|between:-180,180|nullable',
        ]);
        $order = $user->orders()
            ->where('status', 'draft')
            ->with('items')
            ->first();

        if (! $order || $order->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        $totalQuantity = $order->items->sum('estimated_quantity');

        $order->update([
            'status' => 'pending',
            'total_quantity' => $totalQuantity,
            'scheduled_at' => now()->addDay(),
            'address' => $validated['address'] ,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order confirmed successfully',
            'order' => $order
        ]);
    }
}
