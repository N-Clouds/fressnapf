<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'civility',
        'firstname',
        'lastname',
        'company',
        'additional_info',
        'internal_additional_info',
        'street_1',
        'street_2',
        'city',
        'state',
        'zip_code',
        'country_iso_code',
    ];

    protected function casts(): array
    {
        return [
            'firstname' => 'encrypted',
            'lastname' => 'encrypted',
            'company' => 'encrypted',
            'street_1' => 'encrypted',
            'street_2' => 'encrypted',
            'city' => 'encrypted',
            'zip_code' => 'encrypted',
        ];
    }

    /**
     * Relation zur Order (1:1)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
