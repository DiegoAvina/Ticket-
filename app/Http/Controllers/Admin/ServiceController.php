<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Departments\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'servicios' => Service::with(['department', 'category', 'defaultPriority'])->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.create', $this->datosDeFormulario());
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Service::create([...$datos, 'active' => true]);

        return redirect()->route('admin.services.index')->with('success', 'Servicio creado.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'servicio' => $service,
            ...$this->datosDeFormulario(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $datos = $this->validar($request);

        $service->update([...$datos, 'active' => $request->boolean('active')]);

        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service)
    {
        try {
            $service->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'service' => "No se puede eliminar \"{$service->name}\": tiene tickets asociados.",
            ]);
        }

        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'default_priority_id' => ['nullable', 'exists:priorities,id'],
        ]);
    }

    private function datosDeFormulario(): array
    {
        return [
            'departamentos' => Department::orderBy('name')->get(),
            'categorias' => Category::orderBy('name')->get(),
            'prioridades' => Priority::orderBy('level')->get(),
        ];
    }
}
