<?php

namespace App\Domain\Tickets\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $fillable = [
        'code',
        'name',
        'order',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_closed' => 'boolean',
        ];
    }
}
