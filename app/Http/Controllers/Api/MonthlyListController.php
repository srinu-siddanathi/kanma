<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonthlyList;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyListController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $lists = $user->monthlyLists()->with('items.product')->get();

        $lists->each(function ($list) {
            $totalPrice = $list->items->reduce(function ($carry, $item) {
                return $carry + ($item->product->price * $item->quantity);
            }, 0);
            $list->total_price = $totalPrice;
        });

        return response()->json(['data' => $lists]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2024',
        ]);

        $user = Auth::user();
        $list = $user->monthlyLists()->create($request->all());

        return response()->json($list, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $list = $user->monthlyLists()->with('items.product')->findOrFail($id);
        $totalPrice = $list->items->reduce(function ($carry, $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);
        $list->total_price = $totalPrice;
        return response()->json($list);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'month' => 'sometimes|required|integer|between:1,12',
            'year' => 'sometimes|required|integer|min:2024',
        ]);

        $user = Auth::user();
        $list = $user->monthlyLists()->findOrFail($id);
        $list->update($validatedData);

        return response()->json($list);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $list = $user->monthlyLists()->findOrFail($id);
        $list->delete();

        return response()->json(null, 204);
    }

    public function addProduct(Request $request, $listId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $user = Auth::user();
        $list = $user->monthlyLists()->findOrFail($listId);

        $item = $list->items()->create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity ?? 1,
        ]);

        return response()->json($item, 201);
    }

    public function removeProduct($listId, $productId)
    {
        $user = Auth::user();
        $list = $user->monthlyLists()->findOrFail($listId);
        $list->items()->where('product_id', $productId)->delete();

        return response()->json(null, 204);
    }

    public function updateProductQuantity(Request $request, $listId, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $list = $user->monthlyLists()->findOrFail($listId);

        $item = $list->items()->where('product_id', $productId)->firstOrFail();
        $item->update(['quantity' => $request->quantity]);

        return response()->json($item);
    }
}
