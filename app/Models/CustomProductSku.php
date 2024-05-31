<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomProductSku extends Model
{
    use HasFactory;

    protected $fillable = [
        'orignal_sku',
        'custom_sku'
    ];
}
