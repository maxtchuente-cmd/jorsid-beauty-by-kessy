<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = ['booking_id', 'type', 'title', 'message', 'read_at'];
    protected function casts(): array { return ['read_at' => 'datetime']; }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
}
