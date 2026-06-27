<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; font-size: 10px; }
        .stats { width: 100%; margin-bottom: 20px; }
        .stats td { padding: 10px; background: #f9f7f4; border: 1px solid #ddd; width: 25%; }
        .stats strong { display: block; font-size: 16px; color: #B87333; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #111; color: #fff; text-align: left; padding: 8px 6px; font-size: 10px; text-transform: uppercase; }
        td { border-bottom: 1px solid #ddd; padding: 7px 6px; }
        .right { text-align: right; }
        h2 { font-size: 13px; margin: 18px 0 8px; }
    </style>
</head>
<body>
    <h1>Urban Cobblers — Sales Report</h1>
    <p class="meta">Period: {{ $dateFrom }} to {{ $dateTo }} · Generated {{ $generatedAt->format('M d, Y H:i') }}</p>

    <table class="stats">
        <tr>
            <td>Total Orders<strong>{{ $summary['total_orders'] }}</strong></td>
            <td>Total Revenue<strong>${{ number_format($summary['total_revenue'], 2) }}</strong></td>
            <td>Average Order<strong>${{ number_format($summary['average_order'], 2) }}</strong></td>
            <td>Cancelled<strong>{{ $summary['cancelled_orders'] }}</strong></td>
        </tr>
    </table>

    <h2>Orders by Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="right">Orders</th>
                <th class="right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesByStatus as $row)
                <tr>
                    <td>{{ ucfirst($row->status) }}</td>
                    <td class="right">{{ $row->count }}</td>
                    <td class="right">${{ number_format($row->revenue ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="right">Qty Sold</th>
                <th class="right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td class="right">{{ $product['total_qty'] }}</td>
                    <td class="right">${{ number_format($product['total_revenue'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No product sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Monthly Revenue (Last 6 Months)</h2>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="right">Orders</th>
                <th class="right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revenueByMonth as $month)
                <tr>
                    <td>{{ $month['label'] }}</td>
                    <td class="right">{{ $month['orders'] }}</td>
                    <td class="right">${{ number_format($month['revenue'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
