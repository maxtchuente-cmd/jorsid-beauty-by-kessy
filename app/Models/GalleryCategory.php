<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class GalleryCategory extends Model
{
    protected $fillable = ['name','name_de','name_fr','name_en','sort_order'];
    public function items(): HasMany { return $this->hasMany(GalleryItem::class, 'gallery_category_id'); }
}
