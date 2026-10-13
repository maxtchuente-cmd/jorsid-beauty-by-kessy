<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use App\Services\JorsidMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;
class MailSettingsAdminController extends Controller
{
    public function index(): View
    {
        $saved=SmtpSetting::query()->first();
        $settings=[
            'host'=>$saved?->host ?? config('mail.mailers.smtp.host'),
            'port'=>$saved?->port ?? config('mail.mailers.smtp.port',587),
            'encryption'=>$saved?->encryption ?? 'tls',
            'username'=>$saved?->username ?? config('mail.mailers.smtp.username'),
            'from_address'=>$saved?->from_address ?? config('mail.from.address'),
            'from_name'=>$saved?->from_name ?? config('mail.from.name'),
            'admin_notification_email'=>$saved?->admin_notification_email ?? config('jorsid.admin_email'),
            'has_password'=>(bool)($saved?->password_ciphertext ?: config('mail.mailers.smtp.password')),
            'source'=>$saved?'Base de données (chiffrée)':'Configuration .env',
        ];
        return view('admin.mail-settings.index',compact('settings'));
    }
    public function save(Request $request): RedirectResponse
    {
        $v=$request->validate(['host'=>'required|string|max:190','port'=>'required|integer|min:1|max:65535','encryption'=>'required|in:tls,ssl','username'=>'nullable|string|max:190','password'=>'nullable|string|max:1000','from_address'=>'required|email|max:190','from_name'=>'required|string|max:190','admin_notification_email'=>'nullable|email|max:190']);
        $current=SmtpSetting::query()->first();$data=['host'=>$v['host'],'port'=>$v['port'],'encryption'=>$v['encryption'],'username'=>$v['username']??null,'from_address'=>$v['from_address'],'from_name'=>$v['from_name'],'admin_notification_email'=>$v['admin_notification_email']??null];
        if(!empty($v['password'])) $data['password_ciphertext']=Crypt::encryptString($v['password']); elseif($current) $data['password_ciphertext']=$current->password_ciphertext;
        if($current)$current->update($data);else SmtpSetting::create($data);
        return back()->with('success','Paramètres SMTP enregistrés. Envoyez un e-mail de test avant de compter sur ces paramètres.');
    }
    public function test(Request $request,JorsidMailService $mail): RedirectResponse
    {
        $v=$request->validate(['test_email'=>'required|email|max:190']);
        try{$mail->sendTest($v['test_email']);return back()->with('success','L’envoi du test SMTP a été lancé sans erreur. Vérifiez aussi la boîte de réception et les courriers indésirables.');}
        catch(Throwable $e){Log::error('Jorsid SMTP test failed',['error'=>$e->getMessage()]);return back()->with('error','Échec du test SMTP. Vérifiez le serveur, le port, le chiffrement, les identifiants et les restrictions IP dans Brevo. Les détails techniques ont été enregistrés dans les journaux.');}
    }
}
