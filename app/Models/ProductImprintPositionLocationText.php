<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImprintPositionLocationText extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_imprint_position_id',
        'language',
        'images',
        'name',
        'description'
    ];
}
