@extends('layouts.app')

@section('styles')
<style>
    .question-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .type-multiple { background: #ede9fe; color: #7c3aed; }
    .type-essay    { background: #fef3c7; color: #b45309; }
    .type-coding   { background: #d1fae5; color: #065f46; }
    .type-file     { background: #fee2e2; color: #b91c1c; }

    .option-label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .option-label:hover { border-color: #6366f1; background: #f5f3ff; }
    .option-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #6366f1;
        font-size: 11px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .q-card {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .q-card:hover { box-shadow: 0 4px 16px rgba(99,102,241,0.07); }

    .q-card-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .code-textarea {
        background: #0f172a !important;
        color: #86efac !important;
        font-family: 'Fira Code', 'Cascadia Code', monospace !important;
        font-size: 13px !important;
        border-color: #1e293b !important;
    }
    .code-textarea::placeholder { color: #475569 !important; }

    .add-form-card {
        background: white;
        border-radius: 24px;
        border: 2px dashed #c7d2fe;
        padding: 28px;
        transition: border-color 0.2s;
    }
    .add-form-card:focus-within {
        border-color: #6366f1;
        border-style: solid;
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @include('admin.partials.page-header', [
        'title' => 'Kelola Soal Modul',
        'eyebrow' => 'E-Learning',
        'subtitle' => $module->course->title . ' · ' . $module->title . ' · ' . $module->questions->count() . ' soal',
        'icon' => 'quiz',
    ])

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Kursus
        </a>
        <a href="{{ route('admin.courses.modules.submissions', $module) }}" class="px-4 py-2.5 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md shadow-orange-500/20 transition-all flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">assignment_turned_in</span>
            <span>Lihat Jawaban Siswa & Penilaian</span>
        </a>
        <span class="question-type-badge {{ $module->type === 'quiz' ? 'type-multiple' : ($module->type === 'coding' ? 'type-coding' : 'type-essay') }}">
            <span class="material-symbols-outlined" style="font-size:13px">{{ $module->type === 'coding' ? 'code' : ($module->type === 'quiz' ? 'quiz' : 'assignment') }}</span>
            {{ ['assignment' => 'Tugas', 'quiz' => 'Kuis', 'coding' => 'Coding'][$module->type] ?? $module->type }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-5 py-3.5 rounded-2xl text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-700 px-5 py-3.5 rounded-2xl text-sm font-bold">
            <div class="flex items-center gap-2 mb-1"><span class="material-symbols-outlined text-rose-500">error</span> Terjadi kesalahan:</div>
            <ul class="list-disc ml-8 text-xs font-medium space-y-0.5 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- =============================== --}}
    {{-- FORM TAMBAH SOAL BARU           --}}
    {{-- =============================== --}}
    <div class="add-form-card" id="add-question-form-wrap">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">add_circle</span>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">Tambah Soal Baru</h3>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Pilih tipe soal lalu isi detail pertanyaan</p>
            </div>
        </div>

        <form action="{{ route('admin.courses.modules.questions.store', $module) }}" method="POST" id="add-question-form" class="space-y-5">
            @csrf

            {{-- Tipe Soal --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipe Soal</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="new-type-selector">
                    @foreach([
                        ['value' => 'multiple_choice', 'label' => 'Pilihan Ganda', 'icon' => 'checklist', 'class' => 'type-multiple'],
                        ['value' => 'essay',           'label' => 'Essay',          'icon' => 'edit_note',  'class' => 'type-essay'],
                        ['value' => 'coding',          'label' => 'Coding',         'icon' => 'code',       'class' => 'type-coding'],
                        ['value' => 'file',            'label' => 'Upload PDF',     'icon' => 'upload_file','class' => 'type-file'],
                    ] as $typeOpt)
                    <label class="type-option-label cursor-pointer" data-type="{{ $typeOpt['value'] }}">
                        <input type="radio" name="type" value="{{ $typeOpt['value'] }}" class="sr-only" {{ $typeOpt['value'] === 'multiple_choice' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-2xl border-2 border-slate-100 hover:border-orange-200 transition-all text-center h-full selected:border-orange-500 type-opt-card {{ $typeOpt['value'] === 'multiple_choice' ? 'border-orange-500 bg-orange-50/50' : '' }}">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $typeOpt['value'] === 'multiple_choice' ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400' }} transition-all type-opt-icon">
                                <span class="material-symbols-outlined text-base">{{ $typeOpt['icon'] }}</span>
                            </div>
                            <span class="text-[10px] font-black text-slate-600 leading-tight">{{ $typeOpt['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Pertanyaan / Soal --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pertanyaan / Soal</label>
                <textarea name="question" id="new-question" required rows="3"
                    placeholder="Tulis pertanyaan atau soal di sini..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none resize-none"></textarea>
            </div>

            {{-- Pilihan Ganda Options --}}
            <div id="new-options-block" class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilihan Jawaban</label>
                <div class="grid md:grid-cols-2 gap-3">
                    @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                    <div class="flex items-center gap-2">
                        <div class="option-badge shrink-0">{{ $label }}</div>
                        <input name="option_{{ $key }}" id="new-option-{{ $key }}"
                            placeholder="Pilihan {{ $label }}..."
                            class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        Kunci Jawaban:
                        <select name="correct_answer" id="new-correct-answer" class="ml-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-bold focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            {{-- Essay Info Block --}}
            <div id="new-essay-block" class="hidden">
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-500 mt-0.5">edit_note</span>
                    <div>
                        <p class="text-xs font-bold text-amber-800">Mode Essay</p>
                        <p class="text-[11px] text-amber-700 font-medium mt-0.5">Siswa akan menjawab dengan teks bebas. Jawaban akan diperiksa manual oleh instruktur.</p>
                    </div>
                </div>
            </div>

            {{-- Coding Info Block --}}
            <div id="new-coding-block" class="hidden space-y-3">
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-600 mt-0.5">code</span>
                    <div>
                        <p class="text-xs font-bold text-emerald-800">Mode Coding (Live Preview Aktif)</p>
                        <p class="text-[11px] text-emerald-700 font-medium mt-0.5">Siswa akan mendapatkan Live Preview secara real-time saat mengetik kode HTML/CSS/JS. Anda juga dapat menguji kode pada Sandbox di bawah ini.</p>
                    </div>
                </div>

                {{-- Live Sandbox Tester Admin --}}
                <div id="admin-sandbox-box" class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-900 shadow-sm transition-all duration-300">
                    <div class="px-4 py-2 bg-slate-800 border-b border-slate-700 flex items-center justify-between text-xs text-slate-300 font-bold">
                        <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-emerald-400">terminal</span> Live Code Sandbox (Uji Coba Admin)</span>
                        <button type="button" onclick="toggleAdminFullScreen('admin-sandbox-box')" class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-300 hover:text-white bg-slate-700 hover:bg-slate-600 px-2.5 py-1 rounded-lg transition-all border border-slate-600">
                            <span class="material-symbols-outlined text-sm fullscreen-icon">fullscreen</span>
                            <span class="fullscreen-text">Layar Penuh</span>
                        </button>
                    </div>
                    <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-800 admin-editor-grid flex-1">
                        <div class="p-3 bg-slate-950 flex flex-col">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Input Kode Test</label>
                            <textarea id="admin-sandbox-input" oninput="updateAdminPreview()" placeholder="<h1>Halo Axioo</h1>&#10;<style>h1 { color: #10b981; font-family: sans-serif; }</style>" class="w-full flex-1 min-h-[140px] bg-transparent text-emerald-400 font-mono text-xs outline-none resize-none placeholder:text-slate-600"></textarea>
                        </div>
                        <div class="p-3 bg-white flex flex-col">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Live Output Preview</label>
                            <iframe id="admin-sandbox-iframe" class="w-full flex-1 min-h-[140px] border border-slate-200 rounded-lg bg-white"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            {{-- File Upload Info Block --}}
            <div id="new-file-block" class="hidden">
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-500 mt-0.5">upload_file</span>
                    <div>
                        <p class="text-xs font-bold text-rose-800">Mode Upload File PDF</p>
                        <p class="text-[11px] text-rose-700 font-medium mt-0.5">Siswa akan mengupload file PDF sebagai jawaban. Maksimal 20 MB per file.</p>
                    </div>
                </div>
            </div>

            {{-- Skor --}}
            <div class="flex items-center justify-between pt-1">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-slate-400">star</span>
                    Skor / Poin:
                    <input name="score" type="number" min="1" max="100" value="10" required
                        class="ml-2 w-20 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-bold text-center focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                </label>
                <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 hover:bg-orange-700 hover:shadow-orange-500/30 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Soal
                </button>
            </div>
        </form>
    </div>

    {{-- =============================== --}}
    {{-- DAFTAR SOAL                     --}}
    {{-- =============================== --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                Daftar Soal ({{ $module->questions->count() }})
            </h3>
            @if($module->questions->isNotEmpty())
            <div class="text-[11px] font-bold text-slate-400">
                Total skor: <span class="text-orange-600 font-black">{{ $module->questions->sum('score') }}</span>
            </div>
            @endif
        </div>

        @forelse($module->questions as $number => $question)
        <div class="q-card" id="q-card-{{ $question->id }}">
            {{-- Card Header --}}
            <div class="q-card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-sm">
                        {{ $number + 1 }}
                    </div>
                    <div>
                        <span class="question-type-badge
                            {{ $question->type === 'multiple_choice' ? 'type-multiple' : ($question->type === 'essay' ? 'type-essay' : ($question->type === 'coding' ? 'type-coding' : 'type-file')) }}">
                            <span class="material-symbols-outlined" style="font-size:12px">
                                {{ $question->type === 'multiple_choice' ? 'checklist' : ($question->type === 'essay' ? 'edit_note' : ($question->type === 'coding' ? 'code' : 'upload_file')) }}
                            </span>
                            {{ ['multiple_choice' => 'Pilihan Ganda', 'essay' => 'Essay', 'coding' => 'Coding', 'file' => 'Upload PDF'][$question->type] ?? $question->type }}
                        </span>
                        <span class="ml-2 text-[10px] font-bold text-slate-400">Skor: {{ $question->score }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleEditForm({{ $question->id }})"
                        class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-[11px] font-bold hover:bg-amber-100 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                    </button>
                    <form action="{{ route('admin.courses.modules.questions.destroy', $question) }}" method="POST"
                        onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-500 text-[11px] font-bold hover:bg-rose-100 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Question Preview --}}
            <div class="p-5 border-b border-slate-50">
                <p class="text-sm font-semibold text-slate-700 leading-relaxed">{{ $question->question }}</p>
                @if($question->type === 'multiple_choice')
                <div class="grid sm:grid-cols-2 gap-2 mt-4">
                    @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl {{ $question->correct_answer === $key ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                        <div class="w-6 h-6 rounded-lg {{ $question->correct_answer === $key ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} text-[11px] font-black flex items-center justify-center flex-shrink-0">
                            {{ $label }}
                        </div>
                        <span class="text-xs font-medium {{ $question->correct_answer === $key ? 'text-emerald-700 font-bold' : 'text-slate-600' }}">
                            {{ $question->{'option_'.$key} ?? '-' }}
                        </span>
                        @if($question->correct_answer === $key)
                            <span class="ml-auto material-symbols-outlined text-sm text-emerald-500" style="font-variation-settings:'FILL' 1">check_circle</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @elseif($question->type === 'coding')
                <div class="mt-4 border border-slate-200 rounded-2xl overflow-hidden bg-slate-900">
                    <div class="px-4 py-2 bg-slate-800 border-b border-slate-700 flex items-center justify-between text-xs text-slate-300 font-bold">
                        <span class="flex items-center gap-1.5 text-emerald-400"><span class="material-symbols-outlined text-sm">code</span> Live Code Sandbox Uji Coba</span>
                        <span class="text-[10px] text-slate-400 font-normal">Menguji preview keluaran HTML/CSS/JS</span>
                    </div>
                    <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-800">
                        <div class="p-3 bg-slate-950 flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase mb-1">Editor Kode Uji</span>
                            <textarea id="q-code-input-{{ $question->id }}" oninput="updateQuestionPreview({{ $question->id }})" placeholder="Tulis kode HTML/CSS/JS untuk diuji..." class="w-full h-32 bg-transparent text-emerald-400 font-mono text-xs outline-none resize-none placeholder:text-slate-600"><h1>Preview Soal {{ $number + 1 }}</h1></textarea>
                        </div>
                        <div class="p-3 bg-white flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase mb-1">Live Output Preview</span>
                            <iframe id="q-code-iframe-{{ $question->id }}" class="w-full h-32 border border-slate-200 rounded-lg bg-white"></iframe>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Edit Form (hidden by default) --}}
            <div id="edit-form-{{ $question->id }}" class="hidden p-5 bg-slate-50/50">
                <form action="{{ route('admin.courses.modules.questions.update', $question) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipe Soal</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" data-edit-type-selector="{{ $question->id }}">
                            @foreach([
                                ['value' => 'multiple_choice', 'label' => 'Pilihan Ganda', 'icon' => 'checklist'],
                                ['value' => 'essay',           'label' => 'Essay',          'icon' => 'edit_note'],
                                ['value' => 'coding',          'label' => 'Coding',         'icon' => 'code'],
                                ['value' => 'file',            'label' => 'Upload PDF',     'icon' => 'upload_file'],
                            ] as $typeOpt)
                            <label class="edit-type-option-label cursor-pointer" data-qid="{{ $question->id }}" data-type="{{ $typeOpt['value'] }}">
                                <input type="radio" name="type" value="{{ $typeOpt['value'] }}" class="sr-only"
                                    {{ $question->type === $typeOpt['value'] ? 'checked' : '' }}>
                                <div class="edit-type-opt-card flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border-2 transition-all text-center
                                    {{ $question->type === $typeOpt['value'] ? 'border-orange-500 bg-orange-50/50' : 'border-slate-200' }}">
                                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ $question->type === $typeOpt['value'] ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400' }} transition-all edit-type-opt-icon">
                                        <span class="material-symbols-outlined text-sm">{{ $typeOpt['icon'] }}</span>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-600">{{ $typeOpt['label'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pertanyaan / Soal</label>
                        <textarea name="question" rows="3" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none resize-none">{{ $question->question }}</textarea>
                    </div>

                    {{-- Edit: Pilihan Ganda Options --}}
                    <div class="edit-options-block-{{ $question->id }} space-y-3 {{ $question->type !== 'multiple_choice' ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilihan Jawaban</label>
                        <div class="grid md:grid-cols-2 gap-3">
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                            <div class="flex items-center gap-2">
                                <div class="option-badge shrink-0">{{ $label }}</div>
                                <input name="option_{{ $key }}" value="{{ $question->{'option_'.$key} }}"
                                    placeholder="Pilihan {{ $label }}..."
                                    class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            </div>
                            @endforeach
                        </div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            Kunci:
                            <select name="correct_answer" class="ml-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-bold">
                                @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                                    <option value="{{ $key }}" {{ $question->correct_answer === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Edit: Essay Info --}}
                    <div class="edit-essay-block-{{ $question->id }} {{ $question->type !== 'essay' ? 'hidden' : '' }}">
                        <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-[11px] text-amber-700 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-amber-500">edit_note</span>
                            Essay — siswa menjawab dengan teks bebas, diperiksa manual.
                        </div>
                    </div>

                    {{-- Edit: Coding Info --}}
                    <div class="edit-coding-block-{{ $question->id }} {{ $question->type !== 'coding' ? 'hidden' : '' }}">
                        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-[11px] text-emerald-700 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-500">code</span>
                            Coding — siswa menulis kode, diperiksa manual.
                        </div>
                    </div>

                    {{-- Edit: File Info --}}
                    <div class="edit-file-block-{{ $question->id }} {{ $question->type !== 'file' ? 'hidden' : '' }}">
                        <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl text-[11px] text-rose-700 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-rose-400">upload_file</span>
                            Upload PDF — siswa mengupload file PDF sebagai jawaban.
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            Skor:
                            <input name="score" type="number" min="1" max="100" value="{{ $question->score }}" required
                                class="ml-2 w-20 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-sm font-bold text-center focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </label>
                        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-700 transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-8 text-center">
                <div class="w-14 h-14 bg-amber-100 text-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-3xl">quiz</span>
                </div>
                <h4 class="font-bold text-amber-800 text-sm">Belum ada soal</h4>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Gunakan form di atas untuk menambahkan soal pertama.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
// ===== LIVE PREVIEW FUNCTIONS =====
function updateAdminPreview() {
    const codeEl = document.getElementById('admin-sandbox-input');
    const iframe = document.getElementById('admin-sandbox-iframe');
    if (codeEl && iframe) {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(codeEl.value || '<div style="color:#94a3b8; font-family:sans-serif; font-size:12px; padding:12px;">Output preview HTML/CSS/JS akan muncul di sini...</div>');
        doc.close();
    }
}

function updateQuestionPreview(id) {
    const codeEl = document.getElementById('q-code-input-' + id);
    const iframe = document.getElementById('q-code-iframe-' + id);
    if (codeEl && iframe) {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(codeEl.value || '<div style="color:#94a3b8; font-family:sans-serif; font-size:12px; padding:12px;">Output preview HTML/CSS/JS akan muncul di sini...</div>');
        doc.close();
    }
}

function toggleAdminFullScreen(containerId) {
    const box = document.getElementById(containerId);
    if (!box) return;

    const btnText = box.querySelector('.fullscreen-text');
    const btnIcon = box.querySelector('.fullscreen-icon');
    const editorGrid = box.querySelector('.admin-editor-grid');

    if (box.classList.contains('is-fullscreen')) {
        box.classList.remove('is-fullscreen', 'fixed', 'inset-0', 'z-[9999]', 'bg-slate-950', 'p-6', 'flex', 'flex-col', 'h-screen', 'w-screen', 'overflow-hidden');
        if (btnText) btnText.textContent = 'Layar Penuh';
        if (btnIcon) btnIcon.textContent = 'fullscreen';
        if (editorGrid) editorGrid.classList.remove('h-[calc(100vh-80px)]');
        document.body.style.overflow = '';
    } else {
        box.classList.add('is-fullscreen', 'fixed', 'inset-0', 'z-[9999]', 'bg-slate-950', 'p-6', 'flex', 'flex-col', 'h-screen', 'w-screen', 'overflow-hidden');
        if (btnText) btnText.textContent = 'Keluar Layar Penuh';
        if (btnIcon) btnIcon.textContent = 'fullscreen_exit';
        if (editorGrid) editorGrid.classList.add('h-[calc(100vh-80px)]');
        document.body.style.overflow = 'hidden';
    }
}

// ===== TOGGLE EDIT FORM =====
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    form.classList.toggle('hidden');
}

// ===== NEW QUESTION — TYPE TOGGLE =====
document.addEventListener('DOMContentLoaded', function () {
    updateAdminPreview();
    @if(isset($module) && $module->questions)
        @foreach($module->questions as $q)
            @if($q->type === 'coding')
                updateQuestionPreview({{ $q->id }});
            @endif
        @endforeach
    @endif

    // --- ADD FORM TYPE SELECTOR ---
    const newTypeLabels = document.querySelectorAll('#new-type-selector .type-option-label');
    newTypeLabels.forEach(function(lbl) {
        lbl.addEventListener('click', function() {
            const type = this.dataset.type;
            const input = this.querySelector('input[type="radio"]');
            input.checked = true;

            // Reset all cards
            newTypeLabels.forEach(function(l) {
                const card = l.querySelector('.type-opt-card');
                const icon = l.querySelector('.type-opt-icon');
                card.classList.remove('border-orange-500', 'bg-orange-50/50');
                card.classList.add('border-slate-100');
                icon.classList.remove('bg-orange-100', 'text-orange-600');
                icon.classList.add('bg-slate-100', 'text-slate-400');
            });

            // Activate clicked card
            const card = this.querySelector('.type-opt-card');
            const icon = this.querySelector('.type-opt-icon');
            card.classList.add('border-orange-500', 'bg-orange-50/50');
            card.classList.remove('border-slate-100');
            icon.classList.add('bg-orange-100', 'text-orange-600');
            icon.classList.remove('bg-slate-100', 'text-slate-400');

            toggleNewQuestionBlocks(type);
        });
    });

    function toggleNewQuestionBlocks(type) {
        const optBlock   = document.getElementById('new-options-block');
        const essayBlock = document.getElementById('new-essay-block');
        const codingBlock = document.getElementById('new-coding-block');
        const fileBlock  = document.getElementById('new-file-block');
        const qTextarea  = document.getElementById('new-question');

        optBlock.classList.add('hidden');
        essayBlock.classList.add('hidden');
        codingBlock.classList.add('hidden');
        fileBlock.classList.add('hidden');

        // Update option required attrs
        document.querySelectorAll('#add-question-form input[name^="option_"]').forEach(function(el) {
            el.required = (type === 'multiple_choice');
        });
        const keyAnswer = document.getElementById('new-correct-answer');
        if (keyAnswer) keyAnswer.required = (type === 'multiple_choice');

        if (type === 'multiple_choice') {
            optBlock.classList.remove('hidden');
            qTextarea.rows = 3;
        } else if (type === 'essay') {
            essayBlock.classList.remove('hidden');
            qTextarea.rows = 4;
        } else if (type === 'coding') {
            codingBlock.classList.remove('hidden');
            qTextarea.rows = 4;
        } else if (type === 'file') {
            fileBlock.classList.remove('hidden');
            qTextarea.rows = 3;
        }
    }

    // Init on load
    const checkedNew = document.querySelector('#new-type-selector input[type="radio"]:checked');
    if (checkedNew) toggleNewQuestionBlocks(checkedNew.value);

    // --- EDIT FORM TYPE SELECTORS ---
    document.querySelectorAll('.edit-type-option-label').forEach(function(lbl) {
        lbl.addEventListener('click', function() {
            const qid  = this.dataset.qid;
            const type = this.dataset.type;
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            // Reset all in this group
            document.querySelectorAll(`.edit-type-option-label[data-qid="${qid}"]`).forEach(function(l) {
                const c = l.querySelector('.edit-type-opt-card');
                const i = l.querySelector('.edit-type-opt-icon');
                c.classList.remove('border-orange-500', 'bg-orange-50/50');
                c.classList.add('border-slate-200');
                i.classList.remove('bg-orange-100', 'text-orange-600');
                i.classList.add('bg-slate-100', 'text-slate-400');
            });

            // Activate
            const card = this.querySelector('.edit-type-opt-card');
            const icon = this.querySelector('.edit-type-opt-icon');
            card.classList.add('border-orange-500', 'bg-orange-50/50');
            card.classList.remove('border-slate-200');
            icon.classList.add('bg-orange-100', 'text-orange-600');
            icon.classList.remove('bg-slate-100', 'text-slate-400');

            toggleEditBlocks(qid, type);
        });
    });

    function toggleEditBlocks(qid, type) {
        const optB   = document.querySelector(`.edit-options-block-${qid}`);
        const essayB = document.querySelector(`.edit-essay-block-${qid}`);
        const codingB = document.querySelector(`.edit-coding-block-${qid}`);
        const fileB  = document.querySelector(`.edit-file-block-${qid}`);

        [optB, essayB, codingB, fileB].forEach(function(b) { if(b) b.classList.add('hidden'); });

        if (type === 'multiple_choice' && optB)   optB.classList.remove('hidden');
        else if (type === 'essay' && essayB)       essayB.classList.remove('hidden');
        else if (type === 'coding' && codingB)     codingB.classList.remove('hidden');
        else if (type === 'file' && fileB)         fileB.classList.remove('hidden');
    }
});
</script>
@endpush
