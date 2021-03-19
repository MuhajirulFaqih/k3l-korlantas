<?php

namespace App\Console\Commands;

use App\Kejadian;
use App\Personil;
use App\User;
use Illuminate\Console\Command;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class BroadcastKejadian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kejadian:broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast kejadian yang belum diverifikasi lebih dari sama dengan 5 menit';

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
    public function handle(Personil $personil)
    {
        $this->info('Broadcast Start');
        //Ambil kejadian 5 menit yang lalu yang belum di verifikasi
        $kejadian = Kejadian::whereNull('verifikasi')
                            ->where('created_at', '<=', \Carbon\Carbon::now()->subMinute(5))->get();
        $id = [];
        foreach ($kejadian as $key) {
            $id[] = $key->id;
            $data = [
                'id' => $key->id,
                'pesan' => 'Kejadian baru',
                'kejadian' => $key->kejadian,
                'lokasi' => $key->lokasi,
                'nama' => $key->user->nama,
                'jabatan' => $key->user->jabatan,
                'foto' => $key->user->foto,
                'lat' => $key->lat,
                'lng' => $key->lng
            ];

            $event = "kejadian-baru";

            $penerima = $personil->ambilTokenById($key->nearby->pluck('id_personil')->toArray());

            $this->kirimNotifikasiViaGcm($event, $data, $penerima->all());
        }
        Kejadian::whereIn('id', $id)->update(['verifikasi' => 1]);
        $this->info('Broadcast Finish');
    }

    public function kirimNotifikasiViaGcm ($event, $data, $penerima) {
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
