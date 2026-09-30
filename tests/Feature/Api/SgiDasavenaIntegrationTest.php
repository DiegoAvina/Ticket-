<?php

use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Models\Ticket;
use App\Mail\Tickets\TicketCreatedMailable;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed();

    Mail::fake();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->servicioChatbot = Service::where('department_id', $this->ti->id)->where('name', 'Chatbot SGI')->firstOrFail();

    $this->agenteTi = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agenteTi->assignRole('agente');

    $this->cuentaIntegracion = User::factory()->create(['active' => false, 'password' => Hash::make('no-usable')]);
});

function payloadChatbot(array $overrides = []): array
{
    return array_merge([
        'requester_email' => 'empleado@dasavena.com',
        'requester_name' => 'Empleado de Prueba',
        'requester_area' => 'Ventas',
        'tipo' => 'ticket',
        'asunto' => 'La impresora no imprime',
        'descripcion' => 'Se atoró el papel varias veces.',
        'external_ref' => '123',
    ], $overrides);
}

test('sin token no se puede crear un ticket de integración', function () {
    $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot())->assertUnauthorized();
});

test('un token sin la habilidad tickets:create no puede crear tickets', function () {
    Sanctum::actingAs($this->cuentaIntegracion, []);

    $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot())->assertForbidden();

    expect(Ticket::count())->toBe(0);
});

test('crea un ticket real en TI y autoprovisiona al solicitante', function () {
    Sanctum::actingAs($this->cuentaIntegracion, ['tickets:create']);

    $respuesta = $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot());

    $respuesta->assertCreated()->assertJsonStructure(['ticket_id', 'folio']);

    $ticket = Ticket::findOrFail($respuesta->json('ticket_id'));

    expect($ticket->source)->toBe('sgidasavena')
        ->and($ticket->external_ref)->toBe('123')
        ->and($ticket->service_id)->toBe($this->servicioChatbot->id)
        ->and($ticket->assigned_department_id)->toBe($this->ti->id)
        ->and($ticket->priority_id)->toBe($this->servicioChatbot->default_priority_id)
        ->and($ticket->description)->toContain('Ventas');

    $solicitante = User::where('email', 'empleado@dasavena.com')->firstOrFail();

    expect($solicitante->hasRole('usuario'))->toBeTrue()
        ->and($solicitante->department_id)->toBeNull();

    Mail::assertQueued(TicketCreatedMailable::class, fn ($mail) => $mail->hasTo($this->agenteTi->email));
});

test('una idea se crea con prioridad baja', function () {
    Sanctum::actingAs($this->cuentaIntegracion, ['tickets:create']);

    $respuesta = $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot([
        'tipo' => 'idea',
        'external_ref' => '456',
    ]));

    $ticket = Ticket::findOrFail($respuesta->json('ticket_id'));
    $prioridadBaja = Priority::where('name', 'Baja')->firstOrFail();

    expect($ticket->priority_id)->toBe($prioridadBaja->id);
});

test('reenviar el mismo external_ref no duplica el ticket', function () {
    Sanctum::actingAs($this->cuentaIntegracion, ['tickets:create']);

    $primera = $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot())->assertCreated();

    $segunda = $this->postJson('/api/v1/integrations/sgidasavena/tickets', payloadChatbot())->assertOk();

    expect($segunda->json('ticket_id'))->toBe($primera->json('ticket_id'))
        ->and(Ticket::where('source', 'sgidasavena')->count())->toBe(1);
});
