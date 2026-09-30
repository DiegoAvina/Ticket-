<?php

namespace App\Domain\Tickets\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCounter extends Model
{
    protected $fillable = [
        'year',
        'last_number',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'last_number' => 'integer',
        ];
    }
}
