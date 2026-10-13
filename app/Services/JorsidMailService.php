<?php
namespace App\Services;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
class JorsidMailService
{
    public function send(string $to, string $name, string $subject, string $body): void
    {
        $setting=SmtpSetting::query()->first();
        if(!$setting){Mail::raw($body,fn($mail)=>$mail->to($to,$name)->subject($subject));return;}
        $password=$setting->password_ciphertext ? Crypt::decryptString($setting->password_ciphertext) : (string)config('mail.mailers.smtp.password','');
        $config=['transport'=>'smtp','host'=>$setting->host,'port'=>(int)$setting->port,'username'=>$setting->username,'password'=>$password,'timeout'=>15,'local_domain'=>parse_url((string)config('app.url','http://localhost'),PHP_URL_HOST)];
        if($setting->encryption==='ssl') $config['scheme']='smtps'; elseif($setting->encryption==='tls') $config['scheme']='smtp';
        $mailer=Mail::build($config);
        $mailer->raw($body,function($message)use($to,$name,$subject,$setting){$message->from($setting->from_address,$setting->from_name)->to($to,$name)->subject($subject);});
    }
    public function sendTest(string $to): void
    {
        $this->send($to,'Test','Test SMTP — Jorsid Beauty by Kessy','Cet e-mail confirme que le service SMTP configuré pour Jorsid Beauty by Kessy a pu envoyer un message de test.');
    }
}
