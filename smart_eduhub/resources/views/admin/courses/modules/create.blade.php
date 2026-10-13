@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
    /* Fix Trix Toolbar from Tailwind Preflight */
    trix-toolbar {
        margin-bottom: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem 1rem 0 0;
        background: #f8fafc;
        padding: 0.5rem;
    }

    /* Hide only file tools */
    trix-toolbar .trix-button-group--file-tools {
        display: none !important;
    }

    /* Trix Editor */
    trix-editor {
        min-height: 250px !important;
        max-height: 500px !important;
        overflow-y: auto !important;
        border-radius: 0 0 1.5rem 1.5rem !important;
        border: 1px solid #f1f5f9 !important;
        border-top: none !important;
        background-color: #f8fafc !important;
        padding: 1.25rem !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        color: #334155 !important;
        line-height: 1.7 !important;
        outline: none !important;
    }

    trix-editor:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
    }

    /* Fix list styling inside editor */
    trix-editor ul,
    trix-editor ol {
        margin: 0.75rem 0 !important;
        padding-left: 1.5rem !important;
    }

    trix-editor ul {
        list-style-type: disc !important;
    }

    trix-editor ol {
        list-style-type: decimal !important;
    }

    trix-editor li {
        display: list-item !important;
        margin: 0.25rem 0 !important;
    }

    trix-editor blockquote {
        border-left: 4px solid #4f46e5 !important;
        margin: 0.75rem 0 !important;
        padding: 0.5rem 1rem !important;
        color: #64748b !important;
        font-style: italic !important;
    }

    trix-editor pre {
        background: #1e293b !important;
        color: #e2e8f0 !important;
        padding: 1rem !important;
        border-radius: 0.75rem !important;
        font-family: 'Fira Code', monospace !important;
        font-size: 0.8125rem !important;
        overflow-x: auto !important;
        margin: 0.75rem 0 !important;
    }

    trix-editor code {
        background: #f1f5f9 !important;
        color: #4f46e5 !important;
        padding: 0.125rem 0.375rem !important;
        border-radius: 0.25rem !important;
        font-size: 0.8125rem !important;
        font-family: 'Fira Code', monospace !important;
    }

    trix-editor pre code {
        background: transparent !important;
        color: inherit !important;
        padding: 0 !important;
    }

    trix-editor a {
        color: #4f46e5 !important;
        text-decoration: underline !important;
    }

    trix-editor h1 {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        margin: 1rem 0 0.5rem !important;
    }

    trix-editor h2 {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        margin: 0.875rem 0 0.375rem !important;
    }

    trix-editor h3 {
        font-size: 1.125rem !important;
        font-weight: 700 !important;
        margin: 0.75rem 0 0.25rem !important;
    }

    trix-editor hr {
        border: none !important;
        border-top: 2px solid #e2e8f0 !important;
        margin: 1.25rem 0 !important;
    }

    trix-editor img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 0.75rem !important;
        margin: 0.75rem 0 !important;
    }

    /* Attachment styling */
    trix-editor .attachment {
        margin: 0.75rem 0 !important;
    }

    .trix-content ul,
    .trix-content ol {
        margin: 0.75rem 0 !important;
        padding-left: 1.5rem !important;
    }

    .trix-content ul {
        list-style-type: disc !important;
    }

    .trix-content ol {
        list-style-type: decimal !important;
    }

    .trix-content li {
        display: list-item !important;
    }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @include('admin.partials.page-header', [
        'title' => 'Tambah Materi (Modul)',
        'eyebrow' => 'E-Learning',
        'subtitle' => 'Kursus: ' . $course->title,
        'icon' => 'note_add',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.courses.index') }}" class="px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-900 text-rose-700 dark:text-rose-400 px-6 py-4 rounded-2xl space-y-1">
        <div class="flex items-center gap-3 mb-2">
            <span class="material-symbols-outlined">error</span>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-xs font-medium ml-8">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.courses.modules.store', $course) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6">
        @csrf
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Judul Modul</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi Singkat</label>
            <input type="text" name="description" value="{{ old('description') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Detail Materi / Isi</label>
            <div class="trix-wrapper">
                <input id="content" type="hidden" name="content" value="{{ old('content') }}">
                <trix-editor input="content" placeholder="Tuliskan isi materi di sini..."></trix-editor>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 font-medium">Gunakan toolbar di atas untuk formatting: <strong>Bold</strong>, <em>Italic</em>, List, Heading, dll.</p>
        </div>
        <div id="questionsNoticeCard" class="hidden p-5 rounded-2xl bg-orange-50/90 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-900/50 flex items-start sm:items-center gap-3.5 text-orange-900 dark:text-orange-300">
            <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-500/20">
                <span class="material-symbols-outlined text-xl">quiz</span>
            </div>
            <div class="text-xs leading-relaxed">
                <span class="font-bold block text-sm mb-0.5 text-orange-950 dark:text-orange-200">Pembuatan Soal per Modul</span>
                Setelah Anda menekan tombol <strong>"Simpan Modul"</strong> di bawah, sistem akan **langsung mengarahkan Anda ke Halaman Pembuatan Soal** (Pilihan Ganda, Essay, Coding, PDF) khusus untuk modul ini.
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tipe</label>
                <select name="type" id="moduleType" required onchange="toggleModuleInput(this.value)" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
                    <option value="pdf" @selected(old('type') === 'pdf')>Dokumen (PDF)</option>
                    <option value="video" @selected(old('type') === 'video')>Video (YouTube Link)</option>
                    <option value="link" @selected(old('type') === 'link')>Link Eksternal</option>
                    <option value="text" @selected(old('type') === 'text')>Materi Teks (Artikel)</option>
                    <option value="assignment" @selected(old('type') === 'assignment')>Tugas (Pengumpulan)</option>
                    <option value="quiz" @selected(old('type') === 'quiz')>Kuis / Soal</option>
                    <option value="coding" @selected(old('type') === 'coding')>Latihan Coding</option>
                </select>
            </div>
            <div id="fileInputContainer">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">File (Maks 20MB)</label>
                <input type="file" name="file" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all">
            </div>
            <div id="linkInputContainer" class="hidden">
                <label id="linkLabel" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">URL Link</label>
                <input type="url" name="link" value="{{ old('link') }}" placeholder="https://..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.courses.index') }}" class="px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">Batal</a>
            <button type="submit" class="px-6 py-3 bg-orange-600 text-white rounded-2xl font-bold shadow-lg shadow-orange-500/25 hover:bg-orange-700 hover:shadow-orange-500/40 transition-all">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Modul
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
    function toggleModuleInput(type) {
        const fileContainer = document.getElementById('fileInputContainer');
        const linkContainer = document.getElementById('linkInputContainer');
        const linkLabel = document.getElementById('linkLabel');
        const questionsNoticeCard = document.getElementById('questionsNoticeCard');
        const fileInput = fileContainer.querySelector('input[type="file"]');

        if (questionsNoticeCard) {
            if (['assignment', 'quiz', 'coding'].includes(type)) {
                questionsNoticeCard.classList.remove('hidden');
            } else {
                questionsNoticeCard.classList.add('hidden');
            }
        }

        if (type === 'link' || type === 'video') {
            fileContainer.classList.add('hidden');
            linkContainer.classList.remove('hidden');
            linkLabel.textContent = type === 'video' ? 'Link YouTube' : 'URL Link';
        } else if (type === 'text' || type === 'assignment' || type === 'quiz' || type === 'coding') {
            fileContainer.classList.add('hidden');
            linkContainer.classList.add('hidden');
        } else {
            fileContainer.classList.remove('hidden');
            linkContainer.classList.add('hidden');
            fileInput.setAttribute('accept', '.pdf');
        }
    }

    // Add custom code button for Trix
    document.addEventListener('trix-initialize', function(e) {
        const toolbar = e.target.toolbarElement;
        
        // Create code block button
        const blockGroup = toolbar.querySelector('.trix-button-group--block-tools');
        if (blockGroup && !toolbar.querySelector('[data-trix-attribute="code"]')) {
            const codeButton = document.createElement('button');
            codeButton.type = 'button';
            codeButton.className = 'trix-button trix-button--icon';
            codeButton.setAttribute('data-trix-attribute', 'code');
            codeButton.setAttribute('title', 'Code Block');
            codeButton.setAttribute('tabindex', '-1');
            codeButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
            `;
            blockGroup.appendChild(codeButton);
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleModuleInput(document.getElementById('moduleType').value);
        
        // Initialize highlight.js
        hljs.highlightAll();
        
        // Ensure Trix initializes properly
        const editor = document.querySelector('trix-editor');
        if (editor) {
            editor.addEventListener('trix-initialize', function() {
                console.log('Trix editor initialized');
            });
        }
    });
</script>
@endpush