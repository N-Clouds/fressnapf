<?php

namespace App\Services\Mirakl;

use App\Services\Mirakl\Services\Offers;
use App\Services\Mirakl\Services\Orders;

class Mirakl
{
    public function orders()
    {
        return new Orders();
    }

    public function offers()
    {
        return new Offers();
    }
}
