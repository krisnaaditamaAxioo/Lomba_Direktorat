@extends('layouts.siswa')

@section('title', 'Modul & Materi')
@section('page_title', 'Perpustakaan Digital')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16 px-2 sm:px-0">
    
    <!-- Purple Curved Header Block (Edutech Style) - Matching Finance Dashboard -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 pb-8 px-5 text-white overflow-hidden shadow-xl shadow-brand-950/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-5 lg:pb-6 transition-all duration-300">
        <!-- Abstract Circular Glow Overlays -->
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-8 left-12 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- App Logo & Branding Title -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/10">
                    <span class="material-symbols-outlined text-white text-lg font-bold">auto_stories</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-brand-200 block uppercase tracking-wider leading-none">Perpustakaan Digital</span>
                    <h1 class="text-lg font-black tracking-tight block mt-1">Modul & Materi</h1>
                </div>
            </div>

            <!-- Dynamic Live Module Count Badge -->
            <div class="flex items-center gap-2.5 px-3.5 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-[11px] font-bold text-brand-100">
                    {{ count($modules) }} Modul Tersedia
                </span>
            </div>
        </div>
    </div>

    <!-- Module List Container - Clean White & Neo-Minimalist -->
    <div class="bg-white rounded-[2.2rem] border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- List Header -->
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-brand-600 text-xl">folder_open</span>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-800">Daftar Modul Pembelajaran</h3>
            </div>
        </div>

        <!-- Divide-y Flat Minimalist List Items -->
        <div class="divide-y divide-slate-100/80">
            @forelse($modules as $module)
            <div class="p-5 sm:p-6 hover:bg-slate-50/40 transition-all duration-200 group flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <!-- Left Section: Icon & Info Texts -->
                <div class="flex items-center gap-4 min-w-0">
                    <!-- Icon Container with Soft Background corresponding to the module type -->
                    <div class="w-12 h-12 rounded-2xl shrink-0 flex items-center justify-center border {{
                        $module->student_progress >= 100 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' :
                        ($module->type === 'video' ? 'bg-red-50 text-red-600 border-red-100' :
                        ($module->type === 'link' ? 'bg-amber-50 text-amber-600 border-amber-100' :
                        ($module->type === 'text' ? 'bg-brand-50 text-brand-600 border-brand-100' : 'bg-rose-50 text-rose-600 border-rose-100')))
                    }} shadow-sm">
                        <span class="material-symbols-outlined text-2xl">
                            {{ $module->student_progress >= 100 ? 'check_circle' : ($module->type === 'video' ? 'video_library' : ($module->type === 'link' ? 'link' : ($module->type === 'text' ? 'article' : 'picture_as_pdf'))) }}
                        </span>
                    </div>

                    <!-- Typography & Status Details -->
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider border {{
                                $module->type === 'video' ? 'bg-red-50 text-red-700 border-red-100' :
                                ($module->type === 'link' ? 'bg-amber-50 text-amber-700 border-amber-100' :
                                (in_array($module->type, ['text', 'quiz', 'coding']) ? 'bg-brand-50 text-brand-700 border-brand-100' : 'bg-rose-50 text-rose-700 border-rose-100'))
                            }}">
                                {{ ['text' => 'Materi Teks', 'assignment' => 'Tugas', 'quiz' => 'Kuis / Soal', 'coding' => 'Latihan Coding'][$module->type] ?? strtoupper($module->type) }}
                            </span>

                            @if($module->questions->count() > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-brand-50 text-brand-700 border border-brand-100">
                                    <span class="material-symbols-outlined text-[11px]">quiz</span>
                                    {{ $module->questions->count() }} Soal Evaluasi
                                </span>
                            @endif

                            @if(!empty($module->has_new_questions))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                    <span class="material-symbols-outlined text-xs text-amber-600">auto_awesome</span>
                                    <span>{{ $module->unanswered_count }} Soal Baru Ditambahkan</span>
                                </span>
                            @elseif($module->student_progress >= 100)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="material-symbols-outlined text-xs text-emerald-600">check_circle</span>
                                    <span>Selesai</span>
                                </span>
                            @endif
                        </div>
                        
                        <a href="{{ route('siswa.module.show', $module->id) }}" class="block">
                            <h4 class="text-sm font-extrabold text-slate-800 group-hover:text-brand-600 transition-colors leading-snug truncate flex items-center gap-1.5">
                                <span>{{ $module->title }}</span>
                                @if($module->student_progress >= 100)
                                    <span class="material-symbols-outlined text-emerald-500 text-base font-bold" title="Modul Selesai">check_circle</span>
                                @endif
                            </h4>
                        </a>
                        
                        <p class="text-[10px] text-slate-400 font-medium mt-1 truncate">
                            Kelas: {{ $module->course->title ?? 'Umum' }} • Diperbarui {{ $module->updated_at->diffForHumans() }}
                        </p>
                        
                        <!-- Progress Bar with Green Checkmark Highlight -->
                        <div class="flex items-center gap-2 mt-2 max-w-xs">
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden flex-1">
                                <div class="h-full {{ $module->student_progress >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-[#F6863B] to-[#EB4325]' }} rounded-full transition-all duration-500" style="width: {{ $module->student_progress }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold {{ $module->student_progress >= 100 ? 'text-emerald-600' : 'text-brand-600' }}">
                                {{ $module->student_progress }}%
                            </span>
                        </div>
                    </div>
                </div>

                @php
                    $isExternal = $module->type === 'link' || str_starts_with($module->file_path ?? '', 'http');
                    $url = $isExternal ? $module->file_path : asset('storage/'.$module->file_path);
                @endphp

                <!-- Right Section: Action Buttons -->
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0 flex-wrap">
                    @if($module->student_progress >= 100)
                        <a href="{{ route('siswa.module.show', $module->id) }}" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200/80 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                            <span class="material-symbols-outlined text-sm font-bold">check_circle</span>
                            <span>Pelajari Lagi</span>
                        </a>
                    @else
                        <a href="{{ route('siswa.module.show', $module->id) }}" class="px-4 py-2 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200/80 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 shadow-sm shadow-brand-500/10 active:scale-95">
                            <span class="material-symbols-outlined text-sm">menu_book</span>
                            <span>Pelajari</span>
                        </a>
                    @endif
                    
                    @if($module->questions->count() > 0 || in_array($module->type, ['assignment', 'quiz', 'coding']))
                        @if(!empty($module->has_new_questions))
                            <a href="{{ route('siswa.module.soal', $module->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-amber-500/25 active:scale-95">
                                <span class="material-symbols-outlined text-sm">auto_awesome</span>
                                <span>Kerjakan Soal Baru</span>
                            </a>
                        @elseif($module->student_progress >= 100)
                            <a href="{{ route('siswa.module.soal', $module->id) }}" class="px-4 py-2 bg-white hover:bg-brand-50 text-brand-600 hover:text-brand-700 border border-brand-200 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                <span class="material-symbols-outlined text-sm">quiz</span>
                                <span>Ulas Soal</span>
                            </a>
                        @else
                            <a href="{{ route('siswa.module.soal', $module->id) }}" class="px-4 py-2 bg-gradient-to-r from-[#F6863B] to-[#EB4325] hover:brightness-105 text-white rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-orange-500/25 active:scale-95">
                                <span class="material-symbols-outlined text-sm">quiz</span>
                                <span>Kerjakan Soal</span>
                            </a>
                        @endif
                    @endif

                    @if($module->file_path && !in_array($module->type, ['text', 'assignment', 'quiz', 'coding']))
                        @if($module->type === 'video')
                        <a href="{{ route('siswa.module.show', $module->id) }}" class="p-2.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200/80 rounded-xl transition-all shadow-sm active:scale-95" title="Putar Video">
                            <span class="material-symbols-outlined text-base">play_circle</span>
                        </a>
                        @elseif($module->type === 'link')
                        <a href="{{ $url }}" target="_blank" class="p-2.5 bg-amber-50 hover:bg-amber-600 text-amber-600 hover:text-white border border-amber-200/80 rounded-xl transition-all shadow-sm active:scale-95" title="Kunjungi Tautan">
                            <span class="material-symbols-outlined text-base">open_in_new</span>
                        </a>
                        @else
                        <a href="{{ $url }}" target="_blank" class="p-2.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200/80 rounded-xl transition-all shadow-sm active:scale-95" title="Unduh Berkas Lampiran">
                            <span class="material-symbols-outlined text-base">download</span>
                        </a>
                        @endif
                    @endif
                </div>

            </div>
            @empty
            <!-- Minimalist Empty State Inside White Card -->
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100/50">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Modul Aktif</h3>
                <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 leading-relaxed">
                    Materi pembelajaran standar industri Anda belum diterbitkan oleh instruktur kurikulum sekolah Anda.
                </p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleContent(id) {
        const content = document.getElementById(id);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    }
</script>
@endsection