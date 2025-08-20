<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'commercial_id',
        'customer_id',
        'firstname',
        'lastname',
        'email',
        'channel_code',
        'channel_label',
        'order_state',
        'total_price',
        'total_commission',
        'currency_iso_code',
        'shipping_type_label',
    ];

    protected function casts(): array
    {
        return [
            'firstname' => 'encrypted',
            'lastname' => 'encrypted',
        ];
    }

    /**
     * Order lines (1:n)
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    /**
     * Billing address (1:1)
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(BillingAddress::class);
    }

    /**
     * Shipping address (1:1)
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(ShippingAddress::class);
    }
}
