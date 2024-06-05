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

    public function deletedProducts()
    {
        return $this->hasMany(DeletedProduct::class, 'sku', 'sku');
    }
    public function static_content()
    {
        return $this->hasMany(ProductStaticContent::class, 'sku', 'sku');
    }
    public function details()
    {
        return $this->hasOne(ProductDetail::class);
    }
    public function scopeParent($query, $id)
    {
        return $query->where('parent_id', $id);
    }
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function images()
    {
        return $this->hasManyThrough(ProductImage::class, ProductDetail::class);
    }

    public function variants()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }
}
