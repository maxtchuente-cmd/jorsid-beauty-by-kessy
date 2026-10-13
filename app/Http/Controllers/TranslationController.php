<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
class TranslationController extends Controller {
 public function translate(Request $request): JsonResponse {
  $v=$request->validate(['text'=>'required|string|max:5000','source'=>'required|in:de,fr,en','targets'=>'required|array|min:1|max:2','targets.*'=>'required|in:de,fr,en|distinct']);
  if(in_array($v['source'],$v['targets'],true)) return response()->json(['message'=>'La langue source ne doit pas être une langue cible.'],422);
  $key=config('services.deepl.key');
  if(!$key) return response()->json(['message'=>'Le service de traduction n’est pas configuré. Ajoutez DEEPL_AUTH_KEY dans .env côté serveur. Le texte saisi est conservé.'],503);
  $endpoint=str_contains((string)config('services.deepl.endpoint'),'api-free')?'https://api-free.deepl.com/v2/translate':'https://api.deepl.com/v2/translate';
  try {
   $response=Http::timeout(12)->withHeaders(['Authorization'=>'DeepL-Auth-Key '.$key])->asForm()->post($endpoint,['text'=>$v['text'],'source_lang'=>strtoupper($v['source']),'target_lang'=>strtoupper($v['targets'][0]),'preserve_formatting'=>1]);
   if(!$response->successful()) return response()->json(['message'=>'Le fournisseur de traduction a refusé la demande. Réessayez plus tard.'],502);
   $translations=[$v['targets'][0]=>$response->json('translations.0.text')];
   foreach(array_slice($v['targets'],1) as $target) {
    $r=Http::timeout(12)->withHeaders(['Authorization'=>'DeepL-Auth-Key '.$key])->asForm()->post($endpoint,['text'=>$v['text'],'source_lang'=>strtoupper($v['source']),'target_lang'=>strtoupper($target),'preserve_formatting'=>1]);
    if(!$r->successful()) return response()->json(['message'=>'Une traduction est temporairement indisponible. Le texte source n’a pas été modifié.'],502);
    $translations[$target]=$r->json('translations.0.text');
   }
   return response()->json(['source'=>$v['source'],'translations'=>$translations]);
  } catch(\Throwable $e) { report($e); return response()->json(['message'=>'Le service de traduction est temporairement inaccessible.'],503); }
 }
}
