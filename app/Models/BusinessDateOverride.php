<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class BusinessDateOverride extends Model
{
    protected $fillable = ['override_date','is_open','open_time','close_time','label'];
    protected function casts(): array { return ['override_date'=>'date','is_open'=>'boolean']; }
    public function breaks(): HasMany { return $this->hasMany(BusinessDateBreak::class); }
}
