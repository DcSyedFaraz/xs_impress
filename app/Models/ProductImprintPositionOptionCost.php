<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImprintPositionOptionCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_imprint_position_option_id',
        'sku',
        'supplier_sku',
        'texts',
        'price_region_based',
        'price_country_based',
        'is_active_region_based',
        'is_active_country_based',
        'calculation_type',
        'calculation_amount',
        'requirement',
        'unstructured_information'
    ];
}
