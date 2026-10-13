<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AngkatanController extends Controller
{
    public function index()
    {
        $angkatans = Angkatan::ordered()->with('waliKelas')->get();

        return view('angkatans.index', compact('angkatans'));
    }

    public function create()
    {
        $waliKelasList = \App\Models\User::where('role', 'wali_kelas')->get();
        return view('angkatans.create', compact('waliKelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:angkatans,code',
            'name' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'tahun_masuk' => 'nullable|integer|min:1900|max:2100',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Angkatan::create($validated);

        return redirect()->route('angkatans.index')->with('success', 'Data angkatan berhasil ditambahkan.');
    }

    public function edit(Angkatan $angkatan)
    {
        $waliKelasList = \App\Models\User::where('role', 'wali_kelas')->get();
        return view('angkatans.edit', compact('angkatan', 'waliKelasList'));
    }

    public function update(Request $request, Angkatan $angkatan)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:angkatans,code,' . $angkatan->id,
            'name' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'tahun_masuk' => 'nullable|integer|min:1900|max:2100',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $oldCode = $angkatan->code;

        DB::transaction(function () use ($angkatan, $validated, $oldCode) {
            $angkatan->update($validated);

            if ($oldCode !== $validated['code']) {
                Student::where('angkatan', $oldCode)->update(['angkatan' => $validated['code']]);
                Registration::where('angkatan', $oldCode)->update(['angkatan' => $validated['code']]);
            }
        });

        return redirect()->route('angkatans.index')->with('success', 'Data angkatan berhasil diperbarui.');
    }

    public function destroy(Angkatan $angkatan)
    {
        $studentCount = Student::where('angkatan', $angkatan->code)->count();
        $registrationCount = Registration::where('angkatan', $angkatan->code)->count();

        if ($studentCount > 0 || $registrationCount > 0) {
            return redirect()->route('angkatans.index')->with('error', 'Angkatan tidak bisa dihapus karena masih dipakai oleh siswa atau pendaftaran.');
        }

        $angkatan->delete();

        return redirect()->route('angkatans.index')->with('success', 'Data angkatan berhasil dihapus.');
    }
}
