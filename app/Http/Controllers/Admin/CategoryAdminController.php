<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
class CategoryAdminController extends Controller
{
    public function index(): View { return view('admin.categories.index', ['serviceCategories'=>ServiceCategory::withCount('services')->orderBy('sort_order')->orderBy('name')->get(), 'galleryCategories'=>GalleryCategory::withCount('items')->orderBy('sort_order')->orderBy('name')->get()]); }
    public function storeService(Request $request): RedirectResponse { $v=$request->validate(['name'=>'required|string|max:100|unique:service_categories,name','name_de'=>'nullable|string|max:120','name_fr'=>'nullable|string|max:120','name_en'=>'nullable|string|max:120','sort_order'=>'nullable|integer|min:0']); ServiceCategory::create($v+['sort_order'=>$v['sort_order']??0]); return back()->with('success','Catégorie de prestation créée.'); }
    public function updateService(Request $request, ServiceCategory $category): RedirectResponse { $v=$request->validate(['name'=>'required|string|max:100|unique:service_categories,name,'.$category->id,'name_de'=>'nullable|string|max:120','name_fr'=>'nullable|string|max:120','name_en'=>'nullable|string|max:120','sort_order'=>'nullable|integer|min:0']); $category->update($v); $category->services()->update(['category'=>$v['name']]); return back()->with('success','Catégorie de prestation mise à jour.'); }
    public function deleteService(ServiceCategory $category): RedirectResponse { if($category->services()->exists()) return back()->with('error','Cette catégorie contient des prestations. Réaffectez-les avant de la supprimer.'); $category->delete(); return back()->with('success','Catégorie supprimée.'); }
    public function storeGallery(Request $request): RedirectResponse { $v=$request->validate(['name'=>'required|string|max:100|unique:gallery_categories,name','name_de'=>'nullable|string|max:120','name_fr'=>'nullable|string|max:120','name_en'=>'nullable|string|max:120','sort_order'=>'nullable|integer|min:0']); GalleryCategory::create($v+['sort_order'=>$v['sort_order']??0]); return back()->with('success','Catégorie de galerie créée.'); }
    public function updateGallery(Request $request, GalleryCategory $category): RedirectResponse { $v=$request->validate(['name'=>'required|string|max:100|unique:gallery_categories,name,'.$category->id,'name_de'=>'nullable|string|max:120','name_fr'=>'nullable|string|max:120','name_en'=>'nullable|string|max:120','sort_order'=>'nullable|integer|min:0']); $category->update($v); $category->items()->update(['category'=>$v['name']]); return back()->with('success','Catégorie de galerie mise à jour.'); }
    public function deleteGallery(GalleryCategory $category): RedirectResponse { if($category->items()->exists()) return back()->with('error','Cette catégorie contient des photos. Réaffectez-les avant de la supprimer.'); $category->delete(); return back()->with('success','Catégorie de galerie supprimée.'); }
}
