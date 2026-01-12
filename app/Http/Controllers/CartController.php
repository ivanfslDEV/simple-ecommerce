<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::query()
            ->with(['product:id,name,price,stock_quantity'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at')
            ->get();

        return Inertia::render('Cart/Index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $data['quantity'] ?? 1;
        $product = Product::query()
            ->select(['id', 'name', 'stock_quantity'])
            ->findOrFail($data['product_id']);

        $item = CartItem::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        $nextQuantity = $quantity + ($item?->quantity ?? 0);

        if ($nextQuantity > $product->stock_quantity) {
            return back()->withErrors([
                'quantity' => "Only {$product->stock_quantity} left in stock for {$product->name}.",
            ]);
        }

        if ($item) {
            $item->update(['quantity' => $nextQuantity]);
        } else {
            CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $nextQuantity,
            ]);
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->loadMissing('product:id,name,stock_quantity');

        if ($data['quantity'] > $cartItem->product->stock_quantity) {
            return back()->withErrors([
                'quantity' => "Only {$cartItem->product->stock_quantity} left in stock for {$cartItem->product->name}.",
            ]);
        }

        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            abort(404);
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed.');
    }
}
