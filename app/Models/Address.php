<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'contact_name',
        'phone',
        'governorate',
        'city',
        'district',
        'street_address',
        'building_number',
        'floor',
        'apartment',
        'landmark',
        'delivery_instructions',
        'is_default',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->governorate,
            $this->city,
            $this->district,
            $this->street_address,
            $this->building_number ? 'Bldg '.$this->building_number : null,
            $this->floor ? 'Floor '.$this->floor : null,
            $this->apartment ? 'Apt '.$this->apartment : null,
        ]);

        return implode(', ', $parts);
    }
}
