<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'body',
        'is_verified_purchase',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function reviewerDisplayName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        if (count($parts) < 2) {
            return $parts[0] ?? 'Customer';
        }

        return $parts[0].' '.mb_substr($parts[1], 0, 1).'.';
    }
}
