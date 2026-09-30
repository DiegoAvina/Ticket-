<?php

namespace App\Http\Controllers;

use App\Domain\Departments\Models\Department;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Estados considerados "abiertos" (todavía no se está trabajando
     * activamente ni están resueltos) vs. el resto de agrupaciones que usan
     * las vistas de la sección 16 del spec.
     */
    private const ABIERTOS = ['new', 'assigned', 'waiting_user'];

    private const RESUELTOS = ['resolved', 'closed'];

    private const CERRADOS_DEFINITIVOS = ['resolved', 'closed', 'cancelled'];

    /** Relaciones que usa cada fila del listado de tickets del dashboard. */
    private const RELACIONES_FILA = ['status', 'priority', 'requester', 'requesterDepartment', 'assignedDepartment', 'assignedUser', 'service', 'category'];

    public function index(Request $request)
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole('administrador') => $this->administrador(),
            $user->hasRole('encargado') => $this->encargado($user),
            $user->hasRole('agente') => $this->agente($user),
            default => $this->usuario($user),
        };
    }

    private function usuario(User $user)
    {
        $base = Ticket::where('requester_id', $user->id);

        return view('dashboards.usuario', [
            'misTickets' => (clone $base)->with(self::RELACIONES_FILA)->latest()->limit(10)->get(),
            'total' => (clone $base)->count(),
            'abiertos' => $this->contarPorEstado($base, self::ABIERTOS),
            'enProceso' => $this->contarPorEstado($base, ['in_progress']),
            'resueltos' => $this->contarPorEstado($base, self::RESUELTOS),
            'porEstado' => $this->distribucionPorEstado($base),
            'enRiesgo' => $this->enRiesgo($base),
        ]);
    }

    private function agente(User $user)
    {
        $mios = Ticket::where('assigned_user_id', $user->id);

        $nuevosDelArea = Ticket::where('assigned_department_id', $user->department_id)
            ->whereNull('assigned_user_id')
            ->whereHas('status', fn (Builder $q) => $q->where('code', 'new'))
            ->count();

        $proximosAVencer = (clone $mios)
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<=', now()->addHours(4))
            ->whereDoesntHave('status', fn (Builder $q) => $q->whereIn('code', self::CERRADOS_DEFINITIVOS))
            ->count();

        return view('dashboards.agente', [
            'misTickets' => (clone $mios)->with(self::RELACIONES_FILA)->latest()->limit(10)->get(),
            'nuevosDelArea' => $nuevosDelArea,
            'enProceso' => $this->contarPorEstado($mios, ['in_progress']),
            'esperandoUsuario' => $this->contarPorEstado($mios, ['waiting_user']),
            'proximosAVencer' => $proximosAVencer,
            'resueltos' => $this->contarPorEstado($mios, self::RESUELTOS),
            'porEstado' => $this->distribucionPorEstado($mios),
            'enRiesgo' => $this->enRiesgo($mios),
        ]);
    }

    private function encargado(User $user)
    {
        $departamento = $user->department;
        $base = Ticket::where('assigned_department_id', $user->department_id);

        return view('dashboards.encargado', [
            'departamento' => $departamento,
            'nuevos' => $this->contarPorEstado($base, ['new']),
            'enProceso' => $this->contarPorEstado($base, ['in_progress']),
            'esperandoUsuario' => $this->contarPorEstado($base, ['waiting_user']),
            'resueltos' => $this->contarPorEstado($base, self::RESUELTOS),
            'vencidos' => $this->contarVencidos($base),
            'slaCumplimiento' => $this->calcularSlaCumplimiento($base),
            'tiempoPromedioHoras' => $this->calcularTiempoPromedio($base),
            'cargaPorAgente' => $this->cargaPorAgente($user->department_id),
            'ticketsRecientes' => (clone $base)->with(self::RELACIONES_FILA)->latest()->limit(10)->get(),
            'porEstado' => $this->distribucionPorEstado($base),
            'enRiesgo' => $this->enRiesgo($base),
        ]);
    }

    private function administrador()
    {
        $base = Ticket::query();

        $porArea = Department::withCount('tickets')->orderByDesc('tickets_count')->get();

        $tendencia = Ticket::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('dashboards.administrador', [
            'total' => (clone $base)->count(),
            'porEstado' => $this->distribucionPorEstado($base),
            'porArea' => $porArea,
            'vencidos' => $this->contarVencidos($base),
            'slaCumplimiento' => $this->calcularSlaCumplimiento($base),
            'tiempoPromedioHoras' => $this->calcularTiempoPromedio($base),
            'tendenciaFechas' => $tendencia->pluck('fecha'),
            'tendenciaTotales' => $tendencia->pluck('total'),
            'ticketsRecientes' => (clone $base)->with(self::RELACIONES_FILA)->latest()->limit(10)->get(),
            'enRiesgo' => $this->enRiesgo($base),
        ]);
    }

    private function contarPorEstado(Builder $query, array $codigos): int
    {
        return (clone $query)->whereHas('status', fn (Builder $q) => $q->whereIn('code', $codigos))->count();
    }

    /**
     * Conteo por estado dentro del alcance dado, en el orden configurado, listo
     * para las gráficas: [['code', 'label', 'value', 'color'], ...].
     */
    private function distribucionPorEstado(Builder $query)
    {
        return (clone $query)
            ->join('ticket_statuses', 'ticket_statuses.id', '=', 'tickets.status_id')
            ->select('ticket_statuses.code', 'ticket_statuses.name', DB::raw('count(*) as total'))
            ->groupBy('ticket_statuses.code', 'ticket_statuses.name', 'ticket_statuses.order')
            ->orderBy('ticket_statuses.order')
            ->toBase()
            ->get()
            ->map(fn ($fila) => [
                'code' => $fila->code,
                'label' => $fila->name,
                'value' => (int) $fila->total,
                'color' => config('dashboard.status_colors')[$fila->code] ?? '#9ca3af',
            ]);
    }

    /** Tickets abiertos con SLA de resolución más próximo a vencer (incluye vencidos). */
    private function enRiesgo(Builder $query, int $limite = 4)
    {
        return (clone $query)
            ->with(['status', 'priority', 'requester', 'assignedUser'])
            ->whereNotNull('sla_resolution_due_at')
            ->whereDoesntHave('status', fn (Builder $q) => $q->whereIn('code', self::CERRADOS_DEFINITIVOS))
            ->orderBy('sla_resolution_due_at')
            ->limit($limite)
            ->get();
    }

    private function contarVencidos(Builder $query): int
    {
        return (clone $query)
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->whereDoesntHave('status', fn (Builder $q) => $q->whereIn('code', self::CERRADOS_DEFINITIVOS))
            ->count();
    }

    /**
     * % de tickets resueltos/cerrados que se resolvieron antes de la fecha
     * límite de SLA. Null si todavía no hay ninguno resuelto para medir.
     */
    private function calcularSlaCumplimiento(Builder $query): ?float
    {
        $resueltos = (clone $query)
            ->whereHas('status', fn (Builder $q) => $q->whereIn('code', self::RESUELTOS))
            ->whereNotNull('resolved_at');

        $total = (clone $resueltos)->count();

        if ($total === 0) {
            return null;
        }

        $cumplidos = (clone $resueltos)
            ->whereNotNull('sla_resolution_due_at')
            ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
            ->count();

        return round(($cumplidos / $total) * 100, 1);
    }

    private function calcularTiempoPromedio(Builder $query): ?float
    {
        $promedio = (clone $query)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as promedio')
            ->value('promedio');

        return $promedio !== null ? round((float) $promedio, 1) : null;
    }

    private function cargaPorAgente(?int $departmentId)
    {
        if (! $departmentId) {
            return collect();
        }

        return Ticket::where('assigned_department_id', $departmentId)
            ->whereNotNull('assigned_user_id')
            ->whereDoesntHave('status', fn (Builder $q) => $q->whereIn('code', self::CERRADOS_DEFINITIVOS))
            ->select('assigned_user_id', DB::raw('count(*) as total'))
            ->groupBy('assigned_user_id')
            ->with('assignedUser')
            ->get();
    }
}
