<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
    protected $fillable = [
        'line_user_id',
        'display_name',
    ];

    public function watches()
    {
        return $this->hasMany(Watch::class);
    }
}
