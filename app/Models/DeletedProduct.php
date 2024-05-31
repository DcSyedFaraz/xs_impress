<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'deleted_by',
        'deleted_at',
        'recovered_at',
    ];
}
