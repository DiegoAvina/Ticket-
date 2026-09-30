<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Departments\Models\Department;
use App\Domain\Forms\Models\Form;
use App\Domain\SLA\Models\SlaPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'department_id',
        'category_id',
        'name',
        'default_priority_id',
        'sla_policy_id',
        'form_id',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function defaultPriority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'default_priority_id');
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
