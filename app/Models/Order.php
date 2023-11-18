<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type', // buy/rent
        'price',
        'valid_till',
    ];

    protected $casts = [
        'valid_till' => 'datetime'
    ];
}
