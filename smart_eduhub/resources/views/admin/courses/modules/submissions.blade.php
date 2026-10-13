@extends('layouts.app')

@section('styles')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-pending   { background: #fef3c7; color: #b45309; }
    .status-submitted { background: #dbeafe; color: #1e40af; }
    .status-graded    { background: #d1fae5; color: #065f46; }
    .status-none      { background: #f1f5f9; color: #64748b; }

    .ai-badge-wrap {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.04em;
        transition: all .2s;
    }
    .ai-badge-human  { background: #dcfce7; color: #166534; border: 1.5px solid #86efac; }
    .ai-badge-mixed  { background: #fef9c3; color: #854d0e; border: 1.5px solid #fde047; }
    .ai-badge-ai     { background: #fee2e2; color: #991b1b; border: 1.5px solid #fca5a5; }
    .ai-badge-loading{ background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0; }

    .ai-scan-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 14px;
        border-radius: 10px;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: white;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        border: none;
        transition: all .2s;
        box-shadow: 0 2px 8px rgba(99,102,241,.3);
    }
    .ai-scan-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,.4); }
    .ai-scan-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    .ai-result-bar { height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; margin-top: 4px; }
    .ai-result-bar-fill { height: 100%; border-radius: 99px; transition: width .8s cubic-bezier(.4,0,.2,1); }

    .ai-detail-box {
        background: #0f172a;
        color: #94a3b8;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 11px;
        font-family: 'Fira Code', monospace;
        margin-top: 8px;
        border: 1px solid #1e293b;
        line-height: 1.8;
    }
    .ai-detail-box .ai-safe   { color: #34d399; font-weight: bold; }
    .ai-detail-box .ai-warn   { color: #fbbf24; font-weight: bold; }
    .ai-detail-box .ai-danger { color: #f87171; font-weight: bold; }

    @keyframes ai-spin { to { transform: rotate(360deg); } }
    .ai-spin { animation: ai-spin .8s linear infinite; display: inline-block; }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-orange-600 mb-3 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Kursus
            </a>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Hasil &amp; Penilaian Tugas Modul</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">
                <span class="text-slate-400">{{ $module->course->title }}</span>
                <span class="mx-1.5 text-slate-300">·</span>
                <span class="font-bold text-orange-600">{{ $module->title }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.modules.questions', $module) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-700 text-xs transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-orange-600">quiz</span>
                Kelola Soal Modul
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Siswa</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $students->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-black uppercase text-orange-500 tracking-wider">Sudah Mengirim</span>
            <p class="text-2xl font-black text-orange-600 mt-1">{{ $submissions->whereIn('status', ['submitted', 'graded'])->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-black uppercase text-amber-500 tracking-wider">Perlu Penilaian</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $submissions->where('status', 'submitted')->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-black uppercase text-emerald-500 tracking-wider">Selesai Dinilai</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $submissions->where('status', 'graded')->count() }}</p>
        </div>
    </div>

    {{-- AI Detection Banner --}}
    <div class="bg-gradient-to-r from-violet-50 to-orange-50 border border-violet-200 rounded-2xl p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-violet-500 text-xl mt-0.5 shrink-0">smart_toy</span>
        <div>
            <p class="text-xs font-black text-violet-900 mb-0.5">Fitur Deteksi Konten AI</p>
            <p class="text-[11px] text-violet-700 font-medium leading-relaxed">
                Pada setiap jawaban <strong>Essay</strong> dan <strong>Coding</strong>, klik tombol <strong>"Scan AI"</strong> untuk menganalisis apakah jawaban siswa menggunakan AI (ChatGPT, dsb). Untuk essay, sistem juga mencoba validasi dengan ZeroGPT API. Hasil bersifat indikatif — gunakan sebagai referensi, bukan keputusan mutlak.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider">Daftar Pengerjaan Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                        <th class="py-3.5 px-6">Siswa</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Waktu Kirim</th>
                        <th class="py-3.5 px-4 text-center">Nilai</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                    @forelse($students as $student)
                        @php $sub = $submissions->get($student->id); @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 font-black flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $student->nama_lengkap }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $student->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @if(!$sub)
                                    <span class="status-badge status-none"><span class="material-symbols-outlined text-xs">pending</span> Belum Mengerjakan</span>
                                @elseif($sub->status === 'draft')
                                    <span class="status-badge status-pending"><span class="material-symbols-outlined text-xs">edit_note</span> Draf Sementara</span>
                                @elseif($sub->status === 'submitted')
                                    <span class="status-badge status-submitted"><span class="material-symbols-outlined text-xs">mark_email_unread</span> Perlu Dinilai</span>
                                @elseif($sub->status === 'graded')
                                    <span class="status-badge status-graded"><span class="material-symbols-outlined text-xs">task_alt</span> Sudah Dinilai</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-500 font-medium">
                                {{ $sub ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($sub && $sub->score !== null)
                                    <span class="inline-block font-black text-sm text-emerald-600 bg-emerald-50 px-3 py-1 rounded-xl">{{ $sub->score }} / {{ $sub->max_score ?? 100 }}</span>
                                @else
                                    <span class="text-slate-300 font-bold">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($sub && in_array($sub->status, ['submitted', 'graded', 'draft']))
                                    <button type="button" onclick="openGradeModal({{ $student->id }})"
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold text-xs shadow-sm transition-all flex items-center gap-1.5 ml-auto">
                                        <span class="material-symbols-outlined text-sm">edit_square</span>
                                        Periksa &amp; Beri Nilai
                                    </button>
                                @else
                                    <span class="text-slate-300 text-[11px] italic">Belum ada berkas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODALS --}}
@foreach($students as $student)
    @php $sub = $submissions->get($student->id); @endphp
    @if($sub)
        @php
            $submittedAnswers = is_array(json_decode($sub->answer, true)) ? json_decode($sub->answer, true) : [];
            $submittedFiles   = is_array($sub->answer_files) ? $sub->answer_files : [];
            $questionScores   = is_array($sub->question_scores) ? $sub->question_scores : [];
        @endphp
        <div id="gradeModal-{{ $student->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto border border-slate-100">

                {{-- Header --}}
                <div class="p-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-orange-500/20 text-orange-400 font-black flex items-center justify-center text-base border border-orange-500/30">
                            {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-base leading-tight">{{ $student->nama_lengkap }}</h3>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $student->username }} · {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="scanAllAI({{ $student->id }})" id="scanAllBtn-{{ $student->id }}" class="ai-scan-btn">
                            <span class="material-symbols-outlined text-sm">smart_toy</span>
                            Scan Semua AI
                        </button>
                        <button type="button" onclick="toggleFullscreen({{ $student->id }})" id="fullscreenBtn-{{ $student->id }}" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all" title="Layar Penuh">
                            <span class="material-symbols-outlined text-lg" id="fullscreenIcon-{{ $student->id }}">fullscreen</span>
                        </button>
                        <form action="{{ route('admin.courses.modules.submissions.destroy', $sub) }}" method="POST" onsubmit="return confirm('Hapus jawaban siswa ini? Nilai, feedback, dan file upload akan ikut dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-full bg-rose-900/70 hover:bg-rose-700 text-rose-200 hover:text-white flex items-center justify-center transition-all" title="Hapus Jawaban Siswa">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                        <button type="button" onclick="closeGradeModal({{ $student->id }})" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all" title="Tutup">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <form action="{{ route('admin.courses.modules.grade.store', $sub->id) }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-slate-50/50">

                        @if($module->questions->isNotEmpty())
                            @foreach($module->questions as $index => $q)
                                @php
                                    $studentAns  = $submittedAnswers[$q->id] ?? null;
                                    $studentFile = $submittedFiles[$q->id] ?? null;
                                    $currentScore = $questionScores[$q->id] ?? ($q->type === 'multiple_choice' ? ($studentAns === $q->correct_answer ? $q->score : 0) : 0);
                                    $aiScannable  = in_array($q->type, ['essay', 'coding']) && !empty($studentAns);
                                @endphp
                                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-3">

                                    {{-- Question Header --}}
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 flex-wrap gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 font-black text-xs flex items-center justify-center">{{ $index + 1 }}</span>
                                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md
                                                {{ $q->type === 'multiple_choice' ? 'bg-purple-100 text-purple-700' : ($q->type === 'essay' ? 'bg-amber-100 text-amber-700' : ($q->type === 'coding' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700')) }}">
                                                {{ ['multiple_choice'=>'Pilihan Ganda','essay'=>'Essay','coding'=>'Coding','file'=>'Upload PDF'][$q->type] ?? $q->type }}
                                            </span>
                                            @if($aiScannable)
                                                <div id="ai-badge-{{ $sub->id }}-{{ $q->id }}" class="ai-badge-wrap ai-badge-loading hidden">
                                                    <span class="material-symbols-outlined text-xs">smart_toy</span>
                                                    <span>Belum Discan</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($aiScannable)
                                                <button type="button"
                                                    id="scanBtn-{{ $sub->id }}-{{ $q->id }}"
                                                    onclick="scanSingleAI('{{ $sub->id }}','{{ $q->id }}','{{ $q->type }}',{{ json_encode($studentAns) }})"
                                                    class="ai-scan-btn" style="padding:3px 10px;font-size:10px;">
                                                    <span class="material-symbols-outlined text-xs">smart_toy</span> Scan AI
                                                </button>
                                            @endif
                                            <span class="text-xs font-bold text-slate-400">Maks: <strong class="text-orange-600">{{ $q->score }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="text-xs font-bold text-slate-800">{{ $q->question }}</div>

                                    {{-- Answer display --}}
                                    @if($q->type === 'multiple_choice')
                                        <div class="p-3 bg-slate-50 rounded-xl space-y-1 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-500">Jawaban Siswa:</span>
                                                <span class="font-black uppercase {{ $studentAns === $q->correct_answer ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $studentAns ? strtoupper($studentAns).' ('.(($q->{'option_'.$studentAns}) ?? '-').')' : 'Tidak Dijawab' }}
                                                </span>
                                                <span class="material-symbols-outlined text-sm {{ $studentAns === $q->correct_answer ? 'text-emerald-500' : 'text-rose-500' }}">
                                                    {{ $studentAns === $q->correct_answer ? 'check_circle' : 'cancel' }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-slate-400">Kunci: <strong class="uppercase text-slate-600">{{ strtoupper($q->correct_answer) }} ({{ $q->{'option_'.$q->correct_answer} }})</strong></div>
                                        </div>

                                    @elseif($q->type === 'essay')
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Jawaban Essay Siswa:</span>
                                            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 whitespace-pre-line leading-relaxed">{{ $studentAns ?: 'Belum diisi.' }}</div>
                                            <div id="ai-detail-{{ $sub->id }}-{{ $q->id }}" class="ai-detail-box hidden"></div>
                                        </div>

                                    @elseif($q->type === 'coding')
                                        <div id="admin-coding-box-{{ $sub->id }}-{{ $q->id }}" class="space-y-3 p-3.5 bg-slate-900/5 rounded-2xl border border-slate-200/80 transition-all">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-sm text-emerald-500">terminal</span>
                                                    <span>Kode &amp; Live Preview Siswa</span>
                                                </span>
                                                <button type="button" onclick="toggleAdminCodingFullscreen('admin-coding-box-{{ $sub->id }}-{{ $q->id }}')"
                                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-orange-600 hover:text-orange-700 bg-white hover:bg-orange-50 px-3 py-1 rounded-xl transition-all border border-orange-100 shadow-sm">
                                                    <span class="material-symbols-outlined text-sm admin-coding-icon">fullscreen</span>
                                                    <span class="admin-coding-text">Layar Penuh Coding</span>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 admin-coding-grid flex-1">
                                                <div class="space-y-1">
                                                    <span class="text-[10px] text-slate-400 font-bold">Source Code:</span>
                                                    <textarea readonly rows="8" class="w-full bg-slate-900 text-emerald-400 font-mono text-xs p-3 rounded-xl border border-slate-800 leading-relaxed outline-none resize-y">{{ $studentAns }}</textarea>
                                                </div>
                                                <div class="space-y-1 flex flex-col">
                                                    <span class="text-[10px] text-slate-400 font-bold">Live Output:</span>
                                                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white flex-1 min-h-[160px]">
                                                        <iframe id="admin-grade-preview-{{ $sub->id }}-{{ $q->id }}" class="w-full h-full border-0 bg-white" title="Student Code Preview"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="ai-detail-{{ $sub->id }}-{{ $q->id }}" class="ai-detail-box hidden"></div>
                                        </div>

                                    @elseif($q->type === 'file')
                                        <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-rose-500">picture_as_pdf</span>
                                                <span class="text-xs font-bold text-rose-900">Berkas PDF / Gambar Siswa</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($studentFile)
                                                    <a href="{{ Storage::url($studentFile) }}" target="_blank" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700 transition-all inline-flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-xs">download</span> Buka Berkas
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Score input --}}
                                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                                        <label class="text-xs font-bold text-slate-600 flex items-center gap-2">
                                            Skor:
                                            <input type="number" name="question_scores[{{ $q->id }}]" min="0" max="{{ $q->score }}" value="{{ old("question_scores.{$q->id}", $currentScore) }}" required
                                                class="w-20 rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-black text-center focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none">
                                            <span class="text-xs text-slate-400">/ {{ $q->score }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-3">
                                <h4 class="text-xs font-black text-slate-800 uppercase">Jawaban Siswa:</h4>
                                <div class="p-4 bg-slate-50 rounded-xl text-xs font-medium text-slate-800 whitespace-pre-line">{{ $sub->answer ?: '-' }}</div>
                                @if($sub->file_path)
                                    <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-between flex-wrap gap-2">
                                        <span class="text-xs font-bold text-rose-900 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">picture_as_pdf</span> Lampiran Berkas</span>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="px-3 py-1.5 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 inline-flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">download</span> Buka
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-2">
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-600">Feedback Instruktur</label>
                            <textarea name="feedback" rows="3" placeholder="Tulis catatan untuk siswa..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none resize-none">{{ old('feedback', $sub->feedback) }}</textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-white border-t border-slate-100 flex items-center justify-between shrink-0">
                        <button type="button" onclick="closeGradeModal({{ $student->id }})" class="px-5 py-2.5 rounded-xl border border-slate-200 font-bold text-xs text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-xs shadow-lg shadow-orange-500/20 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">save</span> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
// ── Modal ─────────────────────────────────────────────────────────────
function openGradeModal(studentId) {
    const modal = document.getElementById('gradeModal-' + studentId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    @if(isset($module) && $module->questions)
        @foreach($module->questions as $q)
            @if($q->type === 'coding')
                @foreach($students as $st)
                    @php $subItem = $submissions->get($st->id); @endphp
                    @if($subItem)
                        @php
                            $subAnsArray = is_array(json_decode($subItem->answer, true)) ? json_decode($subItem->answer, true) : [];
                            $codeContent = $subAnsArray[$q->id] ?? '';
                        @endphp
                        if (studentId === {{ $st->id }}) {
                            renderAdminGradePreview('admin-grade-preview-{{ $subItem->id }}-{{ $q->id }}', {!! json_encode($codeContent) !!});
                        }
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
}
function closeGradeModal(studentId) {
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => {});
    }
    const modal = document.getElementById('gradeModal-' + studentId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function toggleFullscreen(studentId) {
    const modal = document.getElementById('gradeModal-' + studentId);
    if (!modal) return;
    const inner = modal.querySelector('.rounded-3xl, .rounded-none') || modal.firstElementChild;
    const icon  = document.getElementById('fullscreenIcon-' + studentId);

    if (!document.fullscreenElement) {
        if (modal.requestFullscreen) {
            modal.requestFullscreen().then(() => {
                if (inner) {
                    inner.classList.remove('max-h-[90vh]', 'rounded-3xl', 'my-auto', 'max-w-4xl');
                    inner.classList.add('h-full', 'w-full', 'rounded-none', 'max-w-none', 'max-h-none');
                }
                if (icon) icon.textContent = 'fullscreen_exit';
            }).catch(() => {
                toggleCssFullscreen(inner, icon);
            });
        } else {
            toggleCssFullscreen(inner, icon);
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen().then(() => {
                if (inner) {
                    inner.classList.add('max-h-[90vh]', 'rounded-3xl', 'my-auto', 'max-w-4xl');
                    inner.classList.remove('h-full', 'w-full', 'rounded-none', 'max-w-none', 'max-h-none');
                }
                if (icon) icon.textContent = 'fullscreen';
            }).catch(() => {});
        }
    }
}

function toggleCssFullscreen(inner, icon) {
    if (!inner) return;
    const isFull = inner.classList.contains('max-w-none');
    if (!isFull) {
        inner.classList.remove('max-h-[90vh]', 'rounded-3xl', 'my-auto', 'max-w-4xl');
        inner.classList.add('h-full', 'w-full', 'rounded-none', 'max-w-none', 'max-h-none');
        if (icon) icon.textContent = 'fullscreen_exit';
    } else {
        inner.classList.add('max-h-[90vh]', 'rounded-3xl', 'my-auto', 'max-w-4xl');
        inner.classList.remove('h-full', 'w-full', 'rounded-none', 'max-w-none', 'max-h-none');
        if (icon) icon.textContent = 'fullscreen';
    }
}

document.addEventListener('fullscreenchange', function() {
    if (!document.fullscreenElement) {
        document.querySelectorAll('[id^="gradeModal-"]').forEach(modal => {
            const inner = modal.querySelector('.rounded-none, .max-w-none');
            if (inner) {
                inner.classList.add('max-h-[90vh]', 'rounded-3xl', 'my-auto', 'max-w-4xl');
                inner.classList.remove('h-full', 'w-full', 'rounded-none', 'max-w-none', 'max-h-none');
            }
            const icons = modal.querySelectorAll('[id^="fullscreenIcon-"]');
            icons.forEach(i => i.textContent = 'fullscreen');
        });
    }
});
function renderAdminGradePreview(iframeId, code) {
    const iframe = document.getElementById(iframeId);
    if (!iframe) return;
    const doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write(code || '<div style="color:#94a3b8;font-family:sans-serif;font-size:12px;padding:12px;">Siswa tidak mengisi kode.</div>');
    doc.close();
}

// ── AI Heuristic Detector ─────────────────────────────────────────────
function heuristicAIDetect(text, type) {
    if (!text || text.trim().length < 20)
        return { score: 0, label: 'Teks Terlalu Pendek', signals: ['Teks tidak cukup untuk dianalisis.'], color: 'mixed' };

    const signals = [];
    let score = 0;

    if (type === 'essay') {
        const sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 5);
        if (sentences.length >= 3) {
            const lengths = sentences.map(s => s.trim().split(/\s+/).length);
            const avg = lengths.reduce((a,b)=>a+b,0)/lengths.length;
            const variance = lengths.reduce((a,b)=>a+Math.pow(b-avg,2),0)/lengths.length;
            if (variance < 12) { score+=25; signals.push('\u26A0 Variasi panjang kalimat rendah (variance: '+variance.toFixed(1)+') — AI cenderung seragam'); }
            else { signals.push('\u2713 Variasi kalimat natural (variance: '+variance.toFixed(1)+')'); }
        }
        const aiPhrases = ['pertama-tama','selain itu','dengan demikian','oleh karena itu','sebagai kesimpulan',
            'dapat disimpulkan','secara keseluruhan','namun demikian','pada dasarnya','penting untuk dicatat',
            'in conclusion','furthermore','it is important to note','in addition','however','moreover',
            'therefore','in summary','to summarize','as a result','consequently','on the other hand',
            'first of all','last but not least'];
        const lowerText = text.toLowerCase();
        const foundPhrases = aiPhrases.filter(p=>lowerText.includes(p));
        if (foundPhrases.length >= 3) { score+=30; signals.push('\u26A0 Frasa transisi AI dominan ('+foundPhrases.length+'x): '+foundPhrases.slice(0,3).join(', ')); }
        else if (foundPhrases.length >= 1) { score+=10; signals.push('\u007E Beberapa frasa umum: '+foundPhrases.join(', ')); }
        else { signals.push('\u2713 Tidak ada frasa transisi AI yang dominan'); }

        const words = lowerText.match(/\b[a-z]{4,}\b/g)||[];
        if (words.length > 20) {
            const diversity = new Set(words).size / words.length;
            if (diversity < 0.45) { score+=15; signals.push('\u26A0 Keragaman kosakata rendah ('+(diversity*100).toFixed(0)+'%) — repetisi AI'); }
            else { signals.push('\u2713 Keragaman kosakata baik ('+(diversity*100).toFixed(0)+'%)'); }
        }
        const informalMarkers = /\b(gue|gw|aku|kamu|elo|lo|bro|sis|wkwk|haha|lol|btw|yg|dgn|jd|krn|dll)\b/i;
        if (!informalMarkers.test(text) && text.length > 100) { score+=10; signals.push('\u007E Tidak ada penanda informal — gaya sangat formal (bisa AI)'); }
        else { signals.push('\u2713 Ditemukan penanda bahasa santai — lebih natural'); }

        const paragraphs = text.split(/\n{2,}/).filter(p=>p.trim().length>10);
        if (paragraphs.length >= 3) {
            const pLens = paragraphs.map(p=>p.trim().length);
            const pAvg = pLens.reduce((a,b)=>a+b,0)/pLens.length;
            const pVar = pLens.reduce((a,b)=>a+Math.pow(b-pAvg,2),0)/pLens.length;
            if (pVar < 500) { score+=10; signals.push('\u26A0 Panjang paragraf sangat seragam — pola khas AI'); }
        }

    } else if (type === 'coding') {
        const lowerCode = text.toLowerCase();
        const aiComments = ['// step','// this code','// we use','// here we','// function to',
            '// note:','// first','// second','// finally','// now we','// below'];
        const foundComments = aiComments.filter(c=>lowerCode.includes(c));
        if (foundComments.length >= 2) { score+=30; signals.push('\u26A0 Komentar kode berformat AI ('+foundComments.length+'x): '+foundComments.slice(0,2).join(', ')); }
        else if (foundComments.length === 1) { score+=10; signals.push('\u007E Satu komentar berformat AI ditemukan: '+foundComments[0]); }
        else { signals.push('\u2713 Tidak ada komentar berformat AI'); }

        const lines = text.split('\n').filter(l=>l.trim().length>0);
        const indented = lines.filter(l=>/^(\s{2}|\s{4}|\t)/.test(l));
        if (lines.length > 5 && indented.length/lines.length > 0.6) {
            const indentSizes = indented.map(l=>(l.match(/^(\s+)/)||['',''])[1].length);
            if (indentSizes.every(s=>s%2===0||s%4===0)) { score+=20; signals.push('\u26A0 Indentasi kode sempurna dan konsisten — pola AI generator'); }
        }
        const boilerplates = ['<!doctype html>','<!DOCTYPE html>','<meta charset','<meta name="viewport"'];
        const foundBoiler = boilerplates.filter(b=>text.includes(b));
        if (foundBoiler.length >= 3) { score+=15; signals.push('\u007E Struktur HTML boilerplate lengkap — umum dari AI'); }

        const commentCount = (text.match(/\/\/|\/\*|<!--|#\s/g)||[]).length;
        if (lines.length > 10 && commentCount/lines.length > 0.3) { score+=15; signals.push('\u26A0 Rasio komentar tinggi ('+Math.round(commentCount/lines.length*100)+'%) — tipikal kode AI'); }

        const aiNaming = /\b(myFunction|handleClick|myVariable|exampleFunction|mainFunction|doSomething|sampleData|yourName|yourEmail|placeholder)\b/;
        if (aiNaming.test(text)) { score+=20; signals.push('\u26A0 Nama variabel/fungsi generik ala AI: myFunction, handleClick, dll'); }
        else { signals.push('\u2713 Tidak ada nama variabel generik AI yang jelas'); }
    }

    score = Math.min(100, score);
    let label, color;
    if (score >= 65)      { label='Kemungkinan AI';             color='ai'; }
    else if (score >= 30) { label='Perlu Dicek Lebih Lanjut';   color='mixed'; }
    else                  { label='Kemungkinan Asli (Human)';   color='human'; }
    return { score, label, signals, color };
}

// ── ZeroGPT API (essay only) ──────────────────────────────────────────
async function callZeroGPT(text) {
    try {
        const res = await fetch('https://api.zerogpt.com/api/detect/detectText', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'ApiKey': 'free' },
            body: JSON.stringify({ input_text: text }),
            signal: AbortSignal.timeout(8000)
        });
        if (!res.ok) return null;
        const data = await res.json();
        return (data && typeof data.fakePercentage === 'number') ? data : null;
    } catch { return null; }
}

// ── Render helpers ────────────────────────────────────────────────────
function renderAIBadge(subId, qId, result) {
    const badge = document.getElementById('ai-badge-'+subId+'-'+qId);
    if (!badge) return;
    badge.className = 'ai-badge-wrap ai-badge-'+result.color;
    const icon = result.color==='ai' ? 'smart_toy' : (result.color==='human' ? 'verified_user' : 'help');
    badge.innerHTML = '<span class="material-symbols-outlined text-xs">'+icon+'</span><span>'+result.label+' ('+result.score+'%)</span>';
}
function renderAIDetail(subId, qId, result, apiData) {
    const box = document.getElementById('ai-detail-'+subId+'-'+qId);
    if (!box) return;
    box.classList.remove('hidden');
    const barColor = result.color==='ai' ? '#f87171' : (result.color==='human' ? '#34d399' : '#fbbf24');
    let html = '<div style="margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">'
        +'<span style="color:#e2e8f0;font-weight:800;font-size:12px;">&#x1F916; Laporan Deteksi AI</span>'
        +'<span style="color:'+barColor+';font-weight:900;">'+result.score+'% AI Probability</span></div>'
        +'<div class="ai-result-bar"><div class="ai-result-bar-fill" style="width:'+result.score+'%;background:'+barColor+';"></div></div>'
        +'<div style="margin-top:10px;margin-bottom:4px;color:#94a3b8;font-size:10px;text-transform:uppercase;">Sinyal yang Terdeteksi:</div>';
    result.signals.forEach(function(s) {
        const cls = s.startsWith('\u26A0') ? 'ai-danger' : (s.startsWith('\u2713') ? 'ai-safe' : 'ai-warn');
        html += '<div class="'+cls+'">'+s+'</div>';
    });
    if (apiData) {
        html += '<div style="margin-top:8px;padding-top:8px;border-top:1px solid #1e293b;color:#64748b;font-size:10px;">'
            +'<span style="color:#7c3aed;font-weight:800;">ZeroGPT API:</span> '
            +'AI Score: <span class="'+(apiData.fakePercentage>50?'ai-danger':'ai-safe')+'">'+apiData.fakePercentage.toFixed(1)+'%</span>'
            +(apiData.textWords ? ' &middot; Words: '+apiData.textWords : '')
            +'</div>';
    }
    html += '<div style="margin-top:6px;color:#475569;font-size:10px;font-style:italic;">* Bersifat indikatif. Gunakan sebagai referensi, bukan keputusan mutlak.</div>';
    box.innerHTML = html;
}

// ── Scan functions ────────────────────────────────────────────────────
async function scanSingleAI(subId, qId, type, text) {
    const btn   = document.getElementById('scanBtn-'+subId+'-'+qId);
    const badge = document.getElementById('ai-badge-'+subId+'-'+qId);
    if (btn)   { btn.disabled=true; btn.innerHTML='<span class="material-symbols-outlined text-xs ai-spin">sync</span> Scanning...'; }
    if (badge) { badge.className='ai-badge-wrap ai-badge-loading'; badge.classList.remove('hidden'); badge.innerHTML='<span class="material-symbols-outlined text-xs ai-spin">sync</span><span>Menganalisis...</span>'; }

    const hResult = heuristicAIDetect(text, type);
    let apiData = null;
    if (type === 'essay' && text && text.length > 100) {
        apiData = await callZeroGPT(text);
        if (apiData && typeof apiData.fakePercentage === 'number') {
            const blended = Math.round(hResult.score*0.4 + apiData.fakePercentage*0.6);
            hResult.score = blended;
            hResult.color = blended>=65?'ai':(blended>=30?'mixed':'human');
            hResult.label = blended>=65?'Kemungkinan AI':(blended>=30?'Perlu Dicek Lebih Lanjut':'Kemungkinan Asli');
        }
    }
    renderAIBadge(subId, qId, hResult);
    renderAIDetail(subId, qId, hResult, apiData);
    if (btn) { btn.disabled=false; btn.innerHTML='<span class="material-symbols-outlined text-xs">smart_toy</span> Re-Scan'; }
}

async function scanAllAI(studentId) {
    const btn = document.getElementById('scanAllBtn-'+studentId);
    if (btn) { btn.disabled=true; btn.innerHTML='<span class="material-symbols-outlined text-sm ai-spin">sync</span> Scanning...'; }
    @foreach($students as $student)
        @php $sub = $submissions->get($student->id); @endphp
        @if($sub)
            @foreach($module->questions as $q)
                @if(in_array($q->type, ['essay','coding']))
                    @php
                        $subAnsArr = is_array(json_decode($sub->answer, true)) ? json_decode($sub->answer, true) : [];
                        $ans = $subAnsArr[$q->id] ?? null;
                    @endphp
                    @if($ans)
                        if (studentId === {{ $student->id }}) {
                            await scanSingleAI('{{ $sub->id }}','{{ $q->id }}','{{ $q->type }}',{!! json_encode($ans) !!});
                        }
                    @endif
                @endif
            @endforeach
        @endif
    @endforeach
    if (btn) { btn.disabled=false; btn.innerHTML='<span class="material-symbols-outlined text-sm">smart_toy</span> Scan Semua AI'; }
}

function toggleAdminCodingFullscreen(boxId) {
    const box = document.getElementById(boxId);
    if (!box) return;
    const btnText = box.querySelector('.admin-coding-text');
    const btnIcon = box.querySelector('.admin-coding-icon');
    const grid    = box.querySelector('.admin-coding-grid');

    if (box.classList.contains('is-coding-fullscreen')) {
        box.classList.remove('is-coding-fullscreen', 'fixed', 'inset-0', 'z-[200]', 'bg-slate-950', 'p-6', 'flex', 'flex-col', 'h-screen', 'w-screen', 'overflow-hidden');
        if (btnText) btnText.textContent = 'Layar Penuh Coding';
        if (btnIcon) btnIcon.textContent = 'fullscreen';
        if (grid) grid.classList.remove('h-[calc(100vh-100px)]');
        document.body.style.overflow = '';
    } else {
        box.classList.add('is-coding-fullscreen', 'fixed', 'inset-0', 'z-[200]', 'bg-slate-950', 'p-6', 'flex', 'flex-col', 'h-screen', 'w-screen', 'overflow-hidden');
        if (btnText) btnText.textContent = 'Keluar Layar Penuh';
        if (btnIcon) btnIcon.textContent = 'fullscreen_exit';
        if (grid) grid.classList.add('h-[calc(100vh-100px)]');
        document.body.style.overflow = 'hidden';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.is-coding-fullscreen').forEach(box => {
            box.classList.remove('is-coding-fullscreen', 'fixed', 'inset-0', 'z-[200]', 'bg-slate-950', 'p-6', 'flex', 'flex-col', 'h-screen', 'w-screen', 'overflow-hidden');
            const btnText = box.querySelector('.admin-coding-text');
            const btnIcon = box.querySelector('.admin-coding-icon');
            const grid    = box.querySelector('.admin-coding-grid');
            if (btnText) btnText.textContent = 'Layar Penuh Coding';
            if (btnIcon) btnIcon.textContent = 'fullscreen';
            if (grid) grid.classList.remove('h-[calc(100vh-100px)]');
            document.body.style.overflow = '';
        });
    }
});

</script>
@endpush
