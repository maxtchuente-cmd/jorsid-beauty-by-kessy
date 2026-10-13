<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelZone extends Model
{
    protected $fillable = ['name', 'city', 'postal_code_prefix', 'service_id', 'fee', 'is_active', 'notes'];
    protected function casts(): array { return ['fee' => 'decimal:2', 'is_active' => 'boolean']; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}
