<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::withCount('products')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if ($request->hasFile('image_file')) {
            $validated['image'] = '/storage/'.$request->file('image_file')->store('categories', 'public');
        }

        unset($validated['image_file']);
        Category::create($validated);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        if ($category->name !== $validated['name']) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $category->id);
        }

        if ($request->hasFile('image_file')) {
            $this->deleteStoredImage($category->image);
            $validated['image'] = '/storage/'.$request->file('image_file')->store('categories', 'public');
        }

        unset($validated['image_file']);
        $category->update($validated);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products.');
        }

        $this->deleteStoredImage($category->image);
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    protected function validateCategory(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    protected function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
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
