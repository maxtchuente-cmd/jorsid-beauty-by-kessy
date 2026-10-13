<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'reference', 'service_id', 'customer_name', 'customer_email',
        'customer_phone', 'customer_language', 'service_mode', 'appointment_date', 'appointment_time',
        'address_line', 'postal_code', 'city', 'address_note', 'customer_note',
        'service_price', 'travel_fee', 'total_price', 'status', 'ends_at', 'admin_note', 'cancelled_at', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'cancelled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'service_price' => 'decimal:2',
            'travel_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
