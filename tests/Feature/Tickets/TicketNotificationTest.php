<?php

use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Models\Ticket;
use App\Mail\Tickets\TicketAssignedMailable;
use App\Mail\Tickets\TicketClosedMailable;
use App\Mail\Tickets\TicketCreatedMailable;
use App\Mail\Tickets\TicketMessageAddedMailable;
use App\Mail\Tickets\TicketReopenedMailable;
use App\Mail\Tickets\TicketResolvedMailable;
use App\Mail\Tickets\TicketWaitingUserMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed();

    Mail::fake();

    $this->ti = Department::where('slug', 'ti')->firstOrFail();
    $this->servicio = Service::where('department_id', $this->ti->id)->firstOrFail();

    $this->requester = User::factory()->create(['department_id' => $this->ti->id]);

    $this->agente = User::factory()->create(['department_id' => $this->ti->id]);
    $this->agente->assignRole('agente');
});

test('crear un ticket notifica a los agentes/encargados del área (incluye a los del seeder)', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción de prueba.',
    ]);

    Mail::assertQueued(TicketCreatedMailable::class, function ($mail) {
        return $mail->hasTo($this->agente->email);
    });
});

test('asignar un ticket notifica al agente asignado', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción de prueba.',
    ]);
    $ticket = Ticket::firstOrFail();

    $this->actingAs($this->agente)->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id]);

    Mail::assertQueued(TicketAssignedMailable::class, fn ($mail) => $mail->hasTo($this->agente->email));
});

test('un comentario del agente notifica al solicitante, y uno del solicitante notifica al agente asignado', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción de prueba.',
    ]);
    $ticket = Ticket::firstOrFail();

    $this->actingAs($this->agente)->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id]);
    $this->actingAs($this->agente)->post(route('tickets.status', $ticket), ['estado' => 'in_progress']);

    $this->actingAs($this->agente)->post(route('tickets.messages', $ticket), ['body' => 'Estamos revisando.']);
    Mail::assertQueued(TicketMessageAddedMailable::class, fn ($mail) => $mail->hasTo($this->requester->email));

    $this->actingAs($this->requester)->post(route('tickets.messages', $ticket), ['body' => 'Gracias, quedo atento.']);
    Mail::assertQueued(TicketMessageAddedMailable::class, fn ($mail) => $mail->hasTo($this->agente->email));
});

test('pedir información al usuario le notifica por correo', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción de prueba.',
    ]);
    $ticket = Ticket::firstOrFail();

    $this->actingAs($this->agente)->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id]);
    $this->actingAs($this->agente)->post(route('tickets.status', $ticket), ['estado' => 'in_progress']);
    $this->actingAs($this->agente)->post(route('tickets.status', $ticket), ['estado' => 'waiting_user']);

    Mail::assertQueued(TicketWaitingUserMailable::class, fn ($mail) => $mail->hasTo($this->requester->email));
});

test('resolver, reabrir y cerrar notifican por correo a quien corresponde', function () {
    $this->actingAs($this->requester)->post(route('tickets.store'), [
        'service_id' => $this->servicio->id,
        'title' => 'Ticket de prueba',
        'description' => 'Descripción de prueba.',
    ]);
    $ticket = Ticket::firstOrFail();

    $this->actingAs($this->agente)->post(route('tickets.assign', $ticket), ['agente_id' => $this->agente->id]);
    $this->actingAs($this->agente)->post(route('tickets.status', $ticket), ['estado' => 'in_progress']);
    $this->actingAs($this->agente)->post(route('tickets.resolve', $ticket), ['resolucion' => 'Listo.']);

    Mail::assertQueued(TicketResolvedMailable::class, fn ($mail) => $mail->hasTo($this->requester->email));

    $this->actingAs($this->requester)->post(route('tickets.reopen', $ticket), ['motivo' => 'Sigue fallando.']);

    Mail::assertQueued(TicketReopenedMailable::class, fn ($mail) => $mail->hasTo($this->agente->email));

    $this->actingAs($this->agente)->post(route('tickets.resolve', $ticket), ['resolucion' => 'Ahora sí.']);
    $this->actingAs($this->requester)->post(route('tickets.close', $ticket));

    Mail::assertQueued(TicketClosedMailable::class, fn ($mail) => $mail->hasTo($this->requester->email));
});
