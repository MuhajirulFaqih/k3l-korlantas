<?php

namespace App\Console\Commands;

use App\EmailTr;
use App\Kesatuan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use App\Personil;

class EmailGrabber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:grab';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grab email polri';

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
        $this->info("Running email grabber");

        $oClient = new Client();

        try {
            $this->info("Connecting email client");

            $oClient->connect();

            $this->info("Getting sent folder");

            $aFolder = $oClient->getFolder('Sent');

            $lastGrab = EmailTr::orderBy('created_at', 'desc')->first();

            $messages = $aFolder->query()->since($lastGrab ? Carbon::parse($lastGrab->w_email)->addMinute(1) : Carbon::now()->subHours(20))->get();

            $this->info("Found ".$messages->count()." new TR");

            foreach ($messages as $msg){
                $checkEmail = EmailTr::where('nomor', $msg->getSubject())->where('w_email', $msg->getDate())->first();

                if ($checkEmail)
                    continue;

                $emailTr = EmailTr::create([
                    'pengirim' => env('IMAP_USERNAME'),
                    'nomor' => $msg->getSubject(),
                    'w_email' => $msg->getDate(),
                    'id_email' => $msg->getMessageId()
                ]);

                if ($msg->hasAttachments()){
                    // Save Attachment

                    foreach ($msg->getAttachments()  as $attachment){
                        $extension = $attachment->getExtension();
                        $path = storage_path('app/email');
                        $filename = str_random(40).'.'.$extension;
                        if($attachment->save($path, $filename))
                            $emailTr->attachment()->create([
                                'file' => str_replace(storage_path('app/'), '', $path).'/'.$filename,
                                'format' => $extension,
                                'nama' => $attachment->getName()
                            ]);
                    }
                }

                foreach ($msg->getTo() as $to){
                    $kesatuan = Kesatuan::where('email_polri', $to->mail)->first();

                    $to = $emailTr->penerima()->create([
                        'email' => $to->mail,
                        'id_kesatuan' => optional($kesatuan)->id
                    ]);

                    if($kesatuan){
                        $penerima = (new Kesatuan())->ambilToken($kesatuan)->all();

                        $this->kirimNotifikasiViaGcm('tr-baru', [
                            'id' => $emailTr->id,
                            'pesan' => "Surat Telegram baru",
                            'nama' => $emailTr->pengirim
                        ], $penerima);
                    }
                }

                $this->info("[".$msg->getMessageId()."-".$msg->getSubject()."] Berhasil disimpan");
            }

            $this->info("Disconnecting email client");

            $oClient->disconnect();

        } catch (ConnectionFailedException $e) {
            $this->error("Error ".$e->getMessage(), $e->getTraceAsString());
        } catch (GetMessagesFailedException $e) {
            $this->error($e->getMessage(), $e->getTraceAsString());
        }

        $this->info("Grabbing finish");
    }

    public function kirimNotifikasiViaGcm($event, $data, $penerima)
    {
        $optionBuilder = (new OptionsBuilder())->setTimeToLive(60 * 20);

        $notificationBuilder = (new PayloadNotificationBuilder())
            ->setBody($data)
            ->setTitle($event);
        $dataBuilder = (new PayloadDataBuilder())
            ->addData(["event" => $event, 'data' => $data]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if (count($penerima)) {
            $downstreamResponse = \FCM::sendTo($penerima, $option, null, $data);

            if ($downstreamResponse->numberFailure() > 0 || $downstreamResponse->numberModification() > 0) {
                $tokenToDelete = collect($downstreamResponse->tokensToDelete());
                $tokenToDelete = $tokenToDelete->merge(collect($downstreamResponse->tokensWithError())->map(function ($key, $value) {
                    return $key;
                }));

                User::whereIn('fcm_id', $tokenToDelete)->update(['fcm_id' => null]);

                foreach ($downstreamResponse->tokensToModify() as $key => $value) {
                    User::where('fcm_id', $key)->update(['fcm_id' => $value]);
                }
            }
            return compact('downstreamResponse', 'penerima', 'tokenToDelete');
        }

        return null;
    }
}
