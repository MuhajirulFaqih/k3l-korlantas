<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HTChannels;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\HTChannelsTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class HTController extends Controller
{
    public function getAllChannels(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $channels = HTChannels::getAll()->get();

        return response()->json($channels);
    }

    public function getByKesatuan(Request $request){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        $channels = HTChannels::whereIn('channel_id', $personil->kesatuan->htchannel->pluck('id_channel')->all())->get();

        return response()->json($channels);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $paginator = $request->filter == '' ?
            HTChannels::where('used_in', env('HT_INITIALIZATION', null))->orderBy($orderBy, $direction)->paginate(10) :
            HTChannels::where('used_in', env('HT_INITIALIZATION', null))
                        ->whereRaw("lower(name) LIKE ?", ['%' . strtolower($request->filter) . '%'])
                        ->orderBy($orderBy, $direction)
                        ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new HTChannelsTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function store(Request $request)
    {
    	$user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $validatedData = $request->validate([
            'channel'      => 'required',
        ]);

        $latestChannelId = HTChannels::orderBy('channel_id', 'DESC')->first();

        $channel = HTChannels::insert([
            'server_id' => 1,
            'channel_id' => ($latestChannelId->channel_id + 1),
            'parent_id' => 0,
            'name' => $request->channel,
            'inheritacl' => 1,
            'used_in' => env('HT_INITIALIZATION', null),
        ]);

        if(!$channel) {
            return response()->json(['error' => 'terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $validatedData = $request->validate([
            'channel_id'      => 'required',
            'channel'      => 'required',
        ]);

        $channel = HTChannels::where('channel_id', $id)->update([
            'name' => $request->channel,
        ]);

        if(!$channel) {
            return response()->json(['error' => 'terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    public function delete($id)
    {
        $channel = HTChannels::where('channel_id', $id);
        if(!$channel->delete()) {
            return response()->json(['error' => 'terjadi kesalahan saat menghapus data'], 500);
        }
    }
}
