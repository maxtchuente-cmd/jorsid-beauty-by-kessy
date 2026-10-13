<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SmtpSetting extends Model
{
    protected $fillable=['host','port','encryption','username','password_ciphertext','from_address','from_name','admin_notification_email'];
    protected $hidden=['password_ciphertext'];
}
