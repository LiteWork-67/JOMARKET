<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'product_id',
        'product_variant_id',
        'seller_sku',
        'condition',
        'price',
        'sale_price',
        'cost_price',
        'currency',
        'stock_quantity',
        'reserved_quantity',
        'min_order_quantity',
        'max_order_quantity',
        'handling_days',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'sale_price' => 'decimal:3',
            'cost_price' => 'decimal:3',
            'stock_quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'min_order_quantity' => 'integer',
            'max_order_quantity' => 'integer',
            'handling_days' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function inventoryReservations(): HasMany
    {
        return $this->hasMany(InventoryReservation::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price) {
            return (float) $this->sale_price;
        }

        return (float) $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((((float) $this->price - (float) $this->sale_price) / (float) $this->price) * 100);
    }
}
