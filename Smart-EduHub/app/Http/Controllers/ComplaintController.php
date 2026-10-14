<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    // Admin Methods
    public function index()
    {
        $this->authorizeAdmin();
        $query = Complaint::with(['complainant', 'handledBy'])->latest();
        
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('search')) {
            $search = request('search');
            $query->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }
        
        $complaints = $query->paginate(20);
        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $this->authorizeAdmin();
        $complaint->load(['complainant', 'handledBy']);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'response' => 'nullable|string'
        ]);
        
        if (in_array($validated['status'], ['in_progress', 'resolved', 'rejected']) && !$complaint->handled_by) {
            $validated['handled_by'] = Auth::id();
            $validated['handled_at'] = now();
        }
        
        $complaint->update($validated);
        
        return back()->with('success', 'Aduan berhasil diperbarui!');
    }

    public function destroy(Complaint $complaint)
    {
        $this->authorizeAdmin();
        if ($complaint->proof_photo) {
            Storage::disk('public')->delete($complaint->proof_photo);
        }
        $complaint->delete();
        return back()->with('success', 'Aduan berhasil dihapus!');
    }

    // Student Methods to submit complaints
    public function create()
    {
        if (Auth::guard('student')->check()) {
            return view('siswa.complaints.create');
        }
        
        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048'
        ]);
        
        $complainant = null;
        
        if (Auth::guard('student')->check()) {
            $complainant = Auth::guard('student')->user();
        } elseif (Auth::check()) {
            $complainant = Auth::user();
        }
        
        if (!$complainant) {
            return back()->with('error', 'Silakan login terlebih dahulu!');
        }
        
        $validated['complainant_type'] = $complainant->getMorphClass();
        $validated['complainant_id'] = $complainant->id;
        
        if ($request->hasFile('proof_photo')) {
            $validated['proof_photo'] = $request->file('proof_photo')->store('complaints', 'public');
        }
        
        Complaint::create($validated);
        
        if (Auth::guard('student')->check()) {
            return redirect()->route('siswa.complaints.index')->with('success', 'Aduan berhasil dikirim!');
        }
        
        return back()->with('success', 'Aduan berhasil dikirim!');
    }

    // Student own complaints list
    public function myComplaints()
    {
        if (Auth::guard('student')->check()) {
            $complainant = Auth::guard('student')->user();
            $complaints = $complainant->complaints()->latest()->paginate(10);
            return view('siswa.complaints.index', compact('complaints'));
        }
        
        return redirect()->route('login');
    }

    public function myShow(Complaint $complaint)
    {
        if (!Auth::guard('student')->check()) {
            return redirect()->route('login');
        }

        $complainant = Auth::guard('student')->user();

        if (!$complainant || $complaint->complainant_id != $complainant->id || $complaint->complainant_type != $complainant->getMorphClass()) {
            abort(403);
        }

        return view('siswa.complaints.show', compact('complaint'));
    }

    public function myDestroy(Complaint $complaint)
    {
        $complainant = null;
        
        if (Auth::guard('student')->check()) {
            $complainant = Auth::guard('student')->user();
        } elseif (Auth::check()) {
            $complainant = Auth::user();
        }
        
        if (!$complainant || $complaint->complainant_id != $complainant->id || $complaint->complainant_type != $complainant->getMorphClass()) {
            abort(403);
        }

        if ($complaint->proof_photo) {
            Storage::disk('public')->delete($complaint->proof_photo);
        }
        $complaint->delete();
        
        if (Auth::guard('student')->check()) {
            return redirect()->route('siswa.complaints.index')->with('success', 'Aduan berhasil dihapus!');
        }
        
        return back()->with('success', 'Aduan berhasil dihapus!');
    }

    private function authorizeAdmin()
    {
        if (!Auth::check()) {
            abort(403);
        }
    }
}
