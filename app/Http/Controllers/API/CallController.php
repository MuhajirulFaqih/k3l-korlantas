<?php

namespace App\Http\Controllers\API;

use App\Events\CallAdminEvent;
use App\Events\CallReady;
use App\Events\NotifyAdminReadyEvent;
use App\Events\NotifyAdminRejectEvent;
use App\Events\RejectCallEvent;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CallController extends Controller
{
    public function requestCallFromAdminKesatuan(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 404);

        $callLog = CallLog::where('id_admin', $user->id)->where('is_calling', true)->first();

        if (!$callLog) {
            $callLog = CallLog::create([
                'id_admin' => $user->id
            ]);
        }

        $responseSession = Http::withBasicAuth("OPENVIDUAPP", env("OPENVIDU_SECRET"))->withHeaders([
            'Content-Type' => 'application/json'
        ])
            ->post(env('OPENVIDU_URL') . "/openvidu/api/sessions", [
                'customSessionId' => 'session' . $callLog->id,
                'recordingMode' => 'ALWAYS'
            ]);

        if ($responseSession->status() == 200) {
            $responseSessionJson = $responseSession->json();
            $callLog->update([
                'session_id' => $responseSessionJson['id'],
                'custom_session_id' => 'session' . $callLog->id,
                'startTime' => Carbon::now()
            ]);
        }

        $responseToken = Http::withBasicAuth("OPENVIDUAPP", env("OPENVIDU_SECRET"))
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(env('OPENVIDU_URL') . '/openvidu/api/sessions/session' . $callLog->id . '/connection', [
                'type' => 'WEBRTC',
                'data' => '',
                'role' => 'PUBLISHER'
            ]);

        $responseTokenJson = $responseToken->json();
        if ($responseToken->ok()) {
            $participant = $callLog->participants()->where('id_user', $user->id)->first();
            if ($participant)
                $participant->update([
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status']
                ]);
            else
                $callLog->participants()->create([
                    'id_user' => $user->id,
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status'],
                    'active_at' => Carbon::now()
                ]);

            return response()->json($responseTokenJson);
        }

        return response()->json(['error' => 'Terjadi kesalahan']);
    }

    public function createCallFromPersonil(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = $user->pemilik;

        $id_admin = $request->id_admin;

        $callLog = CallLog::where('id_admin', $id_admin)->where('is_calling', true)->first();

        if (!$callLog){
            $formatedPersonil = fractal()
                ->item($personil)
                ->transformWith(PersonilTransformer::class)
                ->serializeWith(DataArraySansIncludeSerializer::class)
                ->toArray();

            broadcast(new CallAdminEvent($id_admin, $formatedPersonil));

            return response()->json(['success' => true, 'status' => 'Sending call request']);
        }

        $responseToken = Http::withBasicAuth("OPENVIDUAPP", env("OPENVIDU_SECRET"))
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(env('OPENVIDU_URL') . '/openvidu/api/sessions/session' . $callLog->id . '/connection', [
                'type' => 'WEBRTC',
                'data' => '',
                'role' => 'PUBLISHER'
            ]);

        $responseTokenJson = $responseToken->json();
        if ($responseToken->ok()) {
            $participant = $callLog->participants()->where('id_user', $user->id)->first();
            if ($participant)
                $participant->update([
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status']
                ]);
            else
                $callLog->participants()->create([
                    'id_user' => $user->id,
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status'],
                    'active_at' => Carbon::now()
                ]);

            return response()->json($responseTokenJson);
        }

        return response()->json(['error' => 'Terjadi kesalahan']);
    }

    public function endSession(Request $request, $session_id){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['kesatuan', 'admin', 'personil']))
            return response()->json(['error' => "error"]);

        $callLog = CallLog::where('session_id', $session_id)->first();

        if (!$callLog)
            return response()->json(['error' => 'Call not found'], 404);

        $callLog->update([
            'is_calling' => false
        ]);

        $responseHttp = Http::withBasicAuth("OPENVIDUAPP", env("OPENVIDU_SECRET"))->delete(env('OPENVIDU_URL').'/openvidu/api/sessions/'.$session_id);

        Log::info("Response Http ". $responseHttp->body());

        if (!$responseHttp->status() == 204)
            return response()->json(['error' => 'Terjadi kesalahan'], $responseHttp->status());

        return response()->json(['success' => true]);
    }

    public function notifyPersonil(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = Personil::where('nrp', $request->nrp)->first();

        if (!$personil)
            return response()->json(['error' => 'Personil tidak ditemukan'], 404);


        if ($request->ready){
            $this->sendSocketToPersonil($request, $personil);
        } else if ($request->rejected){
            event(new NotifyAdminRejectEvent($personil->auth->id));
        } else {
            $data = [
                'pesan' => 'Panggilan masuk',
                'nama' => $user->jenis_pemilik == 'kesatuan' ? $user->pemilik->kesatuan : $user->pemilik->nama,
                'id' => $user->id,
                'w_notif' => Carbon::now('UTC')->timestamp,
                'url' => url('ios-video-call?id=').$user->id.'&incoming=true',
                'sessionId' => $request->session_id
            ];

            $this->kirimNotifikasiViaOnesignal('incoming-vcon', $data, [$personil->auth->id]);
        }

        return response()->json(['success' => true]);
    }

    private function sendSocketToPersonil(Request $request, Personil $personil){
        Log::info("sendSocketToPersonil", $personil->toArray());
        $callLog = CallLog::where('session_id', $request->session_id)->first();
        $responseToken = Http::withBasicAuth("OPENVIDUAPP", env("OPENVIDU_SECRET"))
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(env('OPENVIDU_URL') . '/openvidu/api/sessions/session' . $callLog->id . '/connection', [
                'type' => 'WEBRTC',
                'data' => '',
                'role' => 'PUBLISHER'
            ]);

        $token = [];
        $responseTokenJson = $responseToken->json();
        if ($responseToken->ok()) {
            $participant = $callLog->participants()->where('id_user', $personil->auth->id)->first();
            if ($participant)
                $participant->update([
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status']
                ]);
            else
                $callLog->participants()->create([
                    'id_user' => $personil->auth->id,
                    'connection_id' => $responseTokenJson['id'],
                    'status' => $responseTokenJson['status'],
                    'active_at' => Carbon::now()
                ]);

            $token = $responseTokenJson;
        }
        event(new NotifyAdminReadyEvent($token, $personil->id));
    }

    public function rejectCall(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        event(new RejectCallEvent($request->id_admin));

        return response()->json(['success' => true]);
    }

    public function getCall(Request $request)
    {
        $user = $request->user();

        $call = $request->filter == '' ?

            CallLog::where('id_from', $user->id)
                ->orWhere('id_to', $user->id)
                ->orderBy('created_at', 'DESC') :

            CallLog::whereIn('id', CallLog::search($request->filter)->get()->pluck('id'))
                ->where(function ($query) use ($user) {
                    $query->where('id_from', $user->id);
                    $query->orWhere('id_to', $user->id);
                })
                ->orderBy('created_at', 'DESC');

        $paginator = $call->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(CallTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function cancelCall(Request $request)
    {
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
            $this->kirimNotifikasiViaOnesignal('cancel-vcal', ['pesan' => 'Cancel Call'], [$notifyUser->id]);

        if ($notifyUser->fcm_id)
            $this->kirimNotifikasiViaGcm('cancel-vcal', ['pesan' => 'Cancel Call'], [$notifyUser->fcm_id]);

        return response()->json(['success' => true]);
    }

    public function createCall(Request $request)
    {
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

        if (!$log)
            return response()->json(['error' => 'terjadi kesalahan'], 500);

        if (env('USE_ONESIGNAL', false))
            $this->kirimNotifikasiViaOnesignal('incoming-call', ['pesan' => 'Incoming call ' . $user->username, 'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username, 'datetime' => Carbon::now()->toDateTimeString()], [(string)$to->id]);

        $this->kirimNotifikasiViaGcm('incoming-call', ['pesan' => 'Incoming call ' . $user->username, 'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username, 'datetime' => Carbon::now()->toDateTimeString()], [$to->fcm_id]);

        return response()->json(['success' => true, 'id' => $log->id]);
    }

    public function updateCall(Request $request)
    {
        $user = $request->user();

        // if ($user->jenis_pemilik !== 'admin')
        //     return response()->json(['error' => 'Terlarang!!'], 403);

        if ($request->status == 'start') {
            $pemilik = $request->jenis == 'masyarakat' ? Masyarakat::find($request->id_user) : Personil::find($request->id_user);

            $to = $pemilik->auth;

            if (!$to)
                return response()->json(['error' => $request->jenis . ' tidak ditemukan'], 404);

            $call = CallLog::create([
                'from' => $user->username,
                'to' => $to->uername,
                'id_from' => $user->id,
                'id_to' => $to->id,
            ]);

            $user->pemilik->update(['in_call', true]);
        } else {
            $call = CallLog::where($request->id);

            if ($call) {
                /*if ($request->status == 'end')
                    exec("sudo service kurento-media-server restart");*/
                $user->pemilik->update(['in_call', false]);
                $update = $request->status == 'start' ? ['start' => \Carbon\Carbon::now()] : ['end' => \Carbon\Carbon::now()];
                if ($call->update($update))
                    return response()->json(['success' => true]);
            }

        }

        return response()->json(['error' => 'Terjadi Kesalahan'], 500);
    }

    public function ready(Request $request)
    {
        $user = $request->user();

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        event(new CallReady(['id_personil' => $personil->id]));

        return response()->json(['success' => true]);
    }
}
