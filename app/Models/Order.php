<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'tracking_number',
        'carrier',
        'subtotal',
        'shipping',
        'total',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_zip',
        'notes',
        'admin_notes',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public static function statuses(): array
    {
        return ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    }

    public static function carriers(): array
    {
        return ['usps', 'ups', 'fedex', 'dhl', 'other'];
    }

    public static function activeStatuses(): array
    {
        return ['pending', 'processing', 'shipped'];
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::activeStatuses(), true);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Order Placed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($status),
        };
    }

    public static function carrierLabel(?string $carrier): ?string
    {
        if (! $carrier) {
            return null;
        }

        return match ($carrier) {
            'usps' => 'USPS',
            'ups' => 'UPS',
            'fedex' => 'FedEx',
            'dhl' => 'DHL',
            'other' => 'Other Carrier',
            default => strtoupper($carrier),
        };
    }

    public function trackingUrl(): ?string
    {
        if (! $this->tracking_number) {
            return null;
        }

        $number = urlencode($this->tracking_number);

        return match ($this->carrier) {
            'usps' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$number}",
            'ups' => "https://www.ups.com/track?tracknum={$number}",
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$number}",
            'dhl' => "https://www.dhl.com/us-en/home/tracking.html?tracking-id={$number}",
            default => null,
        };
    }

    public function recordStatus(string $status, ?string $note = null, ?User $admin = null, ?array $meta = []): OrderStatusHistory
    {
        return $this->statusHistories()->create([
            'status' => $status,
            'note' => $note,
            'tracking_number' => $meta['tracking_number'] ?? $this->tracking_number,
            'carrier' => $meta['carrier'] ?? $this->carrier,
            'updated_by' => $admin?->id,
        ]);
    }

    public function statusProgress(): array
    {
        $steps = [
            ['key' => 'pending', 'label' => 'Order Placed'],
            ['key' => 'processing', 'label' => 'Processing'],
            ['key' => 'shipped', 'label' => 'Shipped'],
            ['key' => 'delivered', 'label' => 'Delivered'],
        ];

        if ($this->status === 'cancelled') {
            return [
                'cancelled' => true,
                'steps' => $steps,
                'currentIndex' => -1,
            ];
        }

        $order = ['pending', 'processing', 'shipped', 'delivered'];
        $currentIndex = array_search($this->status, $order, true);

        return [
            'cancelled' => false,
            'steps' => $steps,
            'currentIndex' => $currentIndex !== false ? $currentIndex : 0,
        ];
    }
}
