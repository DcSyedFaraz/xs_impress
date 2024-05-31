<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStaticContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'language',
        'category',
        'description'
    ];
}
