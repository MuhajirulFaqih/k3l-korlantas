<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminOnlineUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:online';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync admin online';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        try {
            $this->info("Sync online admin");

            $url = env('SOCKET_URL')."/apps/".env('SOCKET_APP_ID')."/channels/presence-".env('SOCKET_PREFIX')."-online/users?auth_key=".env("SOCKET_APP_KEY");
            $response = Http::get($url);
            $this->info("Url ". $url);

            if ($response->clientError() || $response->serverError())
                return true;

            $jsonBody = $response->json();


            if (!isset($jsonBody['users']))
                return true;

            $ids = array_map(function ($items){return $items['id']; }, $jsonBody['users']);

            User::whereIn('id', $ids)->update(['online' => true]);
            User::whereNotIn('id', $ids)->update(['online' => false]);

            return true;
        } catch (\Exception $e){
            Log::info("Eror", $e->getTrace());
        }
    }
}
