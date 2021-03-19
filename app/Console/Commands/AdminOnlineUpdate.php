<?php

namespace App\Console\Commands;

use App\Admin;
use App\User;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleClient;

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
        $this->info("Sync online admin");
        $client = new GuzzleClient(['timeout' => 5.0]);
        $url = env('SOCKET_URL')."/apps/".env('SOCKET_APP_ID')."/channels/presence-".env('SOCKET_PREFIX')."-online/users?auth_key=".env("SOCKET_APP_KEY");
        $this->info("Url ". $url);

        $response = $client->request('GET', $url);

        $body = $response->getBody();

        $jsonBody = json_decode($body);
        if (!isset($jsonBody->users))
            return;

        $ids = array_map(function ($items){return $items->id; }, $jsonBody->users);

        $idsAdmin = User::whereIn('id', $ids)->where('jenis_pemilik', 'admin')->get()->pluck('id_pemilik')->all();

        //dd($idsAdmin);

        //$this->info("Updating ids", $ids);

        Admin::whereIn('id', $idsAdmin)->update(['status' => true]); // Update status online
        Admin::whereNotIn('id', $idsAdmin)->update(['status' => false]); // Update status offline

        return true;
    }
}
