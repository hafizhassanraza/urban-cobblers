<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_price',
        'image',
        'stock',
        'is_featured',
        'is_ready_to_wear',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_ready_to_wear' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function isOnSale(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->isOnSale()) {
            return null;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }
}
