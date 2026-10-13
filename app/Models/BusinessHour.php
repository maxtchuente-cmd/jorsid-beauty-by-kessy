<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BusinessHour extends Model { protected $fillable=['weekday','is_open','open_time','close_time','break_start','break_end']; protected function casts(): array{return ['is_open'=>'boolean'];} }
