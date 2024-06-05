<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'language',
        'name',
        'description',
        'short_description',
        'meta_name',
        'meta_description',
        'meta_keywords',
        'is_active',
        'pimv1_information',
        'unstructured_information',
        'web_shop_information',
        'important_information'
    ];

    public function config()
    {
        return $this->hasMany(ProductConfiguration::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_detail_id');
    }
    public function scopeLanguage($query, $language)
    {
        return $query->where('language', $language);
    }
}
