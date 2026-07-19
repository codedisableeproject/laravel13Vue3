<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('deleted', 0)->paginate(10);
        $roles = Role::where('deleted', 0)->get();
        return Inertia::render('User/Index', compact('users', 'roles'));
    }

    public function create()
    {
        return Inertia::render('User/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|integer|exists:roles,id,deleted,0,deleted_at,NULL',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'password.min' => 'Password wajib minimal 6 karakter',
            'role_id.required' => 'Pilih Role',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'deleted' => 0
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('User/Edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'password.min' => 'Password wajib minimal 6 karakter',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('user.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'deleted' => 1,
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}
