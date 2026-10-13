<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Booking;
use App\Models\BusinessClosure;
use App\Models\BusinessDateOverride;
use App\Models\BusinessHour;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HoursAdminController extends Controller
{
    public function index(): View
    {
        $hours=BusinessHour::orderBy('weekday')->get()->keyBy('weekday');
        $closures=BusinessClosure::whereDate('closure_date','>=',today())->orderBy('closure_date')->get();
        return view('admin.hours.index',compact('hours','closures'));
    }

    public function events(Request $request): JsonResponse
    {
        $v=$request->validate(['start'=>'required|date','end'=>'required|date|after:start']);
        $bookings=Booking::with('service')->whereDate('appointment_date','>=',$v['start'])->whereDate('appointment_date','<',$v['end'])->orderBy('appointment_date')->orderBy('appointment_time')->get();
        $events=$bookings->map(function($b){
            $start=$b->appointment_date->format('Y-m-d').'T'.substr($b->appointment_time,0,8);
            $end=$b->appointment_date->format('Y-m-d').'T'.substr((string)($b->ends_at ?? $b->appointment_time),0,8);
            return ['id'=>'booking-'.$b->id,'title'=>substr($b->appointment_time,0,5).' · '.$b->customer_name,'start'=>$start,'end'=>$end,'kind'=>'booking','booking_id'=>$b->id,'status'=>$b->status,'customer'=>$b->customer_name,'email'=>$b->customer_email,'phone'=>$b->customer_phone,'service'=>$b->service?->name ?? 'Prestation','mode'=>$b->service_mode,'reference'=>$b->reference,'total'=>number_format((float)$b->total_price,2,'.',''),'address'=>trim(($b->address_line??'').' '.($b->postal_code??'').' '.($b->city??''))];
        })->values();
        $closures=BusinessClosure::whereDate('closure_date','>=',$v['start'])->whereDate('closure_date','<',$v['end'])->get()->map(fn($c)=>['id'=>'closure-'.$c->id,'title'=>$c->label ?: 'Fermé','start'=>$c->closure_date->format('Y-m-d'),'allDay'=>true,'kind'=>'closure']);
        $availability=BusinessDateOverride::whereDate('override_date','>=',$v['start'])->whereDate('override_date','<',$v['end'])->get()->map(fn($o)=>['id'=>'availability-'.$o->id,'title'=>$o->is_open?'Geöffnet':'Geschlossen','start'=>$o->override_date->format('Y-m-d'),'allDay'=>true,'kind'=>'availability','is_open'=>(bool)$o->is_open,'open_time'=>$o->open_time?substr($o->open_time,0,5):null,'close_time'=>$o->close_time?substr($o->close_time,0,5):null,'label'=>$o->label]);
        return response()->json($events->concat($closures)->concat($availability)->values());
    }

    public function day(Request $request): JsonResponse
    {
        $v=$request->validate(['date'=>'required|date']); $date=Carbon::parse($v['date']);
        $override=BusinessDateOverride::with('breaks')->whereDate('override_date',$date->toDateString())->first();
        $weekly=BusinessHour::where('weekday',$date->dayOfWeekIso)->first();
        $closure=BusinessClosure::whereDate('closure_date',$date->toDateString())->first();
        $bookings=Booking::with('service')->whereDate('appointment_date',$date->toDateString())->orderBy('appointment_time')->get()->map(fn($b)=>['id'=>$b->id,'reference'=>$b->reference,'time'=>substr($b->appointment_time,0,5),'end'=>$b->ends_at?substr($b->ends_at,0,5):null,'customer'=>$b->customer_name,'email'=>$b->customer_email,'phone'=>$b->customer_phone,'service'=>$b->service?->name ?? 'Prestation','status'=>$b->status,'mode'=>$b->service_mode,'address'=>trim(($b->address_line??'').' '.($b->postal_code??'').' '.($b->city??'')),'total'=>number_format((float)$b->total_price,2,',',' ')]);
        return response()->json(['date'=>$date->toDateString(),'label'=>$date->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY'),'bookings'=>$bookings,'closure'=>$closure?->label,'override'=>$override?['is_open'=>$override->is_open,'open_time'=>$override->open_time?substr($override->open_time,0,5):'09:00','close_time'=>$override->close_time?substr($override->close_time,0,5):'18:00','label'=>$override->label,'breaks'=>$override->breaks->map(fn($b)=>['start'=>substr($b->start_time,0,5),'end'=>substr($b->end_time,0,5)])]:null,'weekly'=>$weekly?['is_open'=>$weekly->is_open,'open_time'=>substr($weekly->open_time,0,5),'close_time'=>substr($weekly->close_time,0,5),'break_start'=>$weekly->break_start?substr($weekly->break_start,0,5):null,'break_end'=>$weekly->break_end?substr($weekly->break_end,0,5):null]:null]);
    }

    public function saveDay(Request $request, BookingNotificationService $notifications): RedirectResponse
    {
        $v=$request->validate(['override_date'=>['required','date','after_or_equal:today'],'is_open'=>['nullable','boolean'],'open_time'=>['nullable','date_format:H:i'],'close_time'=>['nullable','date_format:H:i'],'label'=>['nullable','string','max:150'],'conflict_action'=>['nullable','in:reject,cancel']]);
        $open=(bool)($v['is_open']??false);
        if($open && (empty($v['open_time'])||empty($v['close_time'])||$v['close_time']<=$v['open_time'])) return back()->withInput()->withErrors(['open_time'=>'Pour une journée ouverte, la fermeture doit être après l’ouverture.']);
        $date=$v['override_date'];
        $bookings=Booking::with('service')->whereDate('appointment_date',$date)->whereIn('status',['pending','confirmed'])->get();
        if($open && $bookings->isNotEmpty()) {
            foreach($bookings as $existing) {
                $start=substr($existing->appointment_time,0,5); $end=$existing->ends_at?substr($existing->ends_at,0,5):Carbon::parse($date.' '.$start)->addMinutes((int)($existing->service?->duration_minutes??60))->format('H:i');
                if($start<$v['open_time'] || $end>$v['close_time']) return back()->withInput()->withErrors(['open_time'=>'Les nouveaux horaires excluent le rendez-vous '.$existing->reference.'. Modifiez ou reportez ce rendez-vous avant d’enregistrer.']);
            }
        }
        if(!$open && $bookings->isNotEmpty() && empty($v['conflict_action'])) return back()->withInput()->withErrors(['conflict_action'=>'Cette date contient des rendez-vous. Choisissez de les refuser ou de les annuler avant de fermer la journée.']);
        $previous=$bookings->mapWithKeys(fn($b)=>[$b->id=>$b->status])->all();
        DB::transaction(function() use($v,$open,$date,$bookings){
            $override=BusinessDateOverride::updateOrCreate(['override_date'=>$date],['is_open'=>$open,'open_time'=>$open?$v['open_time']:null,'close_time'=>$open?$v['close_time']:null,'label'=>$v['label']??null]);
            $override->breaks()->delete();
            if($open) BusinessClosure::whereDate('closure_date',$date)->delete();
            else {
                BusinessClosure::updateOrCreate(['closure_date'=>$date],['label'=>$v['label']??'Fermeture exceptionnelle']);
                if($bookings->isNotEmpty()) Booking::whereIn('id',$bookings->pluck('id'))->update(['status'=>($v['conflict_action']==='reject'?'rejected':'cancelled'),'cancelled_at'=>now(),'updated_at'=>now()]);
            }
            AdminNotification::create(['type'=>'agenda_override','title'=>$open?'Horaires exceptionnels enregistrés':'Journée fermée','message'=>'Les horaires du '.Carbon::parse($date)->format('d/m/Y').' ont été mis à jour.']);
        });
        if(!$open) foreach($bookings as $booking){$booking->refresh();$notifications->statusChanged($booking,$previous[$booking->id]??'pending');}
        return redirect()->route('admin.hours.index')->with('success','Les horaires de cette date ont été enregistrés sans modifier les horaires habituels.');
    }

    public function save(Request $r):RedirectResponse
    {
        $v=$r->validate(['hours'=>'required|array','hours.*.is_open'=>'nullable|boolean','hours.*.open_time'=>'required|date_format:H:i','hours.*.close_time'=>'required|date_format:H:i','hours.*.break_start'=>'nullable|date_format:H:i','hours.*.break_end'=>'nullable|date_format:H:i']);
        foreach($v['hours'] as $day=>$row){$open=(bool)($row['is_open']??false);if($open&&$row['close_time']<=$row['open_time'])return back()->withErrors(['hours'=>'La fermeture doit être après l’ouverture.']);if(!empty($row['break_start'])&&!empty($row['break_end'])&&$row['break_end']<=$row['break_start'])return back()->withErrors(['hours'=>'La fin de pause doit être après le début.']);BusinessHour::updateOrCreate(['weekday'=>(int)$day],['is_open'=>$open,'open_time'=>$row['open_time'],'close_time'=>$row['close_time'],'break_start'=>$row['break_start']??null,'break_end'=>$row['break_end']??null]);}
        return back()->with('success','Horaires habituels enregistrés.');
    }
    public function addClosure(Request $r):RedirectResponse{$v=$r->validate(['closure_date'=>'required|date|after_or_equal:today','label'=>'nullable|string|max:150']);BusinessClosure::firstOrCreate(['closure_date'=>$v['closure_date']],['label'=>$v['label']??'Congé']);return back()->with('success','Jour fermé ajouté.');}
    public function deleteClosure(BusinessClosure $closure):RedirectResponse{$closure->delete();return back()->with('success','Jour fermé supprimé.');}
}
