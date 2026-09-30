<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Knowledge\Models\Article;
use App\Domain\Tickets\Actions\AddTicketMessageAction;
use App\Domain\Tickets\Actions\AssignTicketAction;
use App\Domain\Tickets\Actions\ChangeTicketStatusAction;
use App\Domain\Tickets\Actions\CloseTicketAction;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Domain\Tickets\Actions\EscalateTicketAction;
use App\Domain\Tickets\Actions\ReopenTicketAction;
use App\Domain\Tickets\Actions\ResolveTicketAction;
use App\Domain\Tickets\Actions\UpdateTicketDetailsAction;
use App\Domain\Tickets\Enums\TicketMessageType;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketAttachment;
use App\Domain\Tickets\Models\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->visibleTo($request->user())
            ->with(['requester', 'assignedDepartment', 'priority', 'status'])
            ->when($request->filled('estado'), fn ($q) => $q->whereHas('status', fn ($s) => $s->where('code', $request->string('estado'))))
            ->when($request->filled('q'), function ($q) use ($request) {
                $termino = '%'.str_replace(['%', '_'], ['\%', '\_'], $request->string('q')->trim()).'%';

                $q->where(fn ($w) => $w->where('ticket_number', 'like', $termino)
                    ->orWhere('title', 'like', $termino)
                    ->orWhereHas('requester', fn ($r) => $r->where('name', 'like', $termino)));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'estados' => TicketStatus::orderBy('order')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $servicios = Service::query()
            ->where('active', true)
            ->with('department')
            ->orderBy('department_id')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Service $s) => $s->department->name);

        // Sección 32 del spec: sugerir artículos de la base de conocimiento
        // antes de que el usuario termine de crear el ticket.
        $articulosPorServicio = Article::published()
            ->whereNotNull('service_id')
            ->get(['id', 'title', 'service_id'])
            ->groupBy('service_id')
            ->map(fn ($articulos) => $articulos->map(fn (Article $a) => ['id' => $a->id, 'title' => $a->title])->values());

        return view('tickets.create', [
            'serviciosPorDepartamento' => $servicios,
            'articulosPorServicio' => $articulosPorServicio,
            'misActivos' => $request->user()->assets()->where('active', true)->get(),
        ]);
    }

    public function store(StoreTicketRequest $request, CreateTicketAction $accion)
    {
        $ticket = $accion->ejecutar($request->user(), $request->validated());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} creado correctamente.");
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'requester', 'requesterDepartment', 'assignedDepartment', 'assignedUser',
            'service', 'category', 'priority', 'status', 'slaPolicy',
            'messages.user', 'messages.attachments', 'history.user',
        ]);

        $agentesDelArea = User::query()
            ->where('department_id', $ticket->assigned_department_id)
            ->role(['agente', 'encargado'])
            ->orderBy('name')
            ->get();

        return view('tickets.show', [
            'ticket' => $ticket,
            'agentesDelArea' => $agentesDelArea,
            'puedeGestionar' => $request->user()->can('update', $ticket),
            'esAdministrador' => $request->user()->hasRole('administrador'),
            'prioridades' => Priority::orderBy('level')->get(),
            'categorias' => Category::where('department_id', $ticket->assigned_department_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket, UpdateTicketDetailsAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $accion->ejecutar($ticket, $request->user(), $datos);

        return back()->with('success', 'Ticket actualizado.');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', "Ticket {$ticket->ticket_number} eliminado.");
    }

    public function assign(Request $request, Ticket $ticket, AssignTicketAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'agente_id' => ['required', 'exists:users,id'],
        ]);

        $agente = User::findOrFail($datos['agente_id']);

        $accion->ejecutar($ticket, $agente, $request->user());

        return back()->with('success', 'Ticket asignado.');
    }

    public function changeStatus(Request $request, Ticket $ticket, ChangeTicketStatusAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'estado' => ['required', 'string', 'exists:ticket_statuses,code'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ]);

        $accion->ejecutar($ticket, $datos['estado'], $request->user(), $datos['comentario'] ?? null);

        return back()->with('success', 'Estado actualizado.');
    }

    public function addMessage(Request $request, Ticket $ticket, AddTicketMessageAction $accion)
    {
        $this->authorize('view', $ticket);

        $puedeGestionar = $request->user()->can('update', $ticket);

        $datos = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'in:public,internal'],
            'adjuntos' => ['nullable', 'array'],
            'adjuntos.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ]);

        $tipo = ($puedeGestionar && ($datos['type'] ?? 'public') === 'internal')
            ? TicketMessageType::Internal
            : TicketMessageType::Public;

        $mensaje = $accion->ejecutar($ticket, $request->user(), $datos['body'], $tipo);

        foreach ($request->file('adjuntos', []) as $archivo) {
            $ruta = $archivo->store("tickets/{$ticket->id}", 'local');

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'ticket_message_id' => $mensaje->id,
                'uploaded_by' => $request->user()->id,
                'original_name' => $archivo->getClientOriginalName(),
                'path' => $ruta,
                'disk' => 'local',
                'mime_type' => $archivo->getClientMimeType(),
                'size' => $archivo->getSize(),
            ]);
        }

        return back()->with('success', 'Comentario agregado.');
    }

    public function resolve(Request $request, Ticket $ticket, ResolveTicketAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'resolucion' => ['required', 'string', 'max:5000'],
        ]);

        $accion->ejecutar($ticket, $request->user(), $datos['resolucion']);

        return back()->with('success', 'Ticket marcado como resuelto.');
    }

    public function close(Request $request, Ticket $ticket, CloseTicketAction $accion)
    {
        $this->authorize('close', $ticket);

        $accion->ejecutar($ticket, $request->user());

        return back()->with('success', 'Ticket cerrado.');
    }

    public function reopen(Request $request, Ticket $ticket, ReopenTicketAction $accion)
    {
        $this->authorize('reopen', $ticket);

        $datos = $request->validate([
            'motivo' => ['required', 'string', 'max:2000'],
        ]);

        $accion->ejecutar($ticket, $request->user(), $datos['motivo']);

        return back()->with('success', 'Ticket reabierto.');
    }

    public function escalate(Request $request, Ticket $ticket, EscalateTicketAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'assigned_department_id' => ['nullable', 'exists:departments,id'],
            'priority_id' => ['nullable', 'exists:priorities,id'],
            'motivo' => ['nullable', 'string', 'max:2000'],
        ]);

        if (empty($datos['assigned_department_id']) && empty($datos['priority_id'])) {
            throw ValidationException::withMessages([
                'escalamiento' => 'Indica un nuevo departamento y/o una nueva prioridad.',
            ]);
        }

        $accion->ejecutar(
            $ticket,
            $request->user(),
            $datos['assigned_department_id'] ?? null,
            $datos['priority_id'] ?? null,
            $datos['motivo'] ?? null,
        );

        return back()->with('success', 'Ticket escalado.');
    }

    public function downloadAttachment(Ticket $ticket, TicketAttachment $attachment)
    {
        $this->authorize('view', $ticket);

        abort_unless($attachment->ticket_id === $ticket->id, 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }
}
