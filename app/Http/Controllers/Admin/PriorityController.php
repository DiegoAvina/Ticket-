<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Priority;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        return view('admin.priorities.index', [
            'prioridades' => Priority::orderBy('level')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.priorities.create');
    }

    public function store(Request $request)
    {
        Priority::create($this->validar($request));

        return redirect()->route('admin.priorities.index')->with('success', 'Prioridad creada.');
    }

    public function edit(Priority $priority)
    {
        return view('admin.priorities.edit', ['prioridad' => $priority]);
    }

    public function update(Request $request, Priority $priority)
    {
        $priority->update($this->validar($request));

        return redirect()->route('admin.priorities.index')->with('success', 'Prioridad actualizada.');
    }

    public function destroy(Priority $priority)
    {
        try {
            $priority->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'priority' => "No se puede eliminar \"{$priority->name}\": tiene tickets asociados.",
            ]);
        }

        return redirect()->route('admin.priorities.index')->with('success', 'Prioridad eliminada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:1', 'max:255'],
            'first_response_minutes' => ['required', 'integer', 'min:1'],
            'resolution_minutes' => ['required', 'integer', 'min:1'],
        ]);
    }
}
