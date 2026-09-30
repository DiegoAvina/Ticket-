<?php

namespace App\Domain\SLA\Models;

use App\Domain\Catalog\Models\Priority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaPolicy extends Model
{
    protected $fillable = [
        'name',
        'priority_id',
        'first_response_minutes',
        'resolution_minutes',
        'business_hours_aware',
    ];

    protected function casts(): array
    {
        return [
            'first_response_minutes' => 'integer',
            'resolution_minutes' => 'integer',
            'business_hours_aware' => 'boolean',
        ];
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }
}
