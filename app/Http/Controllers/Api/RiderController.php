<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    // GET /api/rider/orders
    public function orders(Request $request)
    {
        $rider = $request->user();

        $orders = \App\Models\Order::where('rider_id', $rider->id)
            ->whereIn('status', ['assigned', 'pending'])
            ->with('items.item')
            ->get();

        return response()->json($orders);
    }


    // POST /api/rider/order/{order_id}/update-weight
    public function updateWeight(Request $request, $order_id)
    {
        $rider = $request->user();

        $request->validate([
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.actual_weight' => 'required|numeric|min:0',
        ]);

        $order = Order::where('id', $order_id)
            ->where('rider_id', $rider->id)
            ->with('items.item')
            ->firstOrFail();

        $totalPoints = 0;
        $order->user->points += $totalPoints;
        $order->user->updateLevel(); // يحدث المستوى تلقائي

        foreach ($request->items as $data) {
            $orderItem = $order->items
                ->where('id', $data['order_item_id'])
                ->first();

            if (! $orderItem) continue;

            $orderItem->update([
                'actual_weight' => $data['actual_weight']
            ]);

            // حساب النقاط
            if ($orderItem->item->price_type === 'kg') {
                $totalPoints += $data['actual_weight'] * $orderItem->item->price;
            } else {
                $totalPoints += $orderItem->item->price;
            }
        }

        // تحديث الطلب
        $order->update([
            'status' => 'collected',
            'total_points' => $totalPoints,
        ]);

        // تسجيل Transaction
        Transaction::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'type' => 'earn',
            'amount' => $totalPoints,
        ]);

        return response()->json([
            'message' => 'Weight updated successfully',
            'points' => $totalPoints
        ]);
    }
}
