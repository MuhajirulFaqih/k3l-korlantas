<?php

namespace App\Console\Commands;

use App\Bhabin;
use App\Masyarakat;
use App\News;
use App\Personil;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class GrabHargaPangan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grab:hargapangan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengambil dftar harga pangan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDate = Carbon::now();

        // Jika hari sabtu atau minggu tidak perlu ambil data

        if (in_array($currentDate->dayOfWeek, [6, 7]))
            return;

        $pangan = json_decode(config('pengaturan.harga_pangan'));

        $this->info('Grabbing data');

        $client = new Client();

        foreach ($pangan as $row){
            $response = [];

            $response[] = "<h3>".$row->nama."</h3>";

            foreach ($row->markets as $market){
                $responseServer = $client->get("http://hargapangan.id/getjson?task=json.statProvinceByRegency&regency_id=".$row->id."&market_id=".$market."&date=".$currentDate->toDateString());

                $responseBody = json_decode($responseServer->getBody());

                $result = $responseBody->result;

                $view = view('others.hargapangan', (array) $result);

                $response[] = $view;
            }

            $response[] = "Sumber : <a href='http://hargapangan.id'>hargapangan.id</a>";

            $news = News::create([
                'title' => 'Daftar Harga Pangan '.$row->nama.' tanggal '.$currentDate->format('d M Y'),
                'content' => implode("<br>", $response),
                'image' => "http://rifan-financindo.com/wp-content/uploads/2014/08/PT.-RIFAN-FINANCINDO-BERJANGKA-JAKARTA90.jpg",
                'slug' => 'daftar-harga-pangan-'.$currentDate->toDateString(),
                'categories' => '[{"term_id": 0,"name":"BERITA HARGA PANGAN","slug":"berita-harga-pangan","term_group":0,"term_taxonomy_id":221,"taxonomy":"category","description":"","parent":0,"count":13158,"filter":"raw","cat_ID":22222,"category_count":13158,"category_description":"","cat_name":"BERITA HARGA PANGAN","category_nicename":"berita-harga-pangan","category_parent":0}]',
                'date' => $currentDate
            ]);

            //TODO send notif
            $data = [
                'id' => $news->id,
                'pesan' => $news->title,
                'nama' => '',
                'waktu' => $news->date->format('Y-m-d H:i:s'),
                'image' => $news->image,
                'kategori' => count($news->categories) ? $news->categories[0]->name : ''
            ];

            $penerima = (new Masyarakat())->ambilToken();
            $penerima = $penerima->merge((new Personil())->ambilToken());
            $penerima = $penerima->merge((new Bhabin())->ambilToken())->all();

            $this->kirimNotifikasiViaGcm('berita-baru', $data, $penerima);
        }

        $this->info("Grabing finish");
    }

    public function kirimNotifikasiViaGcm ($event, $data, $penerima){
        $optionBuilder = (new OptionsBuilder())->setTimeToLive(60*20);

        $notificationBuilder = (new PayloadNotificationBuilder())
            ->setBody($data)
            ->setTitle($event);
        $dataBuilder = (new PayloadDataBuilder())
            ->addData(["event" => $event, 'data' => $data]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if(count($penerima)){
            $downstreamResponse = FCM::sendTo($penerima, $option, null, $data);

            if ($downstreamResponse->numberFailure() > 0 || $downstreamResponse->numberModification() > 0) {
                $tokenToDelete = collect($downstreamResponse->tokensToDelete());
                $tokenToDelete = $tokenToDelete->merge(collect($downstreamResponse->tokensWithError())->map(function ($key, $value){ return $key; }));

                User::whereIn('fcm_id', $tokenToDelete)->update(['fcm_id' => null]);

                foreach ($downstreamResponse->tokensToModify() as $key => $value){
                    User::where('fcm_id', $key)->update(['fcm_id' => $value]);
                }
            }
            return compact('downstreamResponse', 'penerima', 'tokenToDelete');
        }

        return null;
    }
}
