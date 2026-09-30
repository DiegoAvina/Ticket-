<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSgiDasavenaTicketRequest extends FormRequest
{
    /**
     * Solo el token de integración de sgiDasavena (habilidad "tickets:create")
     * puede crear tickets por esta vía — no es un endpoint para usuarios finales.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->tokenCan('tickets:create');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requester_email' => ['required', 'email', 'max:255'],
            'requester_name' => ['required', 'string', 'max:255'],
            'requester_area' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', 'in:ticket,idea'],
            'asunto' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'external_ref' => ['required', 'string', 'max:64'],
        ];
    }
}
