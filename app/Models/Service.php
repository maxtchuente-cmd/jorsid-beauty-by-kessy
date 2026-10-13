<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'service_category_id', 'description', 'image_path', 'name_de', 'name_fr', 'name_en', 'description_de', 'description_fr', 'description_en', 'price', 'price_from',
        'duration_minutes', 'at_salon', 'at_home', 'travel_fee',
        'conditions', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'travel_fee' => 'decimal:2',
            'price_from' => 'boolean',
            'at_salon' => 'boolean',
            'at_home' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
