@extends('layouts.siswa')

@section('title', 'Pengerjaan Soal — ' . $module->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-20 px-2 sm:px-0">
    
    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('siswa.modules') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#EB4325] hover:text-white bg-brand-50 hover:bg-[#EB4325] px-4 py-2.5 rounded-xl border border-orange-200/80 shadow-sm transition-all active:scale-95">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Kembali ke Daftar Modul</span>
        </a>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('siswa.module.show', $module->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-gradient-to-r from-[#F6863B] to-[#EB4325] hover:from-[#EB4325] hover:to-[#c43018] px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition-all active:scale-95">
                <span class="material-symbols-outlined text-base">menu_book</span>
                <span>Baca Materi Modul</span>
            </a>
        </div>
    </div>

    <!-- Banner Header -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] rounded-[2rem] p-6 sm:p-8 text-white shadow-xl shadow-orange-950/10 overflow-hidden">
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-8 left-12 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>
        
        <div class="relative z-10 space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white backdrop-blur-md border border-white/20">
                    {{ $module->course->title ?? 'Kursus' }}
                </span>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/30 text-emerald-200 border border-emerald-400/30 backdrop-blur-md">
                    {{ $questions->count() }} Soal Evaluasi
                </span>
            </div>
            
            <h1 class="text-xl sm:text-2xl font-black leading-tight">{{ $module->title }}</h1>
            <p class="text-xs sm:text-sm text-white/90 max-w-2xl leading-relaxed">
                Jawab pertanyaan atau kumpulkan tugas di bawah ini. Jawaban yang berhasil dikirim akan memperbarui progres modul Anda menjadi <strong class="text-emerald-300">100% Selesai (Ceklis Hijau)</strong>.
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800">
        <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
        <div>
            <h4 class="text-sm font-bold">Berhasil!</h4>
            <p class="text-xs">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @php
        $existingAnswers = $submission && $submission->answer ? json_decode($submission->answer, true) : [];
        if (!is_array($existingAnswers)) { $existingAnswers = []; }
        $answeredQIds = array_keys($existingAnswers);

        $unansweredQuestions = $questions->reject(function($q) use ($answeredQIds) {
            return in_array($q->id, $answeredQIds) || in_array((string)$q->id, $answeredQIds);
        });

        $hasUnansweredNewQuestions = $submission && in_array($submission->status, ['submitted', 'graded']) && $questions->isNotEmpty() && $unansweredQuestions->isNotEmpty();
        $isLocked = $submission && in_array($submission->status, ['submitted', 'graded']) && !$hasUnansweredNewQuestions;
    @endphp

    @if($hasUnansweredNewQuestions)
    <div class="p-6 bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] text-white rounded-3xl shadow-xl shadow-orange-950/15 space-y-3 border border-orange-400/30 relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-white/20 text-white flex items-center justify-center shrink-0 border border-white/30 backdrop-blur-md shadow-inner">
                <span class="material-symbols-outlined text-2xl">auto_awesome</span>
            </div>
            <div>
                <h4 class="text-sm font-black text-white">Soal Baru Ditambahkan oleh Instruktur!</h4>
                <p class="text-xs text-orange-100 mt-0.5 leading-relaxed">
                    Instruktur telah menambahkan <strong>{{ $unansweredQuestions->count() }} soal baru</strong> untuk modul ini. Jawaban lama Anda ({{ count($answeredQIds) }} soal) tetap tersimpan dan terkunci. Silakan jawab soal baru di bawah ini untuk melengkapi tugas Anda.
                </p>
            </div>
        </div>
    </div>
    @elseif($isLocked)
    <div class="p-6 bg-slate-900 text-white rounded-3xl shadow-xl space-y-3 border border-slate-800">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white">Jawaban Telah Dikirim & Dikunci</h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Dikirim pada {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }} • Status:
                        <span class="font-extrabold text-emerald-400 uppercase">{{ $submission->status === 'graded' ? 'Sudah Dinilai' : 'Menunggu Penilaian' }}</span>
                    </p>
                </div>
            </div>
            @if($submission->score !== null)
                <div class="text-right bg-slate-800/80 px-4 py-2 rounded-2xl border border-slate-700">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block">Nilai Perolehan</span>
                    <span class="text-xl font-black text-white">{{ $submission->score }} <span class="text-xs text-slate-400 font-bold">/ {{ $submission->max_score ?? 100 }}</span></span>
                </div>
            @endif
        </div>
        @if($submission->feedback)
            <div class="p-4 bg-orange-950/40 border border-orange-500/30 rounded-2xl text-xs text-orange-200 leading-relaxed">
                <strong class="text-orange-400 font-bold block mb-1">Catatan / Feedback Instruktur:</strong>
                "{{ $submission->feedback }}"
            </div>
        @endif
    </div>
    @elseif($submission && $submission->status === 'draft')
    <div class="p-4 sm:p-5 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3.5 text-amber-900 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-2xl">edit_note</span>
        </div>
        <div>
            <h4 class="text-xs sm:text-sm font-bold text-amber-900">Modul Ini Memiliki Draf Tersimpan</h4>
            <p class="text-[11px] sm:text-xs text-amber-700/90 mt-0.5">Anda dapat melanjutkan pengisian jawaban sebelum menekan tombol Kirim & Selesaikan.</p>
        </div>
    </div>
    @endif

    <!-- Form Pengerjaan Soal -->
    <form action="{{ route('siswa.module.assignment.submit', $module->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if($questions->isNotEmpty())
            <div class="space-y-6">
                @foreach($questions as $index => $q)
                    @php
                        $savedAnswer = $existingAnswers[$q->id] ?? ($existingAnswers[(string)$q->id] ?? '');
                        $isQuestionSubmitted = (isset($existingAnswers[$q->id]) && $existingAnswers[$q->id] !== '') || (isset($existingAnswers[(string)$q->id]) && $existingAnswers[(string)$q->id] !== '');
                        $isQuestionLocked = $submission && in_array($submission->status, ['submitted', 'graded']) && $isQuestionSubmitted;
                    @endphp
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm hover:shadow-md transition-all space-y-5 relative">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-orange-50 text-[#EB4325] border border-orange-200/80 font-black text-xs flex items-center justify-center shadow-xs">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg border
                                {{ $q->type === 'multiple_choice' ? 'bg-purple-50 text-purple-700 border-purple-200/80' :
                                   ($q->type === 'essay' ? 'bg-amber-50 text-amber-800 border-amber-200/80' :
                                   ($q->type === 'coding' ? 'bg-emerald-50 text-emerald-800 border-emerald-200/80' : 'bg-orange-50 text-[#EB4325] border-orange-200/80')) }}">
                                {{ ['multiple_choice'=>'Pilihan Ganda', 'essay'=>'Essay', 'coding'=>'Latihan Coding', 'file'=>'Upload PDF'][$q->type] ?? $q->type }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($isQuestionLocked)
                                <span class="text-[10px] font-black uppercase text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full flex items-center gap-1 border border-slate-200">
                                    <span class="material-symbols-outlined text-xs text-emerald-500">check_circle</span>
                                    Terkunci
                                </span>
                            @elseif($hasUnansweredNewQuestions && !$isQuestionSubmitted)
                                <span class="text-[10px] font-black uppercase text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full flex items-center gap-1 animate-pulse">
                                    <span class="material-symbols-outlined text-xs text-amber-600">auto_awesome</span>
                                    Soal Baru
                                </span>
                            @endif
                            <span class="text-xs font-black text-[#EB4325] bg-orange-50 border border-orange-200/80 px-3 py-1 rounded-full shadow-xs">
                                {{ $q->score }} Poin
                            </span>
                        </div>
                    </div>

                    <div class="text-sm sm:text-base font-bold text-slate-800 leading-relaxed whitespace-pre-line">
                        {!! nl2br(e($q->question)) !!}
                    </div>

                    {{-- 1. Pilihan Ganda --}}
                    @if($q->type === 'multiple_choice')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            @foreach(['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d] as $key => $optionText)
                                @if(!empty($optionText))
                                <label class="flex items-start gap-3 p-3.5 sm:p-4 rounded-2xl border transition-all cursor-pointer {{ $isQuestionLocked ? 'bg-slate-50 border-slate-200 opacity-80 cursor-not-allowed' : 'bg-slate-50/70 border-slate-200 hover:bg-orange-50/50 hover:border-orange-300 has-[:checked]:bg-orange-50/70 has-[:checked]:border-[#EB4325] has-[:checked]:ring-2 has-[:checked]:ring-orange-500/15 shadow-xs' }} group">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}"
                                        @checked(old("answers.{$q->id}", $savedAnswer) === $key)
                                        {{ $isQuestionLocked ? 'disabled' : '' }}
                                        class="mt-0.5 text-[#EB4325] focus:ring-[#EB4325] focus:ring-offset-0 border-slate-300">
                                    <span class="text-xs sm:text-[13px] font-semibold text-slate-700 leading-snug group-hover:text-slate-900">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-white border border-slate-200 text-[10px] font-black uppercase text-slate-500 mr-1.5 group-hover:border-orange-300 group-hover:text-[#EB4325] group-has-[:checked]:bg-[#EB4325] group-has-[:checked]:border-[#EB4325] group-has-[:checked]:text-white transition-all">{{ $key }}</span>
                                        {{ $optionText }}
                                    </span>
                                </label>
                                @endif
                            @endforeach
                        </div>

                    {{-- 2. Essay --}}
                    @elseif($q->type === 'essay')
                        <div class="pt-2">
                            <textarea name="answers[{{ $q->id }}]" rows="4" {{ $isQuestionLocked ? 'disabled' : '' }} placeholder="Tuliskan jawaban essay Anda di sini..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-[#EB4325] transition-all outline-none leading-relaxed {{ $isQuestionLocked ? 'bg-slate-100 text-slate-600' : '' }}">{{ old("answers.{$q->id}", $savedAnswer) }}</textarea>
                        </div>

                    {{-- 3. Coding dengan Live Preview & Fullscreen --}}
                    @elseif($q->type === 'coding')
                        <div id="coding-box-{{ $q->id }}" class="pt-2 space-y-3 transition-all duration-300">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-emerald-500">terminal</span>
                                    <span>Editor Kode & Live Preview</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/80 shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Real-time Live Preview
                                    </span>
                                    <button type="button" onclick="toggleFullScreen('coding-box-{{ $q->id }}')"
                                        class="fullscreen-btn inline-flex items-center gap-1.5 text-xs font-bold text-[#EB4325] hover:text-white bg-orange-50 hover:bg-[#EB4325] px-3 py-1 rounded-xl transition-all border border-orange-200/80 active:scale-95 shadow-xs">
                                        <span class="material-symbols-outlined text-base fullscreen-icon">fullscreen</span>
                                        <span class="fullscreen-text">Layar Penuh</span>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 coding-editor-grid flex-1">
                                {{-- Left: Dark Code Editor --}}
                                <div class="flex flex-col h-full rounded-2xl overflow-hidden border border-slate-800 shadow-inner">
                                    <div class="flex items-center justify-between px-3.5 py-2.5 bg-slate-800 text-[11px] font-mono text-slate-300 shrink-0 border-b border-slate-700">
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                            <span class="ml-1 text-slate-200 font-bold">index.html</span>
                                        </span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">HTML / CSS / JS</span>
                                    </div>
                                    <textarea name="answers[{{ $q->id }}]" id="coding-input-{{ $q->id }}" rows="10"
                                        {{ $isQuestionLocked ? 'disabled' : '' }}
                                        placeholder="<h1>Hello World!</h1>&#10;<style>h1 { color: #EB4325; font-family: sans-serif; }</style>"
                                        oninput="renderLivePreview('coding-input-{{ $q->id }}', 'coding-preview-{{ $q->id }}')"
                                        class="w-full flex-1 min-h-[240px] px-4 py-3 bg-slate-900 text-emerald-400 font-mono text-xs focus:ring-2 focus:ring-[#EB4325]/30 focus:border-[#EB4325] transition-all outline-none leading-relaxed resize-y {{ $isQuestionLocked ? 'opacity-90' : '' }}">{{ old("answers.{$q->id}", $savedAnswer) }}</textarea>
                                </div>

                                {{-- Right: Live Preview Iframe Frame --}}
                                <div class="flex flex-col h-full rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                                    <div class="flex items-center justify-between px-3.5 py-2.5 bg-slate-100 text-[11px] font-mono text-slate-500 shrink-0 border-b border-slate-200">
                                        <span class="flex items-center gap-1.5 font-bold text-slate-700">
                                            <span class="material-symbols-outlined text-sm text-[#EB4325]">preview</span>
                                            Hasil Output (Live Preview)
                                        </span>
                                        <button type="button" onclick="renderLivePreview('coding-input-{{ $q->id }}', 'coding-preview-{{ $q->id }}')" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#EB4325] hover:text-[#c43018] bg-white hover:bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-200/80 transition-all shadow-xs active:scale-95">
                                            <span class="material-symbols-outlined text-xs">refresh</span>
                                            <span>Refresh Output</span>
                                        </button>
                                    </div>
                                    <div class="bg-white flex-1 min-h-[240px]">
                                        <iframe id="coding-preview-{{ $q->id }}" class="w-full h-full border-0 bg-white" title="Live Preview Output"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- 4. PDF File Upload --}}
                    @elseif($q->type === 'file')
                        <div class="pt-2 space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Unggah Berkas PDF (Maks 20MB)</label>
                            @if(isset($submission->answer_files[$q->id]))
                                <div class="p-3 bg-orange-50 border border-orange-200/80 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
                                    <span class="text-xs font-bold text-[#8A1E1E] flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-base text-[#EB4325]">picture_as_pdf</span>
                                        Berkas PDF Berhasil Diunggah
                                    </span>
                                    <a href="{{ Storage::url($submission->answer_files[$q->id]) }}" target="_blank" class="px-3.5 py-1.5 bg-gradient-to-r from-[#F6863B] to-[#EB4325] hover:from-[#EB4325] hover:to-[#c43018] text-white rounded-xl text-xs font-bold shadow-md shadow-orange-500/20 active:scale-95 transition-all inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        Lihat PDF
                                    </a>
                                </div>
                            @endif
                            @if(!$isQuestionLocked)
                                <input type="file" name="answer_files[{{ $q->id }}]" accept=".pdf,image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border file:border-orange-200/80 file:text-xs file:font-bold file:bg-orange-50 file:text-[#EB4325] hover:file:bg-[#EB4325] hover:file:text-white file:transition-all cursor-pointer">
                            @endif
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            {{-- Default Single Answer Box --}}
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-orange-50 text-[#EB4325] border border-orange-200/80 font-black text-xs flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-base">assignment</span>
                    </span>
                    <h4 class="text-sm font-bold text-slate-800">Jawaban / Pengumpulan Tugas Modul</h4>
                </div>
                <div>
                    <textarea name="answer" rows="5" {{ $isLocked ? 'disabled' : '' }} placeholder="Tuliskan jawaban atau keterangan tugas di sini..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-[#EB4325] transition-all outline-none leading-relaxed {{ $isLocked ? 'bg-slate-100 text-slate-600' : '' }}">{{ old('answer', $submission->answer ?? '') }}</textarea>
                </div>
                @if(!$isLocked)
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Lampirkan File PDF / Gambar (Opsional)</label>
                    <input type="file" name="file" accept=".pdf,image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border file:border-orange-200/80 file:text-xs file:font-bold file:bg-orange-50 file:text-[#EB4325] hover:file:bg-[#EB4325] hover:file:text-white file:transition-all cursor-pointer">
                </div>
                @endif
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-slate-200">
            @if($isLocked)
                <div class="text-xs font-bold text-emerald-800 bg-emerald-50 px-4 py-3 rounded-2xl border border-emerald-200 flex items-center gap-2 shadow-xs w-full sm:w-auto">
                    <span class="material-symbols-outlined text-lg text-emerald-600">lock</span>
                    <span>Jawaban Anda Sudah Dikirim & Terkunci (Tidak Dapat Mengirim Ulang)</span>
                </div>
                <a href="{{ route('siswa.grades') }}" class="px-6 py-3 bg-gradient-to-r from-[#F6863B] to-[#EB4325] hover:from-[#EB4325] hover:to-[#c43018] text-white font-bold text-xs rounded-2xl transition-all shadow-lg shadow-orange-500/25 active:scale-95 inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">grade</span>
                    <span>Lihat Rekap Nilai Saya</span>
                </a>
            @else
                <div id="autosave-status" class="text-xs font-bold text-slate-500 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center gap-2 w-full sm:w-auto justify-center sm:justify-start">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    <span>Auto-save aktif (draf tersimpan di browser)</span>
                </div>
                
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <a href="{{ route('siswa.modules') }}" class="px-5 py-3 rounded-2xl border border-slate-200/90 bg-white font-bold text-slate-600 hover:text-[#EB4325] hover:border-orange-200 hover:bg-orange-50/50 text-xs transition-all text-center active:scale-95 shadow-xs">
                        Batal
                    </a>
                    <button type="submit" id="btn-save-draft" onclick="submitAsDraft(event)" class="px-5 py-3 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-black text-xs rounded-2xl shadow-lg shadow-amber-500/25 hover:shadow-amber-500/35 transition-all flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Simpan Draf</span>
                    </button>
                    <button type="submit" id="btn-submit-final" onclick="submitFinal(event)" class="px-6 py-3 bg-gradient-to-r from-[#F6863B] to-[#EB4325] hover:from-[#EB4325] hover:to-[#c43018] active:scale-95 text-white font-black text-xs rounded-2xl shadow-xl shadow-orange-500/30 hover:shadow-orange-500/40 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">send</span>
                        <span>Kirim & Selesaikan</span>
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const DRAFT_KEY = 'smart_acp_draft_m{{ $module->id }}_s{{ Auth::guard('student')->id() }}';

function renderLivePreview(inputId, iframeId) {
    const input = document.getElementById(inputId);
    const iframe = document.getElementById(iframeId);
    if (!input || !iframe) return;

    const doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write(input.value || '<div style="color:#94a3b8; font-family:sans-serif; font-size:12px; padding:16px; text-align:center;">Output preview HTML/CSS/JS akan muncul di sini secara real-time...</div>');
    doc.close();
}

function toggleFullScreen(containerId) {
    const box = document.getElementById(containerId);
    if (!box) return;

    const btnText = box.querySelector('.fullscreen-text');
    const btnIcon = box.querySelector('.fullscreen-icon');
    const editorGrid = box.querySelector('.coding-editor-grid');

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

// Save inputs to localStorage
function saveLocalDraft() {
    const form = document.querySelector('form[action*="assignment"]');
    if (!form) return;
    const formData = new FormData(form);
    const draftObj = {};
    for (let [key, val] of formData.entries()) {
        if (typeof val === 'string' && key !== '_token') {
            draftObj[key] = val;
        }
    }
    localStorage.setItem(DRAFT_KEY, JSON.stringify(draftObj));
    const statusEl = document.getElementById('autosave-status');
    if (statusEl) {
        const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        statusEl.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> <span>Draf tersimpan di browser (${time})</span>`;
    }
}

// Restore draft from localStorage
function restoreLocalDraft() {
    const saved = localStorage.getItem(DRAFT_KEY);
    if (!saved) return;
    try {
        const draftObj = JSON.parse(saved);
        for (let key in draftObj) {
            const val = draftObj[key];
            const el = document.querySelector(`[name="${key}"]`);
            if (el) {
                if (el.type === 'radio') {
                    const radio = document.querySelector(`[name="${key}"][value="${val}"]`);
                    if (radio) radio.checked = true;
                } else if (!el.value) {
                    el.value = val;
                }
            }
        }
    } catch(e) {
        console.error("Error restoring draft:", e);
    }
}

function submitAsDraft(e) {
    e.preventDefault();
    saveLocalDraft();
    const form = e.target.closest('form');
    form.action = "{{ route('siswa.module.assignment.draft', $module->id) }}";
    form.submit();
}

function submitFinal(e) {
    e.preventDefault();
    localStorage.removeItem(DRAFT_KEY);
    const form = e.target.closest('form');
    form.action = "{{ route('siswa.module.assignment.submit', $module->id) }}";
    form.submit();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.is-fullscreen').forEach(box => {
            toggleFullScreen(box.id);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    restoreLocalDraft();

    // Auto save on any input change
    document.querySelectorAll('textarea, input[type="text"], input[type="radio"]').forEach(el => {
        el.addEventListener('input', saveLocalDraft);
        el.addEventListener('change', saveLocalDraft);
    });

    // Render live previews
    @if(isset($questions))
        @foreach($questions as $q)
            @if($q->type === 'coding')
                renderLivePreview('coding-input-{{ $q->id }}', 'coding-preview-{{ $q->id }}');
            @endif
        @endforeach
    @endif
});
</script>
@endpush
