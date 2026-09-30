<?php

namespace App\Domain\Knowledge\Models;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'service_id',
        'department_id',
        'published',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'views' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
