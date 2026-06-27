<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #111; color: #fff; text-align: left; padding: 8px 6px; font-size: 10px; text-transform: uppercase; }
        td { border-bottom: 1px solid #ddd; padding: 7px 6px; vertical-align: top; }
        tr:nth-child(even) td { background: #f9f7f4; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Urban Cobblers — Orders Report</h1>
    <p class="meta">Generated {{ $generatedAt->format('M d, Y H:i') }} · {{ $orders->count() }} orders</p>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Status</th>
                <th>Tracking</th>
                <th class="right">Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->user?->name ?? $order->shipping_name }}</td>
                    <td>{{ $order->shipping_email }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->tracking_number ?? '—' }}</td>
                    <td class="right">${{ number_format($order->total, 2) }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
