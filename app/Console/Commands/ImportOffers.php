<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Services\Mirakl\Mirakl;
use Illuminate\Console\Command;

class ImportOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-offers';

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
        $_offers = $this->client->offers()->import();

        foreach ($_offers['offers'] as $offer) {

            Offer::create([
                'sku' => $offer['shop_sku'],
                'price' => $offer['price'],
                'quantity' => $offer['quantity']
            ]);
        }
    }
}
