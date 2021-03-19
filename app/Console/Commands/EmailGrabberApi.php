<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use App\EmailTr;
use App\Kesatuan;

class EmailGrabberApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:grabapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabbing email and send to server';

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
        $this->info("Running email grabber api");
        try {
            $gClient = new GuzzleClient([
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => env('EMAIL_AUTH'),
                    'Content-Type' => 'multipart/form-data'
                ],
                'http_errors' => false
            ]);

            $oClient = new Client();
            $this->info("Getting sent folder");
            $aFolder = $oClient->getFolder("Sent");
            $lastGrab = EmailTr::orderBy('created_at', 'desc')->first();
            //$this->info('lastGrab', $lastGrab->toArray());
            //$this->info("lastGrab ". $lastGrab ? Carbon::parse($lastGrab->w_email)->addMinute() : Carbon::now()->subDays(1));
            $messages = $aFolder->query()->since($lastGrab ? Carbon::parse($lastGrab->w_email)->addMinute() : Carbon::now()->subDays(1))->get();
            $this->info("Found ".$messages->count()." new TR");

            $params = [];
            //$ids = [];
            foreach ($messages as $index => $msg) {
                $checkEmail = EmailTr::where('id_email', $msg->getMessageId())->first();

                if (!$checkEmail){
                    $localParams = [];
                    $email = [
                        'pengirim' => env('IMAP_USERNAME'),
                        'nomor' => $msg->getSubject(),
                        'w_email' => $msg->getDate(),
                        'id_email' => $msg->getMessageId()
                    ];

                    $localParams[] = [
                        'name' => "pengirim",
                        'contents' => env('IMAP_USERNAME')
                    ];
                    $localParams[] = [
                        'name' => "nomor",
                        'contents' => $msg->getSubject(),
                    ];
                    $localParams[] = [
                        'name' => "w_email",
                        'contents' => $msg->getDate(),
                    ];
                    $localParams[] = [
                        'name' => "id_email",
                        'contents' => $msg->getMessageId(),
                    ];

                    $emailTr = EmailTr::create($email);

                    //$ids[] =$emailTr->id;

                    $this->info('Attachment '.$msg->hasAttachments());

                    if ($msg->hasAttachments()){
                        foreach ($msg->getAttachments() as $indexAttach => $attachment) {
                            $extension = $attachment->getExtension();
                            $path = storage_path('app/email');
                            $filename = str_random(40).'.'.$extension;
                            if ($attachment->save($path, $filename)) {
                                $emailTr->attachment()->create([
                                    'file' => str_replace(storage_path('app/'), '', $path) . '/' . $filename,
                                    'format' => $extension,
                                    'nama' => $attachment->getName()
                                ]);

                                $content = file_get_contents(\storage_path('app/email/') . $filename);
                                $file_info = new \finfo(FILEINFO_MIME_TYPE);
                                $mime_type = $file_info->buffer($content);

                                $localParams[] = [
                                    'name' => "attachments[]",
                                    'contents' => fopen(\storage_path('app/email/') . $filename, 'r'),
                                    'headers' => ['Content-Type' => $mime_type]
                                ];
                            }
                        }
                    }
                    foreach($msg->getTo() as $indexTo => $to){
                        $localParams[] = [
                            'name' => "to[]",
                            'contents' => $to->mail
                        ];

                        /*$kesatuan = Kesatuan::where('email_polri', $to->mail)->first();
                        $to = $emailTr->penerima()->create([
                            'email' => $to->mail,
                            'id_kesatuan' => optional($kesatuan)->id
                        ]);*/
                    }
                    $this->info("[" . $msg->getMessageId() . "-" . $msg->getSubject() . "] Berhasil disimpan");

                    //$this->info('localParams' . json_encode($localParams));
                    //dd($localParams);

                    if (count($localParams) > 0) {

                        $this->info('Sending all email to server ');

                        $responseServer = $gClient->post(env('EMAIL_URL').'/email', [
                            'multipart' => $localParams,
                            'headers' => [
                                'Authorization' => env('EMAIL_AUTH')
                            ]
                        ]);

                        $this->info($responseServer->getBody());

                        /* if ($responseServer->getStatusCode() == 200) {
                            EmailTr::whereIn('id', $ids)->update([
                                'sent_at' => Carbon::now()
                            ]);
                            $this->info('Email sent ');
                        } else {
                            $this->error('Error ', $responseServer->getBody());
                        } */
                    }
                }

                sleep(5);
            }

            $this->info('Finish grabbing....');

            //$this->info('params '.count($params) > 0 ? "true" : "false");
        } catch (ConnectionFailedException $e){
            $this->error("Error ".$e->getMessage(), $e->getTraceAsString());
        } catch (GetMessagesFailedException $e){
            $this->error("Error ".$e->getMessage(), $e->getTraceAsString());
        }
    }
}
