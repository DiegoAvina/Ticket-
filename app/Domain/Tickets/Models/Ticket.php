<?php

namespace App\Domain\Tickets\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\SLA\Models\SlaPolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'requester_id',
        'requester_department_id',
        'assigned_department_id',
        'service_id',
        'category_id',
        'asset_id',
        'priority_id',
        'status_id',
        'assigned_user_id',
        'title',
        'description',
        'source',
        'external_ref',
        'sla_policy_id',
        'sla_response_due_at',
        'sla_resolution_due_at',
        'sla_resolution_warned_at',
        'sla_resolution_breached_at',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_response_due_at' => 'datetime',
            'sla_resolution_due_at' => 'datetime',
            'sla_resolution_warned_at' => 'datetime',
            'sla_resolution_breached_at' => 'datetime',
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Filtra los tickets visibles para el usuario a nivel de consulta (no solo
     * de presentación): administrador ve todo, el solicitante ve los suyos, y
     * agente/encargado ven los de su propio departamento asignado. Esta es la
     * defensa principal contra IDOR en cualquier listado.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('administrador')) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('requester_id', $user->id);

            if ($user->department_id) {
                $q->orWhere('assigned_department_id', $user->department_id);
            }
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function requesterDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'requester_department_id');
    }

    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'assigned_department_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(TicketHistory::class)->orderByDesc('created_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(TicketWatcher::class);
    }
}
