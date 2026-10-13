<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Point;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Support\Facades\Storage;

class PointController extends Controller
{
    private function getViewYear()
    {
        return session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
    }

    public function index()
    {
        $viewYear = $this->getViewYear();

        $query = Point::with(['student'])
            ->where('academic_year', $viewYear);

        $points = $query->latest()->get();
        
        $statsQuery = Point::where('academic_year', $viewYear);

        $stats = [
            'total_merit' => (clone $statsQuery)->where('type', 'achievement')->sum('score'),
            'total_demerit' => (clone $statsQuery)->where('type', 'violation')->sum('score'),
            'active_citations' => (clone $statsQuery)->where('date', '>=', now()->subDays(30))->count(),
        ];

        $distributionQuery = (clone $statsQuery);
        $totalPoints = $distributionQuery->sum('score') ?: 1; // Avoid division by zero

        $distribution = [
            'Academic' => [
                'count' => (clone $distributionQuery)->where(function($q) {
                    $q->where('reason', 'like', '%academic%')->orWhere('reason', 'like', '%belajar%');
                })->sum('score'),
                'color' => 'indigo'
            ],
            'Extracurricular' => [
                'count' => (clone $distributionQuery)->where(function($q) {
                    $q->where('reason', 'like', '%ekskul%')->orWhere('reason', 'like', '%lomba%');
                })->sum('score'),
                'color' => 'emerald'
            ],
            'Behavior' => [
                'count' => (clone $distributionQuery)->where(function($q) {
                    $q->where('reason', 'like', '%disiplin%')->orWhere('reason', 'like', '%perilaku%');
                })->sum('score'),
                'color' => 'amber'
            ]
        ];

        // Calculate percentages
        foreach ($distribution as $key => $data) {
            $distribution[$key]['percentage'] = round(($data['count'] / $totalPoints) * 100);
        }

        return view('points.index', compact('points', 'stats', 'distribution'));
    }

    public function leaderboard()
    {
        $viewYear = $this->getViewYear();

        $topAchievements = Point::where('type', 'achievement')
            ->where('academic_year', $viewYear)
            ->select('student_id')
            ->selectRaw('sum(score) as total_score')
            ->groupBy('student_id')
            ->with(['student'])
            ->orderByDesc('total_score')
            ->take(10)
            ->get();

        $topViolations = Point::where('type', 'violation')
            ->where('academic_year', $viewYear)
            ->select('student_id')
            ->selectRaw('sum(score) as total_score')
            ->groupBy('student_id')
            ->with(['student'])
            ->orderByDesc('total_score')
            ->take(10)
            ->get();

        return view('points.leaderboard', compact('topAchievements', 'topViolations'));
    }

    public function create()
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $students = Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*')
            ->get();
        return view('points.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:achievement,violation',
            'score' => 'required|integer',
            'reason' => 'required|string',
            'date' => 'required|date',
            'proof_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('proof_photo')) {
            $validated['proof_photo'] = $this->processAndStoreImage($request->file('proof_photo'));
        }

        $validated['academic_year'] = $this->getViewYear();

        $point = Point::create($validated);

        // Send notification to student
        $student = Student::find($validated['student_id']);
        $typeText = $validated['type'] === 'achievement' ? 'Prestasi' : 'Pelanggaran';
        StudentNotification::sendTo(
            $validated['student_id'],
            'point',
            "Perubahan Poin: $typeText",
            "Anda mendapatkan poin {$validated['score']} karena: {$validated['reason']}",
            ['point_id' => $point->id, 'type' => $validated['type'], 'score' => $validated['score']]
        );

        return redirect()->route('points')->with('success', 'Point record created.');
    }

    public function edit(Point $point)
    {
        $students = Student::all();
        return view('points.edit', compact('point', 'students'));
    }

    public function update(Request $request, Point $point)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:achievement,violation',
            'score' => 'required|integer',
            'reason' => 'required|string',
            'date' => 'required|date',
            'proof_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('proof_photo')) {
            if ($point->proof_photo) {
                Storage::disk('public')->delete($point->proof_photo);
            }
            $validated['proof_photo'] = $this->processAndStoreImage($request->file('proof_photo'));
        }

        if (!$point->academic_year) {
            $validated['academic_year'] = $this->getViewYear();
        }

        $point->update($validated);

        return redirect()->route('points')->with('success', 'Point record updated.');
    }

    public function destroy(Point $point)
    {
        if ($point->proof_photo) {
            Storage::disk('public')->delete($point->proof_photo);
        }
        $point->delete();
        return redirect()->route('points')->with('success', 'Point record deleted.');
    }

    private function processAndStoreImage($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $tempPath = $file->getRealPath();
        
        $directory = 'points';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        
        $targetPath = storage_path('app/public/' . $directory . '/' . $filename);

        $image = null;
        $compressed = false;

        try {
            if ($extension == 'jpg' || $extension == 'jpeg') {
                $image = @imagecreatefromjpeg($tempPath);
                if ($image) {
                    imagejpeg($image, $targetPath, 60);
                    $compressed = true;
                }
            } elseif ($extension == 'png') {
                $image = @imagecreatefrompng($tempPath);
                if ($image) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, $targetPath, 6);
                    $compressed = true;
                }
            } elseif ($extension == 'webp') {
                $image = @imagecreatefromwebp($tempPath);
                if ($image) {
                    imagewebp($image, $targetPath, 60);
                    $compressed = true;
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
        }

        if ($image) {
            imagedestroy($image);
        }

        if (!$compressed) {
            $file->storeAs($directory, $filename, 'public');
        }

        return $directory . '/' . $filename;
    }
}
