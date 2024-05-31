<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMediaGalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_detail_id',
        'url',
        'description',
        'file_name',
    ];
}
