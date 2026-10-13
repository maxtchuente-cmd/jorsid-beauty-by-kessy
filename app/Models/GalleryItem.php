<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GalleryItem extends Model
{
    protected $fillable=['title','image_path','category','gallery_category_id','alt_text','is_published','sort_order'];
    protected function casts():array{return ['is_published'=>'boolean'];}
    public function galleryCategory(): BelongsTo { return $this->belongsTo(GalleryCategory::class, 'gallery_category_id'); }
}
