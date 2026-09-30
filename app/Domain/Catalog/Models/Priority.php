<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $fillable = [
        'name',
        'level',
        'first_response_minutes',
        'resolution_minutes',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'first_response_minutes' => 'integer',
            'resolution_minutes' => 'integer',
        ];
    }
}
