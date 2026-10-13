<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BusinessClosure extends Model { protected $fillable=['closure_date','label']; protected function casts(): array{return ['closure_date'=>'date'];} }
