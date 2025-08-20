<?php

namespace App\Console\Commands;

use App\Services\Mirakl\Mirakl;
use Illuminate\Console\Command;
use App\Models\Order;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ImportOrders extends Command
{
    protected $signature = 'app:import-orders';
    protected $description = 'Importiert Bestellungen vom Fressnapf-Marktplatz via Guzzle';

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
//        dd();
//        try {
//            /** @var ResponseInterface $response */
//            $response = $this->client->request('GET', $this->baseUri . '/api/orders', [
//                'headers' => [
//                    'Authorization' => $this->token,
//                    'Accept'        => 'application/json',
//                ],
//                'query' => [
//                    'page'               => 1,
//                    'per_page'           => 100,
//                    'order_state_codes'  => 'WAITING_ACCEPTANCE',
//                ],
//                'timeout' => 10,
//            ]);
//        } catch (RequestException $e) {
//            $this->error('API-Fehler: ' . $e->getMessage());
//            return Command::FAILURE;
//        }

        $_orders = $this->client->orders()->all();

        $orders = $_orders['orders'] ?? [];

        foreach ($orders as $o) {
            // 1) Order anlegen/update
            $order = Order::updateOrCreate(
                ['order_id' => $o['order_id']],
                [
                    'commercial_id'       => $o['commercial_id'],
                    'customer_id'         => $o['customer']['customer_id'],
                    'firstname'           => $o['customer']['firstname'],
                    'lastname'            => $o['customer']['lastname'],
                    'email'               => $o['customer']['email']?? null,
                    'channel_code'        => $o['channel']['code'],
                    'channel_label'       => $o['channel']['label'],
                    'order_state'         => $o['order_state'],
                    'total_price'         => $o['total_price'],
                    'total_commission'    => $o['total_commission'],
                    'currency_iso_code'   => $o['currency_iso_code'],
                    'shipping_type_label' => $o['shipping_type_label'],
                ]
            );

//            $this->client->orders()->accept($order);

//            $tmp = $this->client->orders()->get($order);
//
//            if (!isset($tmp['orders']))
//            {
//                throw new \Exception('could not refetch customer data');
//            }

//            // 2) Billing-Adresse speichern
//            if (! empty($o['customer']['billing_address'])) {
//                $b = $o['customer']['billing_address'];
//
//                $order->billingAddress()->updateOrCreate(
//                    ['order_id' => $order->id],
//                    [
//                        'civility'         => $b['civility']         ?? null,
//                        'company'          => $b['company']          ?? null,
//                        'street_1'         => $b['street_1']         ?? null,
//                        'street_2'         => $b['street_2']         ?? null,
//                        'city'             => $b['city']             ?? null,
//                        'state'            => $b['state']            ?? null,
//                        'zip_code'         => $b['zip_code']         ?? null,
//                        'country_iso_code' => $b['country_iso_code'] ?? null,
//                    ]
//                );
//            }

//            // 3) Shipping-Adresse speichern
//            if (! empty($tmp['orders'][0]['customer']['shipping_address'])) {
//                $s = $tmp['orders'][0]['customer']['shipping_address'];
//
//
//                $order->shippingAddress()->updateOrCreate(
//                    ['order_id' => $order->id],
//                    [
//                        'civility'                 => $s['civility']                ?? null,
//                        'company'                  => $s['company']                 ?? null,
//                        'additional_info'          => $s['additional_info']         ?? null,
//                        'internal_additional_info' => $s['internal_additional_info']?? null,
//                        'street_1'                 => $s['street_1']                ?? null,
//                        'street_2'                 => $s['street_2']                ?? null,
//                        'city'                     => $s['city']                    ?? null,
//                        'state'                    => $s['state']                   ?? null,
//                        'zip_code'                 => $s['zip_code']                ?? null,
//                        'country_iso_code'         => $s['country_iso_code']        ?? null,
//                    ]
//                );
//            }
//
//            // 4) OrderLines wie gehabt
            foreach ($o['order_lines'] as $l) {
                $order->lines()->updateOrCreate(
                    ['order_line_id' => $l['order_line_id']],
                    [
                        // Basis
                        'offer_id'                       => $l['offer_id'],
                        'offer_sku'                      => $l['offer_sku'],
                        'offer_state_code'               => $l['offer_state_code']           ?? null,
                        'product_shop_sku'               => $l['product_shop_sku']           ?? null,
                        'product_sku'                    => $l['product_sku'],
                        'product_title'                  => $l['product_title'],
                        'order_line_index'               => $l['order_line_index']           ?? null,
                        'order_line_state'               => $l['order_line_state']           ?? null,
                        'order_line_state_reason_code'   => $l['order_line_state_reason_code'] ?? null,
                        'order_line_state_reason_label'  => $l['order_line_state_reason_label']?? null,

                        // Preise & Mengen
                        'quantity'                       => $l['quantity'],
                        'origin_unit_price'              => $l['origin_unit_price']          ?? null,
                        'price_unit'                     => $l['price_unit']                 ?? null,
                        'price'                          => $l['price']                      ?? null,
                        'total_price'                    => $l['total_price'],
                        'commission_fee'                 => $l['commission_fee']             ?? null,
                        'total_commission'               => $l['total_commission']           ?? null,
                        'shipping_price'                 => $l['shipping_price']             ?? null,

                        // Flags
                        'can_open_incident'              => $l['can_open_incident']          ?? false,
                        'can_refund'                     => $l['can_refund']                 ?? false,

                        // Timestamps
//                        'created_date'                   => $l['created_date']               ?? null,
//                        'debited_date'                   => $l['debited_date']               ?? null,
//                        'received_date'                  => $l['received_date']              ?? null,
//                        'shipped_date'                   => $l['shipped_date']               ?? null,
//                        'last_updated_date'              => $l['last_updated_date']          ?? null,

                        // Labels & Hinweise
                        'category_code'                  => $l['category_code']              ?? null,
                        'category_label'                 => $l['category_label']             ?? null,
                        'tax_legal_notice'               => $l['tax_legal_notice']           ?? null,
                        'description'                    => $l['description']                ?? null,

                        // JSON-Strukturen
                        'cancelations'                   => json_encode($l['cancelations']                  ?? []),
                        'commission_taxes'               => json_encode($l['commission_taxes']              ?? []),
                        'eco_contributions'              => json_encode($l['eco_contributions']             ?? []),
                        'fees'                           => json_encode($l['fees']                         ?? []),
                        'funding'                        => json_encode($l['funding']                      ?? []),
                        'order_line_additional_fields'   => json_encode($l['order_line_additional_fields'] ?? []),
                        'product_medias'                 => json_encode($l['product_medias']                ?? []),
                        'promotions'                     => json_encode($l['promotions']                    ?? []),
                        'purchase_information'           => json_encode($l['purchase_information']          ?? []),
                        'refunds'                        => json_encode($l['refunds']                      ?? []),
                        'shipping_from'                  => json_encode($l['shipping_from']                ?? []),
                        'shipping_taxes'                 => json_encode($l['shipping_taxes']               ?? []),
                        'taxes'                          => json_encode($l['taxes']                        ?? []),
                    ]
                );
            }
        }

        $this->info('✅ Bestellungen erfolgreich importiert via Guzzle.');
        return Command::SUCCESS;
    }
}
