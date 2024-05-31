<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImprintPositionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_imprint_position_id',
        'child_imprints',
        'print_color_as_text',
        'dimension',
        'imprint_texts',
        'sku',
        'supplier_sku',
        'dimensions_height',
        'dimensions_diameter',
        'dimensions_width',
        'dimensions_depth',
        'imprint_type',
        'unstructured_information',
        'print_color',
        'is_active_region_based',
        'is_active_country_based',
        'important_information',
        'price_region_based'
    ];
}
