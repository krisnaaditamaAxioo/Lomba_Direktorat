<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagementUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('management-users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('management-users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'no_wa' => 'nullable',
            'role' => 'required|in:admin,guru,wali_kelas,wakasis,bk,teller,bums',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('management-users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $managementUser)
    {
        return view('management-users.edit', compact('managementUser'));
    }

    public function update(Request $request, User $managementUser)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $managementUser->id,
            'email' => 'required|email|unique:users,email,' . $managementUser->id,
            'password' => 'nullable|min:6',
            'no_wa' => 'nullable',
            'role' => 'required|in:admin,guru,wali_kelas,wakasis,bk,teller,bums',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $managementUser->update($validated);

        return redirect()->route('management-users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $managementUser)
    {
        $managementUser->delete();
        return redirect()->route('management-users.index')->with('success', 'User deleted successfully.');
    }

    public function resetPassword(User $managementUser)
    {
        // Reset password ke default: password123
        $managementUser->update(['password' => Hash::make('password123')]);
        return redirect()->route('management-users.index')->with('success', 'Password user berhasil direset ke password123.');
    }
}
