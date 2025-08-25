<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Services\Mirakl\Mirakl;
use Illuminate\Console\Command;

class UpdateStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    private Mirakl $client;
    private string $baseUri;
    private string $token;

    public function __construct(Mirakl $client)
    {
        parent::__construct();

        $this->client  = $client;
        $this->baseUri = config('fressnapf.host');
        $this->token   = config('fressnapf.token');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offers = Offer::all();

        $this->client->offers()->updateStock($offers);
    }
}
