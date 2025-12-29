<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    /**
     * الطلبات المتاحة للرايدر
     * status = pending
     * rider_id = null
     */
    public function orders()
    {
        $rider = Auth::guard('rider')->user();

        $orders = Order::where('status', ['pending', 'assigned', 'collected', 'delivered'])
            ->whereNull('rider_id')
            ->with(['items.item', 'user'])
            ->get();

        return response()->json($orders);
    }

    /**
     * قبول طلب
     */
    public function acceptOrder($order_id)
    {
        $rider = Auth::guard('rider')->user();

        $order = Order::where('id', $order_id)
            ->where('status', 'pending')
            ->whereNull('rider_id')
            ->firstOrFail();

        $order->update([
            'rider_id' => $rider->id,
            'status'   => 'assigned',
        ]);

        // الرايدر بقى مش متاح مؤقتًا
        $rider->update(['is_available' => false]);

        return response()->json([
            'message' => 'تم قبول الطلب بنجاح',
            'order'   => $order
        ]);
    }

    /**
     * تفاصيل طلب
     */
    public function orderDetails($order_id)
    {
        $rider = Auth::guard('rider')->user();

        $order = Order::with(['items.item', 'user'])
            ->where('id', $order_id)
            ->where('rider_id', $rider->id)
            ->firstOrFail();

        return response()->json($order);
    }

    /**
     * إدخال الوزن الفعلي
     */
    public function updateWeight(Request $request, $order_id)
{
    $rider = Auth::guard('rider')->user();

    $data = $request->validate([
        'items' => 'required|array',
        'items.*.order_item_id'    => 'required|exists:order_items,id',
        'items.*.actual_quantity' => 'required|numeric|min:0.1',
    ]);

    $order = Order::where('id', $order_id)
        ->where('rider_id', $rider->id)
        ->firstOrFail();

    $totalQuantity = 0;
    $totalPoints   = 0;

    foreach ($data['items'] as $row) {

        $orderItem = OrderItem::where('id', $row['order_item_id'])
            ->where('order_id', $order->id)
            ->firstOrFail();

        // ✅ حساب النقاط = الكمية × سعر المنتج
        $points = round($row['actual_quantity'] * $orderItem->price);

        $orderItem->update([
            'actual_quantity' => $row['actual_quantity'],
            'points_earned'   => $points,
        ]);

        $totalQuantity += $row['actual_quantity'];
        $totalPoints   += $points;
    }

    $order->update([
        'total_quantity' => $totalQuantity,
        'total_points'   => $totalPoints,
        'status'         => 'collected',
    ]);

    return response()->json([
        'message' => 'تم تسجيل الوزن بنجاح',
        'order'   => $order
    ]);
}


    /**
     * إنهاء الطلب
     */
    public function completeOrder($order_id)
    {
        $rider = Auth::guard('rider')->user();

        $order = Order::where('id', $order_id)
            ->where('rider_id', $rider->id)
            ->where('status', 'collected')
            ->firstOrFail();

        $order->update(['status' => 'delivered']);

        // إضافة نقاط للمستخدم
        $order->user->increment('points', $order->total_points);

        // الرايدر رجع متاح
        $rider->update(['is_available' => true]);

        return response()->json([
            'message' => 'تم إنهاء الطلب بنجاح'
        ]);
    }

    /**
     * الطلبات الحالية للرايدر
     */
    public function myOrders()
    {
        $rider = Auth::guard('rider')->user();

        $orders = Order::where('rider_id', $rider->id)
            ->whereIn('status', ['assigned', 'collected'])
            ->with('items.item')
            ->get();

        return response()->json($orders);
    }

    /**
     * سجل الطلبات المكتملة
     */
    public function history()
    {
        $rider = Auth::guard('rider')->user();

        $orders = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->with('items.item')
            ->get();

        return response()->json($orders);
    }
}
