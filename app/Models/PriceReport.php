<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceReport extends Model
{
    protected $fillable = [
        'company_name',
        'move_type',
        'distance_range',
        'total_price',
        'comment',
        'nickname',
        'ip_hash',
    ];
}
