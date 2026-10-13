<?php

namespace App\Http\Controllers;

use App\Models\ParentUser;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index()
    {
        $parents = ParentUser::with('student')->latest()->get();

        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        $students = Student::orderBy('nama_lengkap')->get();

        return view('parents.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:parents,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:L,P',
            'student_id' => 'required|exists:students,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        ParentUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'student_id' => $validated['student_id'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('parents.index')->with('success', 'Akun orang tua berhasil ditambahkan.');
    }

    public function edit(ParentUser $parent)
    {
        $students = Student::orderBy('nama_lengkap')->get();

        return view('parents.edit', compact('parent', 'students'));
    }

    public function update(Request $request, ParentUser $parent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:parents,email,' . $parent->id,
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:L,P',
            'student_id' => 'required|exists:students,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $parent->update($validated);

        return redirect()->route('parents.index')->with('success', 'Akun orang tua berhasil diperbarui.');
    }

    public function destroy(ParentUser $parent)
    {
        $parent->delete();

        return redirect()->route('parents.index')->with('success', 'Akun orang tua berhasil dihapus.');
    }
}
