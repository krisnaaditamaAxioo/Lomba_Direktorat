<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\Student;
use App\Models\ModuleQuestion;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $courses = Course::with([
            'angkatan',
            'modules' => function ($query) use ($user) {
                if ($user?->isGuru()) {
                    $query->where('created_by', $user->id);
                }
                $query->with('questions');
            },
        ])->latest()->get();

        // Guru hanya lihat kursus yang dia buat ATAU yang ada modul miliknya
        if ($user?->isGuru()) {
            $courses = $courses->filter(fn ($c) =>
                $c->created_by === $user->id || $c->modules->count() > 0
            )->values();
        }

        $angkatans = Angkatan::all();
        return view('admin.courses.index', compact('courses', 'angkatans'));
    }

    public function progress(Course $course)
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            $course->load(['modules' => function ($query) use ($user) {
                $query->where('created_by', $user->id);
            }]);
        } else {
            $course->load('modules');
        }

        $students = Student::query()
            ->when($course->angkatan_id, fn ($query) => $query->where('angkatan_id', $course->angkatan_id))
            ->orderBy('nama_lengkap')
            ->get();

        $progresses = ModuleProgress::whereIn('student_id', $students->pluck('id'))
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $students->each(function ($student) use ($course, $progresses) {
            $studentProgress = $progresses->get($student->id, collect())->keyBy('module_id');
            $student->module_progress = $studentProgress;
            $student->course_progress = $course->modules->count()
                ? (int) round($course->modules->sum(fn ($module) => $studentProgress[$module->id]->progress ?? 0) / $course->modules->count())
                : 0;
        });

        return view('admin.courses.progress', compact('course', 'students'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'angkatan_id' => 'nullable|exists:angkatans,id',
            ]);

            $data = $request->only(['title', 'description', 'angkatan_id']);
            $data['created_by'] = auth()->id();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('courses', 'public');
            }

            Course::create($data);
            return back()->with('success', 'Kursus berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan kursus: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Course $course)
    {
        $angkatans = Angkatan::all();
        return view('admin.courses.edit', compact('course', 'angkatans'));
    }

    public function update(Request $request, Course $course)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'angkatan_id' => 'nullable|exists:angkatans,id',
            ]);

            $data = $request->only(['title', 'description', 'angkatan_id']);

            if ($request->hasFile('image')) {
                if ($course->image) {
                    Storage::disk('public')->delete($course->image);
                }
                $data['image'] = $request->file('image')->store('courses', 'public');
            }

            $course->update($data);
            return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui kursus: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Course $course)
    {
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }
        $course->delete();
        return back()->with('success', 'Kursus berhasil dihapus.');
    }

    // Module Management within Course
    public function createModule(Course $course)
    {
        return view('admin.courses.modules.create', compact('course'));
    }

    public function storeModule(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'question' => 'nullable|string|max:30000',
            'type' => 'required|in:pdf,video,link,text,assignment,quiz,coding',
            'file' => 'nullable|file|max:20480', // 20MB limit
            'link' => 'nullable|string|url',
        ]);

        $data = [
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'question' => $validated['question'] ?? null,
            'type' => $validated['type'],
            'created_by' => auth()->id(),
        ];

        if ($validated['type'] === 'link' || $validated['type'] === 'video') {
            $data['file_path'] = $validated['link'];
        } elseif ($request->hasFile('file')) {
            $upFile = $request->file('file');
            $storedPath = $upFile->store('modules', 'public');
            $data['file_path'] = $storedPath;
        }

        $module = Module::create($data);

        if (in_array($module->type, ['assignment', 'quiz', 'coding'])) {
            return redirect()->route('admin.courses.modules.questions', $module->id)
                ->with('success', 'Modul berhasil dibuat! Silakan tambahkan soal untuk modul ini pada halaman ini.');
        }

        return redirect()->route('admin.courses.index')->with('success', 'Modul berhasil ditambahkan.');
    }

    public function previewModule(Module $module)
    {
        $module->load('course');
        return view('admin.courses.preview_module', compact('module'));
    }

    public function editModule(Module $module)
    {
        $user = auth()->user();
        if ($user->isGuru() && $module->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $module->load('course');

        return view('admin.courses.modules.edit', compact('module'));
    }

    public function updateModule(Request $request, Module $module)
    {
        $user = auth()->user();
        if ($user->isGuru() && $module->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'question' => 'nullable|string|max:30000',
            'type' => 'required|in:pdf,video,link,text,assignment,quiz,coding',
            'file' => 'nullable|file|max:20480',
            'link' => 'nullable|string|url',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'question' => $validated['question'] ?? null,
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'link' || $validated['type'] === 'video') {
            $data['file_path'] = $validated['link'];
        } elseif ($request->hasFile('file')) {
            // Delete old file if exists
            if ($module->file_path && !filter_var($module->file_path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($module->file_path);
            }
            $data['file_path'] = $request->file('file')->store('modules', 'public');
        }

        $module->update($data);
        return redirect()->route('admin.courses.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroyModule(Module $module)
    {
        $user = auth()->user();
        if ($user->isGuru() && $module->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        if ($module->file_path && !filter_var($module->file_path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($module->file_path);
        }
        $module->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Modul berhasil dihapus.');
    }

    public function questions(Module $module)
    {
        $user = auth()->user();
        if ($user->isGuru() && $module->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $module->load('course', 'questions');
        return view('admin.courses.modules.questions', compact('module'));
    }

    public function storeQuestion(Request $request, Module $module)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:30000',
            'type' => 'required|in:multiple_choice,essay,coding,file',
            'option_a' => 'nullable|string|max:10000',
            'option_b' => 'nullable|string|max:10000',
            'option_c' => 'nullable|string|max:10000',
            'option_d' => 'nullable|string|max:10000',
            'correct_answer' => 'nullable|in:a,b,c,d',
            'score' => 'required|integer|min:1|max:100',
        ]);
        if ($validated['type'] === 'multiple_choice' && (!$validated['option_a'] || !$validated['option_b'] || !$validated['option_c'] || !$validated['option_d'] || !$validated['correct_answer'])) {
            return back()->withErrors(['type' => 'Soal pilihan ganda wajib memiliki empat pilihan dan kunci jawaban.'])->withInput();
        }
        $module->questions()->create($validated);
        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, ModuleQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:30000',
            'type' => 'required|in:multiple_choice,essay,coding,file',
            'option_a' => 'nullable|string|max:10000',
            'option_b' => 'nullable|string|max:10000',
            'option_c' => 'nullable|string|max:10000',
            'option_d' => 'nullable|string|max:10000',
            'correct_answer' => 'nullable|in:a,b,c,d',
            'score' => 'required|integer|min:1|max:100',
        ]);
        if ($validated['type'] === 'multiple_choice' && (!$validated['option_a'] || !$validated['option_b'] || !$validated['option_c'] || !$validated['option_d'] || !$validated['correct_answer'])) {
            return back()->withErrors(['type' => 'Soal pilihan ganda wajib memiliki empat pilihan dan kunci jawaban.'])->withInput();
        }
        $question->update($validated);
        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(ModuleQuestion $question)
    {
        $moduleId = $question->module_id;
        $question->delete();
        return redirect()->route('admin.courses.modules.questions', $moduleId)->with('success', 'Soal berhasil dihapus.');
    }

    public function moduleSubmissions(Module $module)
    {
        $user = auth()->user();
        if ($user->isGuru() && $module->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $module->load('course', 'questions');
        $course = $module->course;

        $students = Student::query()
            ->when($course->angkatan_id, fn ($query) => $query->where('angkatan_id', $course->angkatan_id))
            ->orderBy('nama_lengkap')
            ->get();

        $submissions = AssignmentSubmission::where('module_id', $module->id)
            ->with('student')
            ->get()
            ->keyBy('student_id');

        return view('admin.courses.modules.submissions', compact('module', 'students', 'submissions'));
    }

    public function storeGradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'question_scores' => 'nullable|array',
            'question_scores.*' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:5000',
        ]);

        $submission->load('module.questions');
        $questionScores = $request->input('question_scores', []);
        $totalScore = 0;
        $maxScore = $submission->module->questions->sum('score') ?: 100;

        foreach ($submission->module->questions as $q) {
            $scoreGiven = isset($questionScores[$q->id]) ? (int) $questionScores[$q->id] : 0;
            // Cap to max question score
            $scoreGiven = min($scoreGiven, $q->score);
            $questionScores[$q->id] = $scoreGiven;
            $totalScore += $scoreGiven;
        }

        $submission->update([
            'score' => $totalScore,
            'max_score' => $maxScore,
            'question_scores' => $questionScores,
            'status' => 'graded',
            'feedback' => $request->input('feedback'),
        ]);

        ModuleProgress::updateOrCreate(
            ['student_id' => $submission->student_id, 'module_id' => $submission->module_id],
            ['progress' => 100, 'completed_at' => now(), 'last_seen_at' => now()]
        );

        return back()->with('success', 'Penilaian & Feedback untuk siswa berhasil disimpan!');
    }

    public function destroySubmission(AssignmentSubmission $submission)
    {
        $moduleId = $submission->module_id;
        $studentId = $submission->student_id;

        $paths = array_filter(array_merge(
            [$submission->file_path],
            is_array($submission->answer_files) ? array_values($submission->answer_files) : []
        ));

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }

        $submission->delete();

        ModuleProgress::where('student_id', $studentId)
            ->where('module_id', $moduleId)
            ->update([
                'progress' => 0,
                'completed_at' => null,
                'last_seen_at' => now(),
            ]);

        return back()->with('success', 'Jawaban siswa berhasil dihapus.');
    }

    public function allRekapNilai()
    {
        $user = auth()->user();

        $courses = Course::with([
            'angkatan',
            'modules' => function ($query) use ($user) {
                if ($user->isGuru()) {
                    $query->where('created_by', $user->id);
                }
                $query->with('questions');
            },
        ])->latest()->get();

        // Untuk guru: hanya tampilkan kursus yang dia buat ATAU yang ada modul miliknya
        if ($user->isGuru()) {
            $courses = $courses->filter(fn ($c) =>
                $c->created_by === $user->id || $c->modules->count() > 0
            )->values();
        }

        $courses->each(function ($course) {
            $students = Student::query()
                ->when($course->angkatan_id, fn ($query) => $query->where('angkatan_id', $course->angkatan_id))
                ->get();

            $submissions = AssignmentSubmission::whereIn('student_id', $students->pluck('id'))
                ->whereIn('module_id', $course->modules->pluck('id'))
                ->whereNotNull('score')
                ->get()
                ->groupBy('student_id');

            $gradableModules = $course->modules;

            $totalCourseScore = 0;
            $gradedStudentsCount = 0;

            $students->each(function ($student) use ($gradableModules, $submissions, &$totalCourseScore, &$gradedStudentsCount) {
                $studentSubmissions = $submissions->get($student->id, collect())->keyBy('module_id');
                $totalScoreEarned = 0;
                $totalMaxPossible = 0;

                foreach ($gradableModules as $module) {
                    $sub = $studentSubmissions->get($module->id);
                    if ($sub && $sub->score !== null) {
                        $max = $sub->max_score ?: ($module->questions->sum('score') ?: 100);
                        $totalScoreEarned += $sub->score;
                        $totalMaxPossible += $max;
                    }
                }

                if ($totalMaxPossible > 0) {
                    $avg = round(($totalScoreEarned / $totalMaxPossible) * 100, 1);
                    $totalCourseScore += $avg;
                    $gradedStudentsCount++;
                }
            });

            $course->student_count = $students->count();
            $course->graded_student_count = $gradedStudentsCount;
            $course->course_average = $gradedStudentsCount > 0 ? round($totalCourseScore / $gradedStudentsCount, 1) : 0;
        });

        return view('admin.courses.all_rekap', compact('courses'));
    }

    public function rekapNilai(Course $course)
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            $course->load([
                'angkatan',
                'modules' => function ($query) use ($user) {
                    $query->where('created_by', $user->id)->with('questions');
                },
            ]);
        } else {
            $course->load(['modules.questions', 'angkatan']);
        }

        $students = Student::query()
            ->when($course->angkatan_id, fn ($query) => $query->where('angkatan_id', $course->angkatan_id))
            ->orderBy('nama_lengkap')
            ->get();

        $submissions = AssignmentSubmission::whereIn('student_id', $students->pluck('id'))
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $gradableModules = $course->modules;

        $totalCourseScore = 0;
        $totalCourseStudentsWithGrades = 0;
        $highestScore = 0;
        $highestStudent = null;
        $lowestScore = 100;
        $lowestStudent = null;
        $passedCount = 0;

        $moduleAverages = [];
        foreach ($gradableModules as $m) {
            $moduleAverages[$m->id] = ['sum' => 0, 'count' => 0, 'max' => $m->questions->sum('score') ?: 100];
        }

        $students->each(function ($student) use ($course, $gradableModules, $submissions, &$totalCourseScore, &$totalCourseStudentsWithGrades, &$highestScore, &$highestStudent, &$lowestScore, &$lowestStudent, &$passedCount, &$moduleAverages) {
            $studentSubmissions = $submissions->get($student->id, collect())->keyBy('module_id');

            $totalScoreEarned = 0;
            $totalMaxPossible = 0;
            $gradedCount = 0;
            $scoresList = [];

            foreach ($gradableModules as $module) {
                $sub = $studentSubmissions->get($module->id);
                $max = $sub && $sub->max_score ? $sub->max_score : ($module->questions->sum('score') ?: 100);

                if ($sub && $sub->score !== null) {
                    $score = (float) $sub->score;
                    $pct = $max > 0 ? round(($score / $max) * 100, 1) : 0;
                    $totalScoreEarned += $score;
                    $totalMaxPossible += $max;
                    $gradedCount++;

                    $scoresList[$module->id] = [
                        'score' => $score,
                        'max_score' => $max,
                        'percentage' => $pct,
                        'status' => $sub->status,
                    ];

                    $moduleAverages[$module->id]['sum'] += $pct;
                    $moduleAverages[$module->id]['count']++;
                } else {
                    $scoresList[$module->id] = [
                        'score' => null,
                        'max_score' => $max,
                        'percentage' => null,
                        'status' => $sub ? $sub->status : 'unsubmitted',
                    ];
                }
            }

            $student->scores_list = $scoresList;
            $student->total_score = $totalScoreEarned;
            $student->total_max_score = $totalMaxPossible;

            $avgScore = $totalMaxPossible > 0 ? round(($totalScoreEarned / $totalMaxPossible) * 100, 1) : 0;
            $student->average_score = $avgScore;

            if ($gradedCount > 0) {
                $totalCourseScore += $avgScore;
                $totalCourseStudentsWithGrades++;

                if ($avgScore >= $highestScore) {
                    $highestScore = $avgScore;
                    $highestStudent = $student;
                }
                if ($lowestStudent === null || $avgScore <= $lowestScore) {
                    $lowestScore = $avgScore;
                    $lowestStudent = $student;
                }
                if ($avgScore >= 75) {
                    $passedCount++;
                }
            }
        });

        $courseAverageScore = $totalCourseStudentsWithGrades > 0
            ? round($totalCourseScore / $totalCourseStudentsWithGrades, 1)
            : 0;

        if ($totalCourseStudentsWithGrades === 0) {
            $lowestScore = 0;
        }

        $moduleSummary = [];
        foreach ($gradableModules as $m) {
            $count = $moduleAverages[$m->id]['count'];
            $avgPct = $count > 0 ? round($moduleAverages[$m->id]['sum'] / $count, 1) : 0;
            $moduleSummary[$m->id] = [
                'avg_percentage' => $avgPct,
                'count' => $count,
            ];
        }

        return view('admin.courses.rekap_nilai', compact(
            'course',
            'students',
            'gradableModules',
            'courseAverageScore',
            'highestScore',
            'highestStudent',
            'lowestScore',
            'lowestStudent',
            'passedCount',
            'totalCourseStudentsWithGrades',
            'moduleSummary'
        ));
    }

    public function printRekapNilai(Course $course)
    {
        $user = auth()->user();

        if ($user->isGuru()) {
            $course->load([
                'angkatan',
                'modules' => function ($query) use ($user) {
                    $query->where('created_by', $user->id)->with('questions');
                },
            ]);
        } else {
            $course->load(['modules.questions', 'angkatan']);
        }
        $students = Student::query()
            ->when($course->angkatan_id, fn ($query) => $query->where('angkatan_id', $course->angkatan_id))
            ->orderBy('nama_lengkap')
            ->get();
        $submissions = AssignmentSubmission::whereIn('student_id', $students->pluck('id'))
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $students->each(function ($student) use ($course, $submissions) {
            $studentSubmissions = $submissions->get($student->id, collect())->keyBy('module_id');
            $student->scores = $course->modules->map(function ($module) use ($studentSubmissions) {
                $submission = $studentSubmissions->get($module->id);
                $max = $submission?->max_score ?: ($module->questions->sum('score') ?: 100);
                $score = $submission?->score;
                return ['score' => $score, 'max' => $max, 'percentage' => $score !== null && $max > 0 ? ($score / $max) * 100 : null];
            });
            $graded = $student->scores->whereNotNull('percentage');
            $student->average_score = $graded->count() ? round($graded->avg('percentage'), 1) : 0;
        });

        $gradedAverages = $students->where('average_score', '>', 0)->pluck('average_score');
        $courseAverage = $gradedAverages->count() ? round($gradedAverages->average(), 1) : 0;

        return view('admin.courses.rekap_nilai_print', compact('course', 'students', 'courseAverage'));
    }
}
