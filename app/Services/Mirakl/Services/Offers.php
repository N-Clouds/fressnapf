<?php

namespace App\Services\Mirakl\Services;

use App\Services\Mirakl\Utils\ApiClient;
use Illuminate\Support\Collection;

class Offers extends ApiClient
{
    public function import()
    {
        return $this->_get('/api/offers');
    }

    public function updateStock(Collection $offers)
    {
        $payload = $offers->map(fn($offer) => [
            'price' => $offer->price,
            'quantity' => $offer->quantity,
            'shop_sku' => $offer->sku,
            'update_delete' => 'update'
        ])
            ->values()
            ->toArray();

        return $this->_post('/api/offers',
            ['offers' => $payload],
            []);
    }
}
