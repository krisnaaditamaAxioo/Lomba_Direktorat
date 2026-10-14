<?php

namespace App\Http\Controllers;

use App\Exports\ExamQuestionsExport;
use App\Exports\ExamQuestionsTemplateExport;
use App\Exports\ExamResultsExport;
use App\Imports\ExamQuestionsImport;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\Registration;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class ExamController extends Controller
{
    // --- ADMIN SIDE ---
    public function index()
    {
        $academicYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        $questions = ExamQuestion::query()
            ->where('academic_year', $academicYear)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.exams.index', compact('questions', 'academicYear'));
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:a,b,c,d',
            'score' => 'required|integer|min:1',
        ]);

        $validated['academic_year'] = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        ExamQuestion::create($validated);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function editQuestion(ExamQuestion $question)
    {
        return view('admin.exams.edit', compact('question'));
    }

    public function updateQuestion(Request $request, ExamQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:a,b,c,d',
            'score' => 'required|integer|min:1',
        ]);

        $question->update($validated);

        return redirect()->route('admin.exams.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function importQuestions(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        $academicYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        try {
            Excel::import(new ExamQuestionsImport($academicYear), $validated['file']);
        } catch (ExcelValidationException $e) {
            $messages = [];
            foreach ($e->failures() as $failure) {
                $messages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return redirect()->route('admin.exams.index')->withErrors($messages);
        }

        return redirect()->route('admin.exams.index')->with('success', 'Import soal berhasil.');
    }

    public function exportQuestions()
    {
        $academicYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        $safeYear = str_replace(['/', '\\'], '-', $academicYear);
        $filename = 'bank_soal_' . $safeYear . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ExamQuestionsExport($academicYear), $filename);
    }

    public function downloadQuestionsTemplate()
    {
        return Excel::download(new ExamQuestionsTemplateExport(), 'template_bank_soal.xlsx');
    }

    public function deleteQuestion(ExamQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    public function results()
    {
        $results = ExamResult::with(['registration'])->orderBy('created_at', 'desc')->get();
        return view('admin.exams.results', compact('results'));
    }

    public function topResults(Request $request)
    {
        $limit = (int) ($request->query('limit', 5));
        $limit = max(1, min(20, $limit));

        $top = ExamResult::query()
            ->with('registration')
            ->orderBy('score', 'desc')
            ->orderBy('correct_answers', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function (ExamResult $result) {
                return [
                    'id' => $result->id,
                    'application_id' => $result->registration->application_id ?? '-',
                    'name' => $result->registration->name ?? '-',
                    'angkatan' => $result->registration->angkatan ?? '-',
                    'total_questions' => $result->total_questions,
                    'correct_answers' => $result->correct_answers,
                    'score' => $result->score,
                    'created_at' => $result->created_at?->format('d M Y, H:i') ?? '-',
                ];
            });

        return response()->json([
            'data' => $top,
            'generated_at' => now()->format('d M Y, H:i:s'),
        ]);
    }

    public function exportResultsExcel()
    {
        $filename = "hasil_ujian_seleksi_" . date('Ymd_His') . ".xlsx";

        return Excel::download(new ExamResultsExport(), $filename);
    }

    public function exportResultsPdf()
    {
        $results = ExamResult::query()
            ->with('registration')
            ->orderBy('created_at', 'desc')
            ->get();

        $logoDataUri = null;
        $logoPath = public_path('images/axioo.png');
        if (is_file($logoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.exams.results_pdf', [
            'results' => $results,
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
            'appName' => config('app.name', 'Smart Axioo'),
        ])->setPaper('a4', 'portrait');

        $filename = "hasil_ujian_seleksi_" . date('Ymd_His') . ".pdf";

        return $pdf->download($filename);
    }

    public function exportResults()
    {
        $results = ExamResult::with(['registration'])->orderBy('created_at', 'desc')->get();

        $filename = "hasil_ujian_seleksi_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($results) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'ID Pendaftaran',
                'Nama Siswa',
                'Angkatan',
                'Tahun Akademik',
                'Total Soal',
                'Jawaban Benar',
                'Skor',
                'Tanggal Ujian'
            ]);

            foreach ($results as $index => $result) {
                fputcsv($handle, [
                    $index + 1,
                    $result->registration->application_id ?? '-',
                    $result->registration->name ?? '-',
                    $result->registration->angkatan ?? '-',
                    $result->registration->academic_year ?? '-',
                    $result->total_questions,
                    $result->correct_answers,
                    $result->score,
                    $result->created_at?->format('d/m/Y H:i') ?? '-'
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function deleteResult(ExamResult $result)
    {
        $result->delete();
        return back()->with('success', 'Nilai ujian berhasil dihapus.');
    }

    // --- STUDENT SIDE ---
    public function start($id)
    {
        $registration = Registration::where('application_id', $id)->firstOrFail();
        
        if ($registration->examResult) {
            return redirect()->route('registrations.track', ['id' => $id])->with('error', 'Anda sudah mengerjakan ujian.');
        }

        $academicYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        $questions = ExamQuestion::where('academic_year', $academicYear)->get();

        if ($questions->isEmpty()) {
            return redirect()->route('registrations.track', ['id' => $id])->with('error', 'Belum ada soal ujian yang tersedia.');
        }

        // Pass registration as applicant for the exam view
        return view('exams.take', [
            'registration' => $registration,
            'questions' => $questions,
            'applicant' => $registration,
        ]);
    }

    public function submit(Request $request, $id)
    {
        $registration = Registration::where('application_id', $id)->firstOrFail();
        
        if ($registration->examResult) {
            return response()->json(['success' => false, 'message' => 'Ujian sudah dikerjakan.']);
        }

        $answers = $request->input('answers');
        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $totalScore = 0;

        foreach ($answers as $questionId => $answer) {
            $question = ExamQuestion::find($questionId);
            if ($question && $question->correct_answer === $answer) {
                $correctAnswers++;
                $totalScore += $question->score;
            }
        }

        $startedAt = $request->input('started_at');
        if ($startedAt) {
            $startedAt = Carbon::parse($startedAt)->format('Y-m-d H:i:s');
        }

        ExamResult::create([
            'registration_id' => $registration->id,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score' => $totalScore,
            'started_at' => $startedAt,
            'finished_at' => now(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Selamat, Anda berhasil mengerjakan ujian.',
            'redirect' => route('registrations.track', ['id' => $id])
        ]);
    }
}
