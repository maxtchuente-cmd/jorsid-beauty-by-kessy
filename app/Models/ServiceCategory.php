<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ServiceCategory extends Model
{
    protected $fillable = ['name','name_de','name_fr','name_en','sort_order'];
    public function services(): HasMany { return $this->hasMany(Service::class, 'service_category_id'); }
}
