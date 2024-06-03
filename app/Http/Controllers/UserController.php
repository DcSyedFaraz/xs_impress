<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        return view('modules.user.get', compact(['users']));
    }

    public function new($id = null)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $user = $id ? User::findOrFail($id) : new User();

        return view('modules.user.add', compact('roles', 'permissions', 'user', 'id'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => 'required|string|min:8|',
            'role_id' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->role_id);
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('message', 'User has been created.');
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('modules.user.add', compact(['id', 'user', 'roles', 'permissions']));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($id) {
            $user = User::findOrFail($id);
            $user->update($data);
        } else {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
        }

        $user->syncRoles($request->role_id);
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('message', 'User saved successfully!');
    }
}
