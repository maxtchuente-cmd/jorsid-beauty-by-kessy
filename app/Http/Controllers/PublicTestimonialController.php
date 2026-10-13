<?php
namespace App\Http\Controllers;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
class PublicTestimonialController extends Controller {
 public function store(Request $request): RedirectResponse {
  $data=$request->validate(['customer_name'=>'required|string|max:120','city'=>'nullable|string|max:100','title'=>'nullable|string|max:150','rating'=>'required|integer|min:1|max:5','content'=>'required|string|min:10|max:2000','website'=>'nullable|max:0']);
  unset($data['website']); $data['is_published']=false; Testimonial::create($data);
  return back()->with('testimonial_success','Merci pour votre témoignage. Il sera visible après validation par le salon.');
 }
}
