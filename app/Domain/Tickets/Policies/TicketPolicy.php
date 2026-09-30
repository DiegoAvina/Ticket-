<?php

namespace App\Domain\Tickets\Policies;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Regla espejo de Ticket::scopeVisibleTo: quién puede ver UN ticket ya
     * cargado (por ejemplo al pedir /tickets/{id} directo por URL).
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('administrador')
            || $ticket->requester_id === $user->id
            || ($user->department_id !== null && $ticket->assigned_department_id === $user->department_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Gestionar: asignar, cambiar estado, escalar. Solo el área responsable
     * (agente/encargado) o un administrador.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('administrador')
            || ($user->hasAnyRole(['agente', 'encargado']) && $user->department_id === $ticket->assigned_department_id);
    }

    /**
     * Cerrar: lo confirma quien reportó el problema (o un administrador).
     */
    public function close(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('administrador') || $ticket->requester_id === $user->id;
    }

    /**
     * Reabrir: mismo criterio que cerrar, y solo tiene sentido si sigue
     * "resuelto" (la transición misma la valida ChangeTicketStatusAction).
     */
    public function reopen(User $user, Ticket $ticket): bool
    {
        return ($user->hasRole('administrador') || $ticket->requester_id === $user->id)
            && $ticket->status->code === TicketStatusCode::RESOLVED;
    }

    /**
     * Eliminar (soft delete) un ticket por completo: solo un administrador.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('administrador');
    }
}
