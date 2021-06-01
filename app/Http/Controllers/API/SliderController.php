<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
    	$user = request()->user();
    	if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki akses '], 403);

        $slider = Slider::all();
        $response = [];
        foreach ($slider as $key) {
            $response[] = [ 'id' => $key->id, 'foto' => ($key->foto ? url('api/upload/' . $key->foto) : null) ];
        }

        if (count($slider) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        return response()->json($response);

    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $validatedData = $request->validate([
            'foto' => 'required|image',
        ]);

        $foto = null;

        if (isset($validatedData['foto'])) {
            $foto = $validatedData['foto']
                ->storeAs('slider', $user->id . '_' . Str::random(40) . '.' .
                    $validatedData['foto']->extension());
        }

        $data = [ 'foto' => $foto, ];

        Slider::create($data);

        return response()->json(['success' => true], 201);
    }

    public function destroy(Slider $slider)
    {
        if($slider->delete()) {
            Storage::delete($slider->foto);
            return response()->json(['success' => true], 201);
        }
    }
}
