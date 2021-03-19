<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;
use Illuminate\Http\UploadedFile;

class UploadController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function upload(Request $request){
        $request->validate([
            'file' => 'required|file',
        ]);

        $name = str_random(25);
        $foto = $request->file('file')
                        ->storeAs('Video',$name. '.' . $request->file('video')
                        ->extension());
        return response()->json($foto,200);
    }
}
