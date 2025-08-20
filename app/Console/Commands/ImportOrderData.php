<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Mirakl\Mirakl;
use Illuminate\Console\Command;

class ImportOrderData extends Command
{
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-order-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::whereDoesntHave('billingAddress')->get();

        foreach ($orders as $order) {
            $tmp = $this->client->orders()->get($order);
            $_order = null;

            if(! empty($tmp['orders'][0])){
                $_order = $tmp['orders'][0];
            }



//            dd($_order['customer']['shipping_address']);
            if (! empty($_order['customer']['shipping_address'])) {
                $s = $_order['customer']['shipping_address'];


                $order->shippingAddress()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'civility'                 => $s['civility']                ?? null,
                        'firstname'                => $s['firstname']               ?? null,
                        'lastname'                 => $s['lastname']                ?? null,
                        'company'                  => $s['company']                 ?? null,
                        'additional_info'          => $s['additional_info']         ?? null,
                        'internal_additional_info' => $s['internal_additional_info']?? null,
                        'street_1'                 => $s['street_1']                ?? null,
                        'street_2'                 => $s['street_2']                ?? null,
                        'city'                     => $s['city']                    ?? null,
                        'state'                    => $s['state']                   ?? null,
                        'zip_code'                 => $s['zip_code']                ?? null,
                        'country_iso_code'         => $s['country_iso_code']        ?? null,
                        'phone'                   => $s['phone'] ?? null,
                    ]
                );
            }

            if (! empty($_order['customer']['billing_address'])) {
                $b = $_order['customer']['billing_address'];

                $order->billingAddress()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'civility'         => $b['civility']         ?? null,
                        'firstname'        => $b['firstname']        ?? null,
                        'lastname'         => $b['lastname']         ?? null,
                        'company'          => $b['company']          ?? null,
                        'street_1'         => $b['street_1']         ?? null,
                        'street_2'         => $b['street_2']         ?? null,
                        'city'             => $b['city']             ?? null,
                        'state'            => $b['state']            ?? null,
                        'zip_code'         => $b['zip_code']         ?? null,
                        'country_iso_code' => $b['country_iso_code'] ?? null,
                        'phone'           => $b['phone'] ?? null,
                    ]
                );
            }
        }
    }
}
