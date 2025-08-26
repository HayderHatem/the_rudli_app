<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of admin users
     */
    public function index()
    {
        $users = AdminUser::with('client')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new admin user
     */
    public function create()
    {
        $clients = Client::where('is_active', true)->get();
        return view('admin.users.create', compact('clients'));
    }

    /**
     * Store a newly created admin user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['super_admin', 'client_admin'])],
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'client_id' => $request->client_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Display the specified admin user
     */
    public function show(AdminUser $user)
    {
        $user->load('client');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified admin user
     */
    public function edit(AdminUser $user)
    {
        $clients = Client::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'clients'));
    }

    /**
     * Update the specified admin user
     */
    public function update(Request $request, AdminUser $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admin_users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['super_admin', 'client_admin'])],
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'client_id' => $request->client_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Remove the specified admin user
     */
    public function destroy(AdminUser $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
