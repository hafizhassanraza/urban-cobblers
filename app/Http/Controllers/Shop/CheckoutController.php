<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\UserAuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected UserAuthService $authService,
    ) {}

    public function index(): Response|RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $shipping = Setting::getFloat('shipping_rate', 15.00);

        return Inertia::render('Shop/Checkout', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
            'shipping' => $shipping,
            'user' => $user ? [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ] : null,
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $this->cart->mergeGuestCart(Auth::id());

        return redirect()->route('checkout.index')->with('success', 'Welcome back! You can now complete your order.');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $this->validateRegistration($request);
        $user = $this->createAccount($validated);
        event(new Registered($user));
        $request->session()->regenerate();
        $this->cart->mergeGuestCart($user->id);

        return redirect()->route('checkout.index')->with('success', 'Account created! Your details have been saved.');
    }

    public function store(Request $request): RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if (! Auth::check()) {
            $request->validate([
                'account_mode' => ['required', Rule::in(['login', 'register'])],
            ]);

            if ($request->account_mode === 'login') {
                $request->validate([
                    'login_method' => ['required', Rule::in(['email', 'phone'])],
                    'login' => ['required', 'string', 'max:255'],
                    'password' => ['required', 'string'],
                ]);

                $this->authService->attemptLogin(
                    $request->login_method,
                    $request->login,
                    $request->password,
                );
                $request->session()->regenerate();
                $this->cart->mergeGuestCart(Auth::id());
            } else {
                $validated = $this->validateRegistration($request);
                $user = $this->createAccount($validated);
                event(new Registered($user));
                $request->session()->regenerate();
            }
        }

        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->syncUserProfile($validated);

        $subtotal = $this->cart->subtotal();
        $shipping = Setting::getFloat('shipping_rate', 15.00);
        $total = $subtotal + $shipping;

        $order = DB::transaction(function () use ($items, $validated, $subtotal, $shipping, $total) {
            foreach ($items as $item) {
                if ($item->quantity > $item->product->stock) {
                    abort(422, "Not enough stock for {$item->product->name}.");
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'UC-'.strtoupper(str()->random(8)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_zip' => $validated['shipping_zip'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $order->recordStatus('pending', 'Your order has been received and is awaiting processing.');

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total' => $item->product->price * $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $this->cart->clear();

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    protected function validateRegistration(Request $request): array
    {
        if ($request->login_method === 'email') {
            $request->merge(['shipping_email' => $request->login]);
        }

        if ($request->login_method === 'phone') {
            $request->merge(['shipping_phone' => $request->login]);
        }

        $rules = [
            'login_method' => ['required', Rule::in(['email', 'phone'])],
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'shipping_email' => ['nullable', 'email', 'max:255'],
            'shipping_phone' => ['nullable', 'string', 'max:50'],
        ];

        if ($request->login_method === 'email') {
            $rules['login'][] = 'email';
        } else {
            $rules['login'][] = 'min:7';
        }

        $rules['shipping_email'] = ['required', 'email', 'max:255'];
        $rules['shipping_phone'] = ['required', 'string', 'max:50'];

        return $request->validate($rules);
    }

    protected function createAccount(array $validated)
    {
        $fullAddress = trim($validated['shipping_address'].', '.$validated['shipping_city'].' '.$validated['shipping_zip']);

        return $this->authService->register([
            'login_method' => $validated['login_method'],
            'login' => $validated['login'],
            'password' => $validated['password'],
            'name' => $validated['shipping_name'],
            'email' => $validated['shipping_email'] ?? null,
            'phone' => $validated['shipping_phone'] ?? null,
            'address' => $fullAddress,
        ]);
    }

    protected function syncUserProfile(array $validated): void
    {
        $user = Auth::user();
        $fullAddress = trim($validated['shipping_address'].', '.$validated['shipping_city'].' '.$validated['shipping_zip']);

        $user->update([
            'name' => $validated['shipping_name'],
            'email' => $validated['shipping_email'] ?? $user->email,
            'phone' => isset($validated['shipping_phone'])
                ? $this->authService->normalizePhone($validated['shipping_phone'])
                : $user->phone,
            'address' => $fullAddress,
        ]);
    }
}
