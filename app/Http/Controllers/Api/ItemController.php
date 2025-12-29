<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // GET /api/items
    public function index()
    {
        return response()->json([
            "items" => Item::where('is_active', true)->get()
        ]);
    }

    // POST /api/items
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'pricing_type'  => 'required|in:kg,piece',
            'price'         => 'required|numeric|min:0',
            'image'         => 'string',
            'category'      => 'required|string'
        ]);

        $item = Item::create($data);

        return response()->json($item, 201);
    }
}
