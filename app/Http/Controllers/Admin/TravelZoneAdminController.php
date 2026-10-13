<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\TravelZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelZoneAdminController extends Controller
{
    public function index(): View { return view('admin.travel-zones.index', ['zones' => TravelZone::with('service')->orderBy('city')->orderBy('name')->paginate(30), 'services' => Service::orderBy('name')->get()]); }
    public function store(Request $r): RedirectResponse { TravelZone::create($this->validated($r)); return back()->with('success', 'Zone de déplacement ajoutée.'); }
    public function update(Request $r, TravelZone $zone): RedirectResponse { $zone->update($this->validated($r)); return back()->with('success', 'Zone de déplacement mise à jour.'); }
    public function destroy(TravelZone $zone): RedirectResponse { $zone->delete(); return back()->with('success', 'Zone supprimée.'); }
    private function validated(Request $r): array { return $r->validate(['name'=>'required|string|max:120','city'=>'nullable|string|max:100','postal_code_prefix'=>'nullable|string|max:12','service_id'=>'nullable|exists:services,id','fee'=>'required|numeric|min:0|max:100000','is_active'=>'nullable|boolean','notes'=>'nullable|string|max:500']) + ['is_active'=>$r->boolean('is_active')]; }
}
