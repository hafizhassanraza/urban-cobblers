<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->boolean('low_stock')) {
            $query->where('stock', '<=', 5);
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search', 'category_id', 'status', 'low_stock']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if (empty($validated['sku'])) {
            $validated['sku'] = 'UC-'.strtoupper(Str::random(6));
        }

        if ($request->hasFile('image')) {
            $validated['image'] = '/storage/'.$request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => $product->load('category'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        if ($product->name !== $validated['name']) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $product->id);
        }

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($product->image);
            $validated['image'] = '/storage/'.$request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredImage($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', 'in:activate,deactivate,delete'],
        ]);

        $products = Product::whereIn('id', $validated['ids'])->get();

        match ($validated['action']) {
            'activate' => $products->each->update(['is_active' => true]),
            'deactivate' => $products->each->update(['is_active' => false]),
            'delete' => $products->each(function (Product $product) {
                $this->deleteStoredImage($product->image);
                $product->delete();
            }),
        };

        return back()->with('success', 'Bulk action completed.');
    }

    protected function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_ready_to_wear'] = $request->boolean('is_ready_to_wear');
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }

    protected function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, '/storage/')) {
            return;
        }

        $relative = str_replace('/storage/', '', $path);
        Storage::disk('public')->delete($relative);
    }
}
