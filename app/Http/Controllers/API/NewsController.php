<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use App\Serializers\DataArraySansIncludeSerializer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\NewsTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class NewsController extends Controller
{
    public function semua()
    {
        $paginator = News::orderBy('date', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(NewsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function get(News $news){
        return fractal()
            ->item($news)
            ->transformWith(NewsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function tambah(Request $request){
        $header = $request->header();

        //dd($header);

        $header['app-key'] = $header['app-key'] ?? null;

        $header['app-key'] = $header['app-key'] == null ? null : $header['app-key'][0];


        if ($header['app-key'] !== env('APP_KEY'))
            return response()->json(['error' => true, 'message' => 'Permission denied'], 403);

        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $request->content, $matches);
        $filename = $matches [1][0] ?? null;

        $image = $request->image == null ? $filename : $request->image;

        $news = News::create([
            'id_post' => $request->id_post,
            'date' => $request->date,
            'content' => $request->content,
            'title' => $request->title,
            'image' => $image,
            'slug' => $request->slug,
            'categories' => $request->categories
        ]);

        if (!$news)
            return response()->json(['error' => true], 500);

        $news = News::find($news->id);

        //TODO send notif
        $data = [
            'id' => $news->id,
            'pesan' => $news->title,
            'nama' => '',
            'waktu' => $news->date->format('Y-m-d H:i:s'),
            'image' => $news->image,
            'kategori' => count($news->categories) ? $news->categories[0]->name : ''
        ];

        $penerima = $this->masyarakat->ambilToken();
        $penerima = $penerima->merge($this->personil->ambilToken());
        $penerima = $penerima->merge($this->bhabin->ambilToken())->all();

        $this->kirimNotifikasiViaGcm('berita-baru', $data, $penerima);

        if (env('USE_ONESIGNAL', false)){
            $penerimaOneSignal = $this->masyarakat->ambilId();
            $penerimaOneSignal = $penerimaOneSignal->merge($this->personil->ambilId());
            $penerimaOneSignal = $penerimaOneSignal->merge($this->bhabin->ambilId());
            $this->kirimNotifikasiViaOnesignal('berita-baru', $data, $penerimaOneSignal);
        }

        return response()->json(['success' => true]);
    }

    public function testNotif(News $news)
    {
        //TODO send notif
        /*$data = [
            'id' => $news->id,
            'pesan' => $news->title,
            'waktu' => $news->date->format('Y-m-d H:i:s'),
            'image' => $news->image,
            'kategori' => count($news->categories) ? $news->categories[0]->name : ''
        ];

        $penerima = $this->masyarakat->ambilToken();

        $notif = $this->kirimNotifikasiAppViaGcm('berita-baru', $data, $penerima->all());

        dd($notif);*/
    }

    public function slide(){
        $news = News::orderBy('date', 'desc')->limit(5)->get();

        return fractal()
            ->collection($news)
            ->transformWith(NewsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
