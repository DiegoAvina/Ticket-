<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Departments\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'department_id',
        'name',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
