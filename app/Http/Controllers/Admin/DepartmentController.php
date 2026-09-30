<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Departments\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('admin.departments.index', [
            'departamentos' => Department::withCount(['users', 'tickets'])->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Department::create([
            ...$datos,
            'slug' => Str::slug($datos['name']),
            'active' => true,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Departamento creado.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', ['departamento' => $department]);
    }

    public function update(Request $request, Department $department)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $department->update([
            ...$datos,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Departamento actualizado.');
    }

    public function destroy(Department $department)
    {
        try {
            $department->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'department' => "No se puede eliminar \"{$department->name}\": tiene tickets asociados.",
            ]);
        }

        return redirect()->route('admin.departments.index')->with('success', 'Departamento eliminado.');
    }
}
