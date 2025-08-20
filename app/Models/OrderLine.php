<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_line_id',
        'offer_id',
        'offer_sku',
        'offer_state_code',
        'product_shop_sku',
        'product_sku',
        'product_title',
        'order_line_index',
        'order_line_state',
        'order_line_state_reason_code',
        'order_line_state_reason_label',
        'quantity',
        'origin_unit_price',
        'price_unit',
        'price',
        'total_price',
        'commission_fee',
        'total_commission',
        'shipping_price',
        'can_open_incident',
        'can_refund',
        'created_date',
        'debited_date',
        'received_date',
        'shipped_date',
        'last_updated_date',
        'category_code',
        'category_label',
        'tax_legal_notice',
        'description',
        'cancelations',
        'commission_taxes',
        'eco_contributions',
        'fees',
        'funding',
        'order_line_additional_fields',
        'product_medias',
        'promotions',
        'purchase_information',
        'refunds',
        'shipping_from',
        'shipping_taxes',
        'taxes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
