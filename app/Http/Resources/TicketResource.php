<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'source' => $this->source,
            'status' => ['code' => $this->status->code, 'name' => $this->status->name],
            'priority' => ['id' => $this->priority->id, 'name' => $this->priority->name],
            'requester' => ['id' => $this->requester->id, 'name' => $this->requester->name, 'email' => $this->requester->email],
            'assigned_department' => ['id' => $this->assignedDepartment->id, 'name' => $this->assignedDepartment->name],
            'assigned_user' => $this->whenLoaded('assignedUser', fn () => $this->assignedUser ? [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ] : null),
            'sla_response_due_at' => $this->sla_response_due_at,
            'sla_resolution_due_at' => $this->sla_resolution_due_at,
            'first_response_at' => $this->first_response_at,
            'resolved_at' => $this->resolved_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
        ];
    }
}
