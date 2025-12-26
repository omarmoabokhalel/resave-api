<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Transaction;

class AdminController extends Controller
{
    // كل الطلبات
    public function allOrders()
    {
        return Order::with('user', 'rider', 'items.item')
            ->latest()
            ->get();
    }

    // الطلبات حسب الحالة
    public function ordersByStatus($status)
    {
        return Order::where('status', $status)
            ->with('user', 'rider')
            ->get();
    }

    // كل الرايدرز
    public function allRiders()
    {
        return Rider::all();
    }

    // تغيير حالة الطلب
    public function updateOrderStatus(Request $request, $order_id)
    {
        $request->validate([
            'status' => 'required|in:assigned,collected,delivered,cancelled'
        ]);

        $order = Order::findOrFail($order_id);
        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order
        ]);
    }

    // Analytics
    public function analytics()
    {
        return response()->json([
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_points' => Transaction::where('type', 'earn')->sum('amount'),
            'total_users' => \App\Models\User::count(),
        ]);
    }
}
