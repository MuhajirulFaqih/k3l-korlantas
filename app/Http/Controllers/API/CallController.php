<?php

namespace App\Http\Controllers\API;

use App\Models\CallLog;
use App\Events\CallAdminEvent;
use App\Events\CallReady;
use App\Http\Controllers\Controller;
use App\Models\Personil;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\CallTransformer;
use App\Transformers\PersonilTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CallController extends Controller
{
    public function getCall(Request $request){
        $user = $request->user();

        $call = $request->filter == '' ? 
                    
                    CallLog::where('id_from', $user->id)
                            ->orWhere('id_to', $user->id)
                            ->orderBy('created_at', 'DESC') :

                    CallLog::whereIn('id', CallLog::search($request->filter)->get()->pluck('id'))
                            ->where(function($query) use($user) {
                                $query->where('id_from', $user->id);
                                $query->orWhere('id_to', $user->id);
                            })
                            ->orderBy('created_at', 'DESC') ;

        $paginator = $call->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(CallTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function createCallFromPersonil(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = $user->pemilik;

        $user = $request->id_user;

        $formatedPersonil = fractal()
            ->item($personil)
            ->transformWith(PersonilTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        broadcast(new CallAdminEvent($user, $formatedPersonil));

        return response()->json(['success' => true]);
    }

    public function createCall(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang!!'], 403);

        $personil = Personil::find($request->id_personil);

        $to = $personil->auth ?? $personil->bhabin->auth;

        if (!$to)
            return response()->json(['error' => 'Personil tidak ditemukan'], 404);

        $log = CallLog::create([
            'from' => $user->username,
            'to' => $to->username,
            'id_from' => $user->id,
            'id_to' => $to->id
        ]);
    

        if(!$log)
            return response()->json(['error' => 'terjadi kesalahan'], 500);

        if (env('USE_ONESIGNAL', false)){
            $this->kirimNotifikasiViaOnesignal('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->id]);
        }

        $this->kirimNotifikasiViaGcm('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->fcm_id]);
        
        return response()->json(['success' => true, 'id' => $log->id]);
    }

    public function updateCall(Request $request){
        $user = $request->user();

        // if ($user->jenis_pemilik !== 'admin')
        //     return response()->json(['error' => 'Terlarang!!'], 403);

        $call = CallLog::find($request->id);

        if ($call){
            if ($request->status == 'end')
                exec("sudo service kurento-media-server restart");
            $update = $request->status == 'start'? ['start' => \Carbon\Carbon::now()] : ['end' => \Carbon\Carbon::now()];
            if($call->update($update))
                return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Terjadi Kesalahan'], 500);
    }

    public function ready(Request $request){
        $user = $request->user();

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        event(new CallReady(['id_personil' => $personil->id ]));
        
        return response()->json(['success' => true]);
    }
}
