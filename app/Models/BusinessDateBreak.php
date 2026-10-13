<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BusinessDateBreak extends Model
{
    protected $fillable = ['business_date_override_id','start_time','end_time'];
    public function override(): BelongsTo { return $this->belongsTo(BusinessDateOverride::class, 'business_date_override_id'); }
}
