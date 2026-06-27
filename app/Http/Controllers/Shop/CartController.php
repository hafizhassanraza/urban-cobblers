<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(): Response
    {
        $items = $this->cart->items();

        return Inertia::render('Shop/Cart', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $this->cart->add($product, $validated['quantity'] ?? 1);

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->cart->update($cartItem, $validated['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $this->cart->remove($cartItem);

        return back()->with('success', 'Item removed.');
    }
}
