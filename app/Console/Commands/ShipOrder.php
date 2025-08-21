<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Mirakl\Mirakl;
use Illuminate\Console\Command;

class ShipOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ship-order';

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

    public function handle(): int
    {
        $orders = Order::OpenTracking()->get();

        $counter = 0;
        $error = 0;

        foreach ($orders as $o) {
            try {
                $this->client->orders()->postTrackingInformation($o);

                $this->client->orders()->markAsShipped($o);

                $o->tracking_submitted_at = now();
                $o->save();
                $counter++;
            }catch (\Exception $e){
                \Log::error($e->getMessage());
                $error++;
            }


        }

        $this->info($counter . ' Bestellungen erfolgreich versendet. ' . $error . ' Fehler.');
        return Command::SUCCESS;
    }

}
