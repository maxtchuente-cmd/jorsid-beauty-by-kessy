<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
class TranslationController extends Controller
{
    public function translate(Request $request)
    {
        $v=$request->validate(['text'=>'required|string|max:5000','source'=>'required|in:de,fr,en']);
        $endpoint=trim((string)config('jorsid.translation_endpoint',''));
        if($endpoint==='') return response()->json(['message'=>'Le service de traduction n’est pas configuré. Ajoutez JORSID_TRANSLATION_URL dans le fichier .env, puis relancez la configuration Laravel.'],422);
        $targets=array_values(array_diff(['de','fr','en'],[$v['source']])); $translations=[];
        foreach($targets as $target){
            try {
                $payload=['q'=>$v['text'],'source'=>$v['source'],'target'=>$target,'format'=>'text'];
                $key=(string)config('jorsid.translation_api_key',''); if($key!=='') $payload['api_key']=$key;
                $response=Http::timeout(12)->acceptJson()->post($endpoint,$payload);
                if(!$response->successful() || !is_string($response->json('translatedText'))) throw new \RuntimeException('Réponse de traduction invalide.');
                $translations[$target]=$response->json('translatedText');
            } catch(\Throwable $e) {
                report($e);
                return response()->json(['message'=>'La traduction automatique a échoué. Le texte original a été conservé ; vérifiez le service configuré et réessayez.'],502);
            }
        }
        return response()->json(['source'=>$v['source'],'translations'=>$translations]);
    }
}
