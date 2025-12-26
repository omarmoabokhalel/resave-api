<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = $user->transactions()
            ->with('order')
            ->latest()
            ->get();

        $balance = $transactions->sum(function ($t) {
            return $t->type === 'earn'
                ? $t->amount
                : -$t->amount;
        });

        return response()->json([
            'balance' => $balance,
            'transactions' => $transactions
        ]);
    }
}
