<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BusinessClosure;
use App\Models\BusinessHour;
use App\Models\BusinessDateOverride;
use App\Models\AdminNotification;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingAdminController extends Controller
{
    public function index(Request $r): View
    {
        $q = Booking::with('service')->orderByDesc('appointment_date')->orderByDesc('appointment_time');
        if ($r->filled('status')) $q->where('status', $r->query('status'));
        if ($r->filled('date')) $q->whereDate('appointment_date', $r->query('date'));
        $notifications = AdminNotification::with('booking')->latest()->limit(12)->get();
        $unreadCount = AdminNotification::whereNull('read_at')->count();
        return view('admin.bookings.index', ['bookings' => $q->paginate(25)->withQueryString(), 'notifications' => $notifications, 'unreadCount' => $unreadCount]);
    }

    public function dateConflicts(Request $request)
    {
        $v = $request->validate(['date' => ['required', 'date', 'after_or_equal:today']]);
        $bookings = Booking::with('service')->whereDate('appointment_date', $v['date'])
            ->whereIn('status', ['pending', 'confirmed'])->orderBy('appointment_time')->get();
        return response()->json(['date' => $v['date'], 'count' => $bookings->count(), 'bookings' => $bookings->map(fn ($b) => [
            'id' => $b->id, 'reference' => $b->reference, 'customer' => $b->customer_name,
            'email' => $b->customer_email, 'service' => $b->service?->name,
            'time' => substr($b->appointment_time, 0, 5), 'status' => $b->status,
        ])]);
    }

    public function markNotificationRead(AdminNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);
        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function update(Request $r, Booking $booking, BookingNotificationService $notifications): RedirectResponse
    {
        $v = $r->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,rejected,no_show',
            'admin_note' => 'nullable|string|max:3000',
            'appointment_date' => 'nullable|date|after_or_equal:today',
            'appointment_time' => 'nullable|date_format:H:i',
        ]);
        $hasDate = $r->filled('appointment_date');
        $hasTime = $r->filled('appointment_time');
        if ($hasDate xor $hasTime) throw ValidationException::withMessages(['appointment_date' => 'Pour reporter un rendez-vous, choisissez une nouvelle date et une nouvelle heure.']);
        $previous = $booking->status;
        $previousDate = $booking->appointment_date->format('Y-m-d');
        $previousTime = substr($booking->appointment_time, 0, 5);
        $targetDate = $hasDate ? $v['appointment_date'] : $previousDate;
        $targetTime = $hasTime ? $v['appointment_time'] : $previousTime;
        if ($hasDate || $v['status'] === 'confirmed') $this->assertSlotAvailable($booking, $targetDate, $targetTime);
        $v['appointment_date'] = $targetDate;
        $v['appointment_time'] = $targetTime;
        $duration = max(15, (int) ($booking->service?->duration_minutes ?? 60));
        $v['ends_at'] = Carbon::parse($targetDate.' '.$targetTime)->addMinutes($duration)->format('H:i:s');
        $v['confirmed_at'] = $v['status'] === 'confirmed' ? ($booking->confirmed_at ?? now()) : $booking->confirmed_at;
        $v['cancelled_at'] = in_array($v['status'], ['cancelled', 'rejected'], true) ? now() : null;
        $booking->update($v);
        if ($previous !== $booking->status || $previousDate !== $targetDate || $previousTime !== $targetTime) {
            $notifications->statusChanged($booking, $previous);
        }
        return back()->with('success', ($previousDate !== $targetDate || $previousTime !== $targetTime) ? 'Rendez-vous reporté et mis à jour.' : 'Rendez-vous mis à jour. Un e-mail de statut a été tenté si le SMTP est configuré.');
    }

    private function assertSlotAvailable(Booking $booking, string $date, string $time): void
    {
        $service = $booking->service;
        if (!$service) throw ValidationException::withMessages(['status' => 'La prestation associée à ce rendez-vous est introuvable.']);
        if (BusinessClosure::whereDate('closure_date', $date)->exists()) throw ValidationException::withMessages(['appointment_date' => 'Cette journée est fermée.']);
        $day = Carbon::parse($date);
        $override = BusinessDateOverride::with('breaks')->whereDate('override_date', $date)->first();
        $hours = BusinessHour::where('weekday', $day->dayOfWeekIso)->first();
        if ($override) {
            if (!$override->is_open) throw ValidationException::withMessages(['appointment_date' => 'Cette journée est fermée.']);
            $open = substr((string) $override->open_time, 0, 5); $close = substr((string) $override->close_time, 0, 5);
            $breaks = $override->breaks->map(fn($b) => [substr($b->start_time, 0, 5), substr($b->end_time, 0, 5)])->all();
        } else {
            if ($hours ? !$hours->is_open : $day->isSunday()) throw ValidationException::withMessages(['appointment_date' => 'Aucun horaire de travail n’est prévu pour cette date.']);
            $open = $hours ? substr($hours->open_time, 0, 5) : '09:00'; $close = $hours ? substr($hours->close_time, 0, 5) : '18:00';
            $breaks = ($hours && $hours->break_start && $hours->break_end) ? [[substr($hours->break_start, 0, 5), substr($hours->break_end, 0, 5)]] : [];
        }
        $start = Carbon::parse($date.' '.$time); $end = $start->copy()->addMinutes(max(15, (int) $service->duration_minutes));
        if ($time < $open || $end->format('H:i') > $close) throw ValidationException::withMessages(['appointment_time' => 'La prestation complète doit tenir dans les horaires de travail.']);
        foreach ($breaks as [$breakStart, $breakEnd]) if ($start->format('H:i') < $breakEnd && $end->format('H:i') > $breakStart) throw ValidationException::withMessages(['appointment_time' => 'Le créneau chevauche une pause.']);
        $buffer = max(0, (int) config('jorsid.home_travel_buffer_minutes', 30));
        $candidateStart = $booking->service_mode === 'home' ? $start->copy()->subMinutes($buffer) : $start->copy();
        $candidateEnd = $booking->service_mode === 'home' ? $end->copy()->addMinutes($buffer) : $end->copy();
        $others = Booking::with('service')->where('id', '!=', $booking->id)->whereDate('appointment_date', $date)->whereIn('status', ['pending', 'confirmed'])->get();
        foreach ($others as $other) {
            $otherStart = Carbon::parse($date.' '.substr($other->appointment_time, 0, 5));
            $otherEnd = $other->ends_at ? Carbon::parse($date.' '.substr($other->ends_at, 0, 5)) : $otherStart->copy()->addMinutes((int) ($other->service?->duration_minutes ?? 60));
            if ($other->service_mode === 'home') { $otherStart->subMinutes($buffer); $otherEnd->addMinutes($buffer); }
            if ($candidateStart->lt($otherEnd) && $candidateEnd->gt($otherStart)) throw ValidationException::withMessages(['appointment_time' => 'Ce créneau chevauche un autre rendez-vous. Choisissez une autre date ou une autre heure.']);
        }
    }

    public function addClosure(Request $r, BookingNotificationService $notifications): RedirectResponse
    {
        $v = $r->validate([
            'closure_date' => ['required', 'date', 'after_or_equal:today'],
            'label' => ['nullable', 'string', 'max:150'],
            'conflict_action' => ['nullable', 'in:reject,cancel'],
        ]);
        $bookings = Booking::with('service')->whereDate('appointment_date', $v['closure_date'])
            ->whereIn('status', ['pending', 'confirmed'])->orderBy('appointment_time')->get();
        if ($bookings->isNotEmpty() && empty($v['conflict_action'])) {
            return back()->withInput()->with('date_conflicts', $bookings->map(fn ($b) => $b->customer_name.' — '.substr($b->appointment_time, 0, 5).' — '.$b->service?->name.' ('.$b->status.')')->all())
                ->withErrors(['conflict_action' => 'Cette date contient déjà des rendez-vous. Choisissez de les refuser ou de les annuler avant de fermer la date.']);
        }
        $previousStatuses = $bookings->mapWithKeys(fn ($booking) => [$booking->id => $booking->status])->all();
        $status = ($v['conflict_action'] ?? '') === 'reject' ? 'rejected' : 'cancelled';
        DB::transaction(function () use ($v, $bookings, $previousStatuses, $status) {
            if ($bookings->isNotEmpty()) {
                Booking::whereIn('id', $bookings->pluck('id'))->update([
                    'status' => $status,
                    'cancelled_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            BusinessClosure::firstOrCreate(['closure_date' => $v['closure_date']], ['label' => $v['label'] ?? 'Congé']);
            AdminNotification::create([
                'type' => 'agenda_closure', 'title' => 'Date fermée dans l’agenda',
                'message' => 'La date du '.Carbon::parse($v['closure_date'])->format('d/m/Y').' a été fermée'.($bookings->isNotEmpty() ? ' et '.$bookings->count().' rendez-vous ont été traités.' : '.'),
            ]);
        });
        // E-mails are sent only after the database changes have committed.
        foreach ($bookings as $booking) {
            $booking->refresh();
            $notifications->statusChanged($booking, $previousStatuses[$booking->id] ?? 'pending');
        }
        return back()->with('success', 'Date fermée dans l’agenda. Les clientes concernées ont été informées par e-mail si le service e-mail est configuré.');
    }
}
