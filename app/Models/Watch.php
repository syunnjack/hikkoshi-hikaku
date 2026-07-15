<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watch extends Model
{
    protected $fillable = [
        'line_user_id',
        'company_name',
        'last_checked_report_id',
    ];

    public function lineUser()
    {
        return $this->belongsTo(LineUser::class);
    }
}
