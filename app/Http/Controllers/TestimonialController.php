<?php
namespace App\Http\Controllers;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
class TestimonialController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $v=$request->validate(['customer_name'=>'required|string|max:120','title'=>'nullable|string|max:160','city'=>'nullable|string|max:100','rating'=>'required|integer|min:1|max:5','content'=>'required|string|min:10|max:2000','privacy_consent'=>'accepted'], ['privacy_consent.accepted'=>'Veuillez confirmer que vous acceptez le traitement de votre témoignage.']);
        unset($v['privacy_consent']); $v['is_published']=false; Testimonial::create($v);
        return back()->with('testimonial_success','Merci pour votre témoignage. Il sera publié après vérification.');
    }
}
