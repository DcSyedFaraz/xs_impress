<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'supplier_sequence',
        'type',
        'parent_id',
        'sku',
        'supplier_sku',
        'a_number',
        'non_language_depended_product_details',
        'battery_information',
        'ean',
        'video_url',
        'forbidden_regions',
        'imprint_references',
        'product_costs',
        'sample_price_country_based',
        'product_price_region_based',
        'unstructured_information',
    ];

    public function deletedProducts(){
        return $this->hasMany(DeletedProduct::class,'sku','sku');
    }
}
