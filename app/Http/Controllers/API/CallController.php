<?php

namespace App\Http\Controllers\API;

use App\Events\CallAdminEvent;
use App\Events\CallReady;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CallLog;
use App\Models\Masyarakat;
use App\Models\Personil;
use App\Models\User;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\CallTransformer;
use App\Transformers\MasyarakatTransformer;
use App\Transformers\PersonilTransformer;
use Carbon\Carbon;
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

        if ($user->jenis_pemilik !== 'masyarakat')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = $user->pemilik;

        $admin = Admin::where('status', true)->where('in_call', false)->where('visiblility', true)->first();

        if (!$admin)
            return response()->json(['error' => 'Panggilan sibuk. Silahkan ulangi beberapa saat'], 404);

        $user = $admin->auth;

        $formatedPersonil = fractal()
            ->item($personil)
            ->transformWith(MasyarakatTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        broadcast(new CallAdminEvent($user->id, $formatedPersonil));

        return response()->json(['success' => true, 'username' => $user->username]);
    }

    public function cancelCall(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validateData = $request->validate([
            'username' => 'required'
        ]);

        $notifyUser = User::where('username', $validateData['username'])->first();

        if (!$notifyUser)
            return response()->json(['error' => "Not Found"], 404);

        if (env('USE_ONESIGNAL', false))
            $this->kirimNotifikasiViaOnesignal('cancel-call', ['pesan' => 'Cancel call'], [$notifyUser->id]);

        if ($notifyUser->fcm_id)
            $this->kirimNotifikasiViaGcm('cancel-call', ['pesan' => 'Cancel call'], [$notifyUser->fcm_id]);

        return response()->json(['success' => true]);
    }

    public function createCall(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang!!'], 403);

        $personil = Personil::find($request->id_personil);

        $to = $personil->auth;

        if (!$to)
            return response()->json(['error' => 'Personil tidak ditemukan'], 404);

        $log = CallLog::create([
            'from' => $user->username,
            'to' => $to->username,
            'id_from' => $user->id,
            'id_to' => $to->id
        ]);

        $user->pemilik->update(['in_call', true]);

        if(!$log)
            return response()->json(['error' => 'terjadi kesalahan'], 500);

        if (env('USE_ONESIGNAL', false))
            $this->kirimNotifikasiViaOnesignal('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username, 'datetime' => Carbon::now()->toDateTimeString()], [(string) $to->id]);

        $this->kirimNotifikasiViaGcm('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username, 'datetime' => Carbon::now()->toDateTimeString()], [$to->fcm_id]);

        return response()->json(['success' => true, 'id' => $log->id]);
    }

    public function updateCall(Request $request){
        $user = $request->user();

        // if ($user->jenis_pemilik !== 'admin')
        //     return response()->json(['error' => 'Terlarang!!'], 403);

        if ($request->status == 'start'){
            $pemilik = $request->jenis == 'masyarakat' ? Masyarakat::find($request->id_user) : Personil::find($request->id_user);

            $to = $pemilik->auth;

            if (!$to)
                return response()->json(['error' => $request->jenis.' tidak ditemukan'], 404);

            $call = CallLog::create([
               'from' => $user->username,
               'to' => $to->uername,
               'id_from' => $user->id,
                'id_to' => $to->id,
            ]);

            $user->pemilik->update(['in_call', true]);
        } else {
            $call = CallLog::where($request->id);

            if ($call){
                /*if ($request->status == 'end')
                    exec("sudo service kurento-media-server restart");*/
                $user->pemilik->update(['in_call', false]);
                $update = $request->status == 'start'? ['start' => \Carbon\Carbon::now()] : ['end' => \Carbon\Carbon::now()];
                if($call->update($update))
                    return response()->json(['success' => true]);
            }

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
