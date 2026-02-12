<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'quantity', 
        'shipping_type',
        'shipping_cost',
        'condition',
        'category'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'quantity' => 'integer',
    ];
}