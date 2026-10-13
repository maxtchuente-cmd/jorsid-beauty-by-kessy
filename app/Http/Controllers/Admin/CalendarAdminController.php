<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BusinessClosure;
use App\Models\BusinessDateOverride;
use App\Models\BusinessHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CalendarAdminController extends Controller {
 public function index(): View { return view('admin.calendar.index'); }
 public function events(Request $r): JsonResponse {
  $v=$r->validate(['start'=>'required|date','end'=>'required|date|after:start']);
  $start=Carbon::parse($v['start'])->toDateString(); $end=Carbon::parse($v['end'])->toDateString();
  $bookings=Booking::with('service')->whereBetween('appointment_date',[$start,$end])->whereNotIn('status',['cancelled','rejected'])->orderBy('appointment_date')->orderBy('appointment_time')->get();
  $events=$bookings->map(fn($b)=>['id'=>'booking-'.$b->id,'title'=>substr($b->appointment_time,0,5).' · '.$b->customer_name.' · '.($b->service?->name??'Prestation'),'start'=>$b->appointment_date->toDateString().'T'.substr($b->appointment_time,0,5).':00','end'=>$b->ends_at?$b->appointment_date->toDateString().'T'.substr($b->ends_at,0,5).':00':null,'type'=>'booking','status'=>$b->status,'customer'=>$b->customer_name,'email'=>$b->customer_email,'phone'=>$b->customer_phone,'service'=>$b->service?->name,'reference'=>$b->reference,'date'=>$b->appointment_date->toDateString(),'time'=>substr($b->appointment_time,0,5),'mode'=>$b->service_mode,'price'=>$b->total_price]);
  $closures=BusinessClosure::whereBetween('closure_date',[$start,$end])->get()->map(fn($c)=>['id'=>'closure-'.$c->id,'title'=>'Fermé · '.($c->label??'Jour fermé'),'start'=>$c->closure_date->toDateString(),'allDay'=>true,'type'=>'closed']);
  $overrides=BusinessDateOverride::whereBetween('override_date',[$start,$end])->get()->map(fn($o)=>['id'=>'override-'.$o->id,'title'=>($o->is_open?'Horaires spéciaux':'Fermé').($o->label?' · '.$o->label:''),'start'=>$o->override_date->toDateString(),'allDay'=>true,'type'=>$o->is_open?'override':'closed']);
  return response()->json($events->concat($closures)->concat($overrides)->values());
 }
 public function day(Request $r): JsonResponse {
  $v=$r->validate(['date'=>'required|date']);
  $date=Carbon::parse($v['date']); $override=BusinessDateOverride::whereDate('override_date',$date)->first();
  $hours=BusinessHour::where('weekday',$date->dayOfWeekIso)->first();
  $bookings=Booking::with('service')->whereDate('appointment_date',$date)->whereNotIn('status',['cancelled','rejected'])->orderBy('appointment_time')->get();
  return response()->json(['date'=>$date->toDateString(),'closed'=>BusinessClosure::whereDate('closure_date',$date)->exists()||($override&&!$override->is_open), 'override'=>$override, 'hours'=>['is_open'=>$override?->is_open??$hours?->is_open??!$date->isSunday(),'open_time'=>$override?->open_time??$hours?->open_time??'09:00','close_time'=>$override?->close_time??$hours?->close_time??'18:00','breaks'=>$override?->breaks??(($hours&&$hours->break_start&&$hours->break_end)?[['start'=>substr($hours->break_start,0,5),'end'=>substr($hours->break_end,0,5)]]:[])], 'bookings'=>$bookings->map(fn($b)=>['id'=>$b->id,'reference'=>$b->reference,'customer'=>$b->customer_name,'email'=>$b->customer_email,'phone'=>$b->customer_phone,'service'=>$b->service?->name,'time'=>substr($b->appointment_time,0,5),'end'=>$b->ends_at?substr($b->ends_at,0,5):null,'status'=>$b->status,'price'=>$b->total_price])]);
 }
 public function saveDay(Request $r): RedirectResponse {
  $v=$r->validate(['override_date'=>'required|date|after_or_equal:today','is_open'=>'required|boolean','open_time'=>'nullable|date_format:H:i','close_time'=>'nullable|date_format:H:i','label'=>'nullable|string|max:150','breaks'=>'nullable|array','breaks.*.start'=>'required_with:breaks|date_format:H:i','breaks.*.end'=>'required_with:breaks|date_format:H:i']);
  if($v['is_open'] && (!$v['open_time']||!$v['close_time']||$v['close_time']<=$v['open_time'])) return back()->withInput()->withErrors(['open_time'=>'Pour une journée ouverte, indiquez des horaires valides.']);
  foreach($v['breaks']??[] as $break) if($break['end']<=$break['start']||($v['is_open']&&($break['start']<$v['open_time']||$break['end']>$v['close_time']))) return back()->withInput()->withErrors(['breaks'=>'Chaque pause doit se situer dans les horaires de travail.']);
  $active=Booking::whereDate('appointment_date',$v['override_date'])->whereIn('status',['pending','confirmed'])->exists();
  if(!$v['is_open']&&$active) return back()->withInput()->withErrors(['is_open'=>'Cette date contient des rendez-vous actifs. Traitez-les dans la page Rendez-vous avant de fermer la journée.']);
  BusinessDateOverride::updateOrCreate(['override_date'=>$v['override_date']],['is_open'=>$v['is_open'],'open_time'=>$v['is_open']?$v['open_time']:null,'close_time'=>$v['is_open']?$v['close_time']:null,'breaks'=>$v['is_open']?($v['breaks']??[]):[],'label'=>$v['label']??null]);
  return redirect()->route('admin.calendar.index',['date'=>$v['override_date']])->with('success','Les horaires de cette date ont été enregistrés sans modifier les horaires habituels.');
 }
}
