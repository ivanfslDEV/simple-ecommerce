<?php

namespace App\Http\Controllers;

use App\Jobs\SendLowStockNotification;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = $user
            ? CartItem::query()
                ->with(['product:id,name,price,stock_quantity'])
                ->where('user_id', $user->id)
                ->orderBy('created_at')
                ->get()
            : collect();

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

    public function checkout(Request $request)
    {
        $user = $request->user();

        $items = CartItem::query()
            ->with(['product:id,name,stock_quantity'])
            ->where('user_id', $user->id)
            ->get();

        if ($items->isEmpty()) {
            return back()->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        DB::transaction(function () use ($items, $user) {
            $products = Product::query()
                ->whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get(['id', 'name', 'stock_quantity']);

            $productMap = $products->keyBy('id');

            foreach ($items as $item) {
                $product = $productMap->get($item->product_id);
                $available = $product?->stock_quantity ?? 0;

                if (! $product || $item->quantity > $available) {
                    $name = $product?->name ?? 'Product';

                    throw ValidationException::withMessages([
                        'quantity' => "Only {$available} left in stock for {$name}.",
                    ]);
                }
            }

            $lowStockThreshold = SendLowStockNotification::DEFAULT_THRESHOLD;

            foreach ($items as $item) {
                $product = $productMap->get($item->product_id);
                $previousStock = $product->stock_quantity;

                $product->decrement('stock_quantity', $item->quantity);

                $remaining = $product->stock_quantity;

                if ($previousStock > $lowStockThreshold && $remaining <= $lowStockThreshold) {
                    SendLowStockNotification::dispatch($product, $remaining, $lowStockThreshold)
                        ->afterCommit();
                }
            }

            CartItem::query()
                ->where('user_id', $user->id)
                ->delete();
        });

        return back()->with('success', 'Purchase completed.');
    }
}
