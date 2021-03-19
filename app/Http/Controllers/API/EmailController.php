<?php

namespace App\Http\Controllers\API;

use App\Models\EmailTr;
use App\Models\Personil;
use App\Models\Kesatuan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\EmailTrTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class EmailController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil','bhabin']))
            return response()->json(['Anda tidak memiliki akses ke halaman ini'], 403);

        $paginator = EmailTr::filter($user->pemilik instanceof Personil ? $user->pemilik->kesatuan->id : $user->pemilik->personil->kesatuan->id)->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(EmailTrTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function get(Request $request, EmailTr $email){
        $user = $request->user();
        $kesatuan = $user->pemilik instanceof Personil ? $user->pemilik->kesatuan : $user->pemilik->personil->kesatuan;


        // Check jika user bukan personil dan user tidak memiliki aksess ke email tersebut
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']) || !$email->penerima->where('id_kesatuan', $kesatuan->id)->first())
            return response()->json(['Anda tidak memiliki aksess ke halaman ini'], 403);

        return fractal()
            ->item($email)
            ->transformWith(EmailTrTransformer::class)
            ->parseIncludes('attachment')
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function tambah(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang !!'], 403);

        $validData = $request->validate([
            'pengirim' => 'required',
            'nomor' => 'required',
            'w_email' => 'required',
            'id_email' => 'required',
            'attachments.*' => 'required',
            'to.*' => 'required'
        ]);


        $emailTr = EmailTr::create([
            'nomor' => $validData['nomor'],
            'pengirim' => $validData['pengirim'],
            'w_email' => $validData['w_email'],
            'id_email' => $validData['id_email']
        ]);

        if (count($validData['attachments'])) {
            foreach ($validData['attachments'] as $attachment) {
                $file = $attachment->storeAs(
                    'email', str_random(40).'.'.$attachment->extension()
                );

                $emailTr->attachment()->create([
                    'file' => $file,
                    'format' => $attachment->extension()
                ]);
            }
        }

        foreach($validData['to'] as $to){
            $kesatuan = Kesatuan::where('email_polri', $to)->first();

            $emailTr->penerima()->create([
                'email' => $to,
                'id_kesatuan' => optional($kesatuan)->id
            ]);

            if ($kesatuan) {
                $penerima = (new Kesatuan())->ambilToken($kesatuan)->all();
                $penerimaOneSignal = (new Kesatuan())->ambilId($kesatuan)->all();

                if (env('USE_ONESIGNAL', false))
                    $this->kirimNotifikasiViaOnesignal('tr-baru', [
                        'id' => $emailTr->id,
                        'pesan' => "Surat Telegram baru",
                        'nama' => $emailTr->pengirim
                    ], $penerimaOneSignal);

                $this->kirimNotifikasiViaGcm('tr-baru', [
                    'id' => $emailTr->id,
                    'pesan' => "Surat Telegram baru",
                    'nama' => $emailTr->pengirim
                ], $penerima);
            }
        }


        return response()->json(['success' => true]);
    }
}
