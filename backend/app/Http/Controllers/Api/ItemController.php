<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index()
    {
        try {
            $items = Item::all();
            return response()->json($items);
        } catch (\Exception $e) {
            Log::error('Error fetching items: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'shipping_type' => 'required|in:free,flat_rate,calculated',
                'shipping_cost' => 'nullable|numeric|min:0|required_if:shipping_type,flat_rate,calculated',
                'condition' => 'nullable|string|in:new,used,refurbished',
                'category' => 'nullable|string|max:100',
            ]);

            $item = Item::create($validated);
            return response()->json($item, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating item: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'quantity' => 'sometimes|integer|min:0',
                'shipping_type' => 'sometimes|in:free,flat_rate,calculated',
                'shipping_cost' => 'nullable|numeric|min:0',
                'condition' => 'nullable|string|in:new,used,refurbished',
                'category' => 'nullable|string|max:100',
            ]);

            $item->update($validated);
            return response()->json($item);
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy(Item $item)
    {
        try {
            $item->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}