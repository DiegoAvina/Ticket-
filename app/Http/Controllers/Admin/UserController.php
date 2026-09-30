<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Departments\Models\Department;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['department', 'roles'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', ['usuarios' => $usuarios]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'usuario' => $user,
            'roles' => Role::orderBy('name')->get(),
            'departamentos' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $esUnoMismo = $user->id === $request->user()->id;

        $datos = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($esUnoMismo && (! in_array('administrador', $datos['roles'], true) || $request->boolean('active') === false)) {
            return back()->withErrors([
                'roles' => 'No puedes quitarte a ti mismo el rol de administrador ni desactivar tu propia cuenta.',
            ]);
        }

        $user->update([
            'department_id' => $datos['department_id'] ?? null,
            'active' => $esUnoMismo ? true : $request->boolean('active'),
        ]);

        $user->syncRoles($datos['roles']);

        return redirect()->route('admin.users.index')->with('success', "Usuario {$user->name} actualizado.");
    }
}
