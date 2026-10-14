<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentNotification;
use App\Models\Student;

class StudentNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = StudentNotification::with('student')->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::orderBy('nama_lengkap')->get();
        return view('admin.notifications.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'attachment' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $data = ['created_by' => auth()->id()];
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('notifications', 'public');
        }

        StudentNotification::sendToMultiple(
            $validated['student_ids'],
            'admin_custom',
            $validated['title'],
            $validated['message'],
            $data
        );

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil dikirim ke ' . count($validated['student_ids']) . ' siswa!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentNotification $notification)
    {
        $students = Student::orderBy('nama_lengkap')->get();
        return view('admin.notifications.edit', compact('notification', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentNotification $notification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $data = $notification->data ?? [];
        if ($request->hasFile('attachment')) {
            if (!empty($data['attachment']) && Storage::disk('public')->exists($data['attachment'])) {
                Storage::disk('public')->delete($data['attachment']);
            }
            $data['attachment'] = $request->file('attachment')->store('notifications', 'public');
        }

        $notification->update(array_merge($validated, ['data' => $data]));

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentNotification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil dihapus!');
    }
}
