<?php
namespace App\Services;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use Carbon\Carbon;
use Throwable;
class JorsidMailService
{
    public function send(string $to, string $name, string $subject, string $body, ?Booking $booking = null, string $language = 'de'): void
    {
        $setting = SmtpSetting::query()->first();
        $fromAddress = $setting?->from_address ?: config('mail.from.address');
        $fromName = $setting?->from_name ?: config('mail.from.name', 'Jorsid Beauty by Kessy');
        $mailer = Mail::mailer();
        if ($setting) {
            $password = $setting->password_ciphertext ? Crypt::decryptString($setting->password_ciphertext) : (string) config('mail.mailers.smtp.password', '');
            $config = ['transport'=>'smtp','host'=>$setting->host,'port'=>(int)$setting->port,'username'=>$setting->username,'password'=>$password,'timeout'=>15,'local_domain'=>parse_url((string)config('app.url','http://localhost'),PHP_URL_HOST)];
            if ($setting->encryption === 'ssl') $config['scheme'] = 'smtps'; elseif ($setting->encryption === 'tls') $config['scheme'] = 'smtp';
            $mailer = Mail::build($config);
        }
        $html = view('emails.booking-notification', [
            'language' => in_array($language, ['de','fr','en'], true) ? $language : 'de',
            'logoUrl' => asset('images/jorsid-logo.jpg'), 'eyebrow' => $booking ? 'Appointment details' : 'Jorsid Beauty',
            'subject' => $subject, 'body' => $body,
            'bookingUrl' => $booking ? route('booking.confirmation', ['booking' => $booking->reference]) : null,
            'buttonText' => match ($language) {'fr' => 'Voir le récapitulatif', 'en' => 'View appointment summary', default => 'Terminübersicht ansehen'},
        ])->render();
        $mailer->html($html, function ($message) use ($to, $name, $subject, $booking, $fromAddress, $fromName) {
            if ($fromAddress) $message->from($fromAddress, $fromName ?: 'Jorsid Beauty by Kessy');
            $message->to($to, $name)->subject($subject);
            if ($booking && $booking->status === 'confirmed') {
                $message->attachData($this->calendarInvite($booking), 'jorsid-appointment.ics', ['mime' => 'text/calendar; charset=UTF-8; method=PUBLISH']);
            }
        });
    }

    private function calendarInvite(Booking $booking): string
    {
        $start = Carbon::parse($booking->appointment_date->format('Y-m-d').' '.substr($booking->appointment_time, 0, 5), config('app.timezone'))->utc()->format('Ymd\THis\Z');
        $end = Carbon::parse($booking->appointment_date->format('Y-m-d').' '.substr((string)($booking->ends_at ?: $booking->appointment_time), 0, 5), config('app.timezone'))->utc()->format('Ymd\THis\Z');
        $summary = ($booking->service?->name ?: 'Jorsid Beauty appointment');
        $location = $booking->service_mode === 'home' ? trim(($booking->address_line ?: '').' '.($booking->postal_code ?: '').' '.($booking->city ?: '')) : 'Jorsid Beauty by Kessy salon';
        $escape = static fn(string $value) => str_replace(["\\", ";", ",", "\n", "\r"], ["\\\\", "\\;", "\\,", "\\n", ''], $value);
        return "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Jorsid Beauty by Kessy//Appointments//EN\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nBEGIN:VEVENT\r\nUID:".$escape($booking->reference)."@jorsidbeauty\r\nDTSTAMP:".now('UTC')->format('Ymd\THis\Z')."\r\nDTSTART:".$start."\r\nDTEND:".$end."\r\nSUMMARY:".$escape($summary)."\r\nLOCATION:".$escape($location)."\r\nDESCRIPTION:".$escape('Référence '.$booking->reference.' — Jorsid Beauty by Kessy')."\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";
    }

    public function sendTest(string $to): void
    {
        $this->send($to,'Test','Test SMTP — Jorsid Beauty by Kessy','Cet e-mail confirme que le service SMTP configuré pour Jorsid Beauty by Kessy a pu envoyer un message de test.', null, 'fr');
    }
}
