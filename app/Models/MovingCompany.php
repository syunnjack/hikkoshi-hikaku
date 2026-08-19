<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovingCompany extends Model
{
    protected $fillable = [
        'name', 'kana_column', 'certificate_url', 'source_url', 'confirmed_on',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_on' => 'date',
        ];
    }

    /** 50音の並び順。データは「あ行」などの文字列で持っている。 */
    public const COLUMN_ORDER = ['あ行', 'か行', 'さ行', 'た行', 'な行', 'は行', 'ま行', 'や行', 'ら行', 'わ行'];
}
