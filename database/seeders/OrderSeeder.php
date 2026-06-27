<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $sarah = User::where('email', 'sarah@example.com')->first();
        $admin = User::where('email', 'admin@urbancobblers.com')->first();

        if (! $customer) {
            return;
        }

        $this->createOrder($customer, [
            ['slug' => 'navy-leather-peshawari', 'qty' => 1],
            ['slug' => 'cedar-shoe-trees', 'qty' => 1],
        ], 'delivered', 'John Customer', 'customer@example.com', [
            'tracking_number' => '9400111899223344556677',
            'carrier' => 'usps',
            'shipped_at' => now()->subDays(5),
            'delivered_at' => now()->subDays(2),
        ], $admin);

        $this->createOrder($customer, [
            ['slug' => 'black-croc-signature-bit', 'qty' => 1],
        ], 'shipped', 'John Customer', 'customer@example.com', [
            'tracking_number' => '1Z999AA10123456784',
            'carrier' => 'ups',
            'shipped_at' => now()->subDay(),
        ], $admin);

        $this->createOrder($customer, [
            ['slug' => 'complete-care-set', 'qty' => 1],
            ['slug' => 'premium-laces', 'qty' => 2],
        ], 'processing', 'John Customer', 'customer@example.com', [], $admin);

        if ($sarah) {
            $this->createOrder($sarah, [
                ['slug' => 'copper-croc-peshawari', 'qty' => 1],
                ['slug' => 'tan-ostrich-peshawari', 'qty' => 1],
            ], 'pending', 'Sarah Mitchell', 'sarah@example.com', [], $admin);
        }
    }

    protected function createOrder(User $user, array $items, string $status, string $name, string $email, array $meta = [], ?User $admin = null): void
    {
        $subtotal = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $product = Product::where('slug', $item['slug'])->first();
            if (! $product) {
                continue;
            }

            $lineTotal = $product->price * $item['qty'];
            $subtotal += $lineTotal;

            $orderItems[] = [
                'product' => $product,
                'quantity' => $item['qty'],
                'total' => $lineTotal,
            ];
        }

        if (empty($orderItems)) {
            return;
        }

        $shipping = 15.00;
        $total = $subtotal + $shipping;

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'UC-'.strtoupper(substr(uniqid(), -8)),
            'status' => $status,
            'tracking_number' => $meta['tracking_number'] ?? null,
            'carrier' => $meta['carrier'] ?? null,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'shipping_name' => $name,
            'shipping_email' => $email,
            'shipping_phone' => $user->phone,
            'shipping_address' => $user->address ?? '123 Main Street',
            'shipping_city' => 'New York',
            'shipping_zip' => '10001',
            'shipped_at' => $meta['shipped_at'] ?? null,
            'delivered_at' => $meta['delivered_at'] ?? null,
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'price' => $item['product']->price,
                'quantity' => $item['quantity'],
                'total' => $item['total'],
            ]);
        }

        $order->recordStatus('pending', 'Your order has been received and is awaiting processing.', $admin);

        if ($status !== 'pending') {
            $notes = [
                'processing' => 'Your order is being prepared in our workshop.',
                'shipped' => 'Your order has shipped.'.($order->tracking_number ? ' Tracking: '.$order->tracking_number : ''),
                'delivered' => 'Your order has been delivered. Thank you for shopping with us!',
            ];

            if (in_array($status, ['processing', 'shipped', 'delivered'])) {
                $order->recordStatus('processing', $notes['processing'], $admin);
            }

            if (in_array($status, ['shipped', 'delivered'])) {
                $order->recordStatus('shipped', $notes['shipped'], $admin, [
                    'tracking_number' => $order->tracking_number,
                    'carrier' => $order->carrier,
                ]);
            }

            if ($status === 'delivered') {
                $order->recordStatus('delivered', $notes['delivered'], $admin);
            }
        }
    }
}
