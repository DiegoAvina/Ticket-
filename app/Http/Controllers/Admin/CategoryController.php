<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Category;
use App\Domain\Departments\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categorias' => Category::with('department')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.create', [
            'departamentos' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Category::create($this->validar($request));

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', [
            'categoria' => $category,
            'departamentos' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validar($request));

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'category' => "No se puede eliminar \"{$category->name}\": está en uso.",
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);
    }
}
