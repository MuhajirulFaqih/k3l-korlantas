<?php
namespace App\Http\Controllers\API;

use App\Models\Sp2hp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
class Sp2hpController extends Controller
{
    public function __construct()
    {
        //$this->middleware('admin');
    }
    public function index()
    {
        $this->middleware('admin');
        $list_sp2hp = Sp2hp::get();
        return view('sp2hp.index', compact('list_sp2hp'));
    }
    public function create()
    {
        $this->middleware('admin');
        return view('sp2hp.create');
    }
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|min:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => array_flatten(
                    $validator->errors()->messages()
                ),
            ], 422);
        }
        return response()->json($this->get_sp2hp($request->all()), 200);
    }
    protected function get_sp2hp(array $data)
    {
        return Sp2hp::where('kode_unik', '=', $data['q'])->orWhere('no_telp', '=', $data['q'])->get();
    }
    public function store(Request $request)
    {
        $this->middleware('admin');
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()
                ->route('sp2hp.create')
                ->withErrors($validator)
                ->withInput();
        }
        $id = $this->buatSp2hp($request->all());
        if (!$id) {
            return redirect()
                ->route()
                ->with(['error' => 'SP2HP Gagal di buat.<br>Silahkan coba lagi']);
        }
        $sp2hp = Sp2hp::find($id);
        $message = view('sp2hp.sms', ['satuan' => $sp2hp->kesatuan, 'kode' => $sp2hp->kode_unik])->render();
        // Todo Send sms
        $smsRequest = $this->sendSms($sp2hp->no_telp, $message);
        $smsResponse = json_decode($smsRequest->getBody());
        $sp2hp->id_sms = $smsResponse->info->id;
        $sp2hp->status = $smsResponse->info->status;
        $sp2hp->sms = $message;
        $sp2hp->save();
        return redirect('sp2hp');
    }
    public function show(Sp2hp $sp2hp)
    {
        $this->middleware('admin');
        return view('sp2hp.show', compact('sp2hp'));
    }
    public function upload(Request $request){
        $header = $request->header();
        //dd($header);
        $header['app-key'] = $header['app-key'] ?? null;
        $header['app-key'] = $header['app-key'] == null ? null : $header['app-key'][0];
        //dd($header);
        if ($header['app-key'] !== env('APP_KEY'))
            return response()->json(['error' => true, 'message' => $header['app-key']], 403);
        // Todo Upload
        $file = $request->file->storeAs('sp2hp', str_random(40) . '.' . $request->file->extension());
        if (!$file)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        return response()->json(['success' => true, 'filename' => $file]);
    }
    public function smsCallback(Request $request){
        Log::info('callback', $request->all());
        $id = $request->id;
        $status = $request->status;
        $sp2hp = Sp2hp::where('id_sms', $id)->first();
        if ($sp2hp){
            $sp2hp->status = $status;
            $sp2hp->save();
        }
        return response()->json(['success' => true]);
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'pelapor' => 'required|min:3',
            'kasus' => 'required|min:6',
            // 'kode_unik' => 'required|min:3',
            'file' => 'required|mimes:pdf|max:24576',
            'no_surat' => 'required|min:6',
            'tgl_surat' => 'required|date_format:Y-m-d',
        ]);
    }
    protected function buatSp2hp(array $data)
    {
        $file = $data['file']->storeAs('sp2hp', str_random(40) . '.' . $data['file']->extension());
        
        do {
            $kode = strtoupper(bin2hex(random_bytes(4)));
        } while (Sp2hp::where('kode_unik', $kode)->count() > 0);
        $sp2hp = Sp2hp::create([
            'pelapor' => $data['pelapor'],
            'kasus' => $data['kasus'],
            'kode_unik' => $kode,
            'link' => $file,
            'kesatuan' => $data['satuan'],
            'no_telp' => $data['no_hp'],
            'no_surat' => $data['no_surat'],
            'tgl_surat' => $data['tgl_surat'],
        ]);
        if ($file && $sp2hp) {
            return $sp2hp->id;
        }
        return false;
    }
}