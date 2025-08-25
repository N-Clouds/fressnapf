<?php

namespace App\Services\Mirakl\Services;

use App\Models\Order;
use App\Services\Mirakl\Utils\ApiClient;
use Nette\NotImplementedException;

class Orders extends ApiClient
{
    public function all()
    {
        return $this->_get(
            '/api/orders',
            [],
            [
                'page'              => 1,
                'per_page'          => 100,
            ]
        );
    }

    public function open()
    {
        return $this->_get(
            '/api/orders',
            [],
            [
                'page'              => 1,
                'per_page'          => 100,
                'order_state_codes' => 'WAITING_ACCEPTANCE',
            ]
        );
    }

    public function accept(Order $order)
    {
        $payloadLines = $order->lines
            ->map(fn($line) => [
                'accepted' => true,
                'id'       => $line->order_line_id,
            ])
            ->values()
            ->toArray();

        // 2) Baue den Endpoint-Pfad
        $endpoint = "/api/orders/{$order->order_id}/accept";

        // 3) Führe das PUT-Request aus
        return $this->_post(
            $endpoint,
            ['order_lines' => $payloadLines],  // JSON-Body
            []                                 // hier keine weiteren Query-Params
        );

    }

    public function get(Order $order)
    {
        return $this->_get(
            '/api/orders',
            [],
            [
                'page'              => 1,
                'per_page'          => 100,
                'order_ids' => $order->order_id,
            ]
        );
    }

    public function postTrackingInformation(Order $order)
    {
        $endpoint = "/api/orders/{$order->order_id}/tracking";

        return $this->_put(
            $endpoint,
            [
                'carrier_code'          => 'GLS',
                'carrier_standard_code' => null,
                'carrier_name'          => '',
                'carrier_url'           => '',
                'tracking_number'       => $order->tracking_id
            ],
            []
        );
    }

    public function markAsShipped(Order $order)
    {
        $endpoint = "/api/orders/{$order->order_id}/ship";
        return $this->_put($endpoint);
    }
//
//    public function create($data)
//    {
//        return $this->_post('/v0.5.0/orders', $data);
//    }
//
//    public function status($order)
//    {
//        return $this->_get('/v0.5.0/orders/' . $order . '/status');
//    }
//
//    public function returns($order)
//    {
//        throw new NotImplementedException();
//    }
}
