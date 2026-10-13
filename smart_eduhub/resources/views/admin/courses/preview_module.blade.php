@extends('layouts.app')

@section('title', 'Preview Modul: ' . $module->title)

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
    /* Custom Styling for Rich Text Content (Trix Editor / Prose) */
    .trix-content {
        font-size: 1rem !important;
        line-height: 1.8 !important;
    }
    
    .trix-content pre {
        background: #1e293b !important;
        color: #e2e8f0 !important;
        padding: 1rem !important;
        border-radius: 0.75rem !important;
        font-family: 'Fira Code', monospace !important;
        font-size: 0.875rem !important;
        overflow-x: auto !important;
    }
    
    .trix-content code {
        background: #f1f5f9 !important;
        color: #4f46e5 !important;
        padding: 0.125rem 0.375rem !important;
        border-radius: 0.25rem !important;
        font-family: 'Fira Code', monospace !important;
        font-size: 0.875em !important;
    }
    
    .trix-content pre code {
        display: block;
        background: transparent !important;
        color: #e2e8f0 !important;
        padding: 0 !important;
    }
    
    .trix-content ul { 
        list-style-type: disc !important; 
        padding-left: 1.5rem !important; 
        margin-bottom: 1.5rem !important;
    }
    .trix-content ul li { 
        margin-bottom: 0.5rem !important;
    }
    
    .trix-content ol { 
        list-style-type: decimal !important; 
        padding-left: 1.5rem !important; 
        margin-bottom: 1.5rem !important;
    }
    .trix-content ol li { 
        margin-bottom: 0.5rem !important;
    }
    
    .trix-content li { 
        color: #334155;
    }
    .trix-content a { 
        color: #4f46e5; 
        text-decoration: underline; 
        font-weight: 700; 
        transition: color 0.2s ease;
    }
    .trix-content a:hover {
        color: #3b32d8;
    }
    .trix-content strong { 
        font-weight: 700; 
        color: #1e293b; 
    }
    .trix-content h1 {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin-top: 2rem !important;
        margin-bottom: 1rem !important;
    }
    .trix-content h2 {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 1.5rem !important;
        margin-bottom: 0.75rem !important;
    }
    .trix-content h3 {
        font-size: 1.125rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 1.25rem !important;
        margin-bottom: 0.5rem !important;
    }
    .trix-content p {
        margin-bottom: 1.25rem !important;
        color: #334155 !important;
        line-height: 1.8 !important;
    }
    .trix-content blockquote {
        border-left: 4px solid #4f46e5 !important;
        margin: 1rem 0 !important;
        padding: 0.5rem 1rem !important;
        color: #64748b !important;
        font-style: italic !important;
    }
    .trix-content hr {
        border: none !important;
        border-top: 2px solid #e2e8f0 !important;
        margin: 1.5rem 0 !important;
    }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header Admin Info -->
    <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl flex items-center justify-between no-print">
        <div class="flex items-center gap-3 text-amber-800">
            <span class="material-symbols-outlined">visibility</span>
            <p class="text-sm font-bold">Mode Preview: Menampilkan tampilan seperti di portal siswa.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-xs font-black text-amber-900 uppercase tracking-widest hover:underline">
            Tutup Preview
        </a>
    </div>

    <!-- Navigation & Breadcrumb (Simulasi) -->
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-2 text-sm font-bold text-slate-400">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Daftar Modul
        </div>
        <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
            <span>{{ $module->course->title }}</span>
            <span class="material-symbols-outlined text-[10px]">chevron_right</span>
            <span class="text-slate-900">{{ $module->title }}</span>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Module Header -->
        <div class="p-10 lg:p-12 border-b border-slate-50 bg-slate-50/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100">
                        <span class="material-symbols-outlined text-xs">auto_stories</span>
                        {{ $module->type === 'text' ? 'Materi Teks' : strtoupper($module->type) }}
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 leading-tight">{{ $module->title }}</h1>
                    <p class="text-slate-500 font-medium">{{ $module->description }}</p>
                </div>
                
                @if($module->file_path && $module->type !== 'text')
                <div class="shrink-0">
                    @php
                        $isExternal = $module->type === 'link' || str_starts_with($module->file_path ?? '', 'http');
                        $url = $isExternal ? $module->file_path : asset('storage/'.$module->file_path);
                    @endphp
                    @if($module->type === 'video')
                    <button onclick="document.getElementById('video-player').scrollIntoView({behavior: 'smooth'})" class="flex items-center gap-3 px-8 py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                        <span class="material-symbols-outlined">
                            play_circle
                        </span>
                        Putar Video
                    </button>
                    @else
                    <a href="{{ $url }}" target="_blank" class="flex items-center gap-3 px-8 py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                        <span class="material-symbols-outlined">
                            {{ $module->type === 'link' ? 'open_in_new' : 'download' }}
                        </span>
                        {{ $module->type === 'link' ? 'Buka Link' : 'Unduh Dokumen' }}
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Module Body -->
        <div class="p-10 lg:p-16 space-y-10">
            @if($module->type === 'video' && $module->file_path)
                @php
                    $videoUrl = $module->file_path;
                    $videoId = '';
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match)) {
                        $videoId = $match[1];
                    }
                @endphp

                @if($videoId)
                <div id="video-player" class="aspect-video rounded-[32px] overflow-hidden border-8 border-slate-100 shadow-2xl bg-black">
                    <iframe 
                        class="w-full h-full" 
                        src="https://www.youtube.com/embed/{{ $videoId }}?rel=0" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                </div>
                @else
                <div id="video-player" class="p-8 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-700">
                    <span class="material-symbols-outlined">error</span>
                    <div>
                        <p class="text-sm font-bold">Link YouTube tidak valid atau format tidak didukung.</p>
                        <p class="text-xs mt-1">Pastikan link yang dimasukkan adalah link video YouTube yang benar.</p>
                    </div>
                </div>
                @endif
            @endif

            @if($module->content)
                <div class="trix-content max-w-none">
                    {!! $module->content !!}
                </div>
            @elseif($module->type !== 'video')
                <div class="py-20 text-center space-y-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto">
                        <span class="material-symbols-outlined text-4xl text-slate-200">description</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Tidak ada isi teks</h3>
                    <p class="text-slate-400 font-medium">Modul ini hanya berisi file lampiran atau video.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.trix-content pre').forEach(function(pre) {
            let code = pre.querySelector(':scope > code');

            if (!code) {
                code = document.createElement('code');
                pre.classList.forEach(function(className) {
                    if (className.startsWith('language-')) code.classList.add(className);
                });
                code.textContent = pre.innerText;
                pre.replaceChildren(code);
            }

            const fencedCode = code.textContent.match(/^\s*```([\w+-]+)?[ \t]*\r?\n([\s\S]*?)\r?\n```[ \t]*\s*$/);
            if (fencedCode) {
                code.textContent = fencedCode[2];
                if (fencedCode[1] && !Array.from(code.classList).some(function(name) { return name.startsWith('language-'); })) {
                    code.classList.add('language-' + fencedCode[1]);
                }
            }

            if (window.hljs) hljs.highlightElement(code);
        });
    });
</script>
@endpush
