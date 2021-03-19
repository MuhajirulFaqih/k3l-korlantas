<?php

namespace App\Console\Commands;

use App\Personil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class PersonilToRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personil:redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info("Memprosess dump to redis");
        foreach(Personil::get() as $personil){
            Redis::set('personil:'.$personil->nrp, $personil->jabatan->jabatan.' '.$personil->nama);
        }
        $this->info("Selesai...");
    }
}
