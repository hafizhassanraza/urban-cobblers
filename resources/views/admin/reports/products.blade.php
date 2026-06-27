<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #111; color: #fff; text-align: left; padding: 8px 6px; font-size: 10px; text-transform: uppercase; }
        td { border-bottom: 1px solid #ddd; padding: 7px 6px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Urban Cobblers — Product Catalog</h1>
    <p class="meta">Generated {{ $generatedAt->format('M d, Y H:i') }} · {{ $products->count() }} products</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>SKU</th>
                <th class="right">Price</th>
                <th class="right">Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>{{ $product->sku ?? '—' }}</td>
                    <td class="right">${{ number_format($product->price, 2) }}</td>
                    <td class="right">{{ $product->stock }}</td>
                    <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
