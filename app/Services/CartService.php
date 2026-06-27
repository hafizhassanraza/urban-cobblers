<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getSessionId(): string
    {
        if (! session()->has('cart_session_id')) {
            session(['cart_session_id' => (string) str()->uuid()]);
        }

        return session('cart_session_id');
    }

    public function items(): Collection
    {
        $query = CartItem::with('product.category');

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->getSessionId());
        }

        return $query->get();
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->items()->sum(fn (CartItem $item) => $item->product->price * $item->quantity);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);

        if ($product->stock < $quantity) {
            abort(422, 'Not enough stock available.');
        }

        $attributes = Auth::check()
            ? ['user_id' => Auth::id(), 'product_id' => $product->id]
            : ['session_id' => $this->getSessionId(), 'product_id' => $product->id];

        $item = CartItem::where($attributes)->first();

        if ($item) {
            $newQty = min($product->stock, $item->quantity + $quantity);
            $item->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                ...$attributes,
                'quantity' => $quantity,
            ]);
        }
    }

    public function update(CartItem $item, int $quantity): void
    {
        $this->authorizeItem($item);

        if ($quantity <= 0) {
            $item->delete();

            return;
        }

        $quantity = min($item->product->stock, $quantity);
        $item->update(['quantity' => $quantity]);
    }

    public function remove(CartItem $item): void
    {
        $this->authorizeItem($item);
        $item->delete();
    }

    public function clear(): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            CartItem::where('session_id', $this->getSessionId())->delete();
        }
    }

    public function mergeGuestCart(int $userId): void
    {
        $sessionId = session('cart_session_id');

        if (! $sessionId) {
            return;
        }

        $guestItems = CartItem::with('product')
            ->where('session_id', $sessionId)
            ->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min(
                        $guestItem->product->stock,
                        $existing->quantity + $guestItem->quantity
                    ),
                ]);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }

    protected function authorizeItem(CartItem $item): void
    {
        if (Auth::check() && $item->user_id !== Auth::id()) {
            abort(403);
        }

        if (! Auth::check() && $item->session_id !== $this->getSessionId()) {
            abort(403);
        }
    }
}
