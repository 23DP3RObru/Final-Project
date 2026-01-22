<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description'];
    
    // Optional: Add casting if needed
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}