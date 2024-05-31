<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImprintPositionOptionPriceCountryBased extends Model
{
    use HasFactory;

    protected $table = 'product_imprint_position_option_price_country_based';

    protected $fillable = [
        'product_imprint_position_option_id',
        'country_currency',
        'type',
        'price',
        'quantity',
        'on_request',
        'valuta',
        'quantity_increments',
        'vat_percentage',
        'minimum_order_quantity',
        'vat_setting_id'
    ];
}
