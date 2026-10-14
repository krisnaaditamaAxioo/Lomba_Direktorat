@extends('layouts.app')

@section('title', 'Ajukan Kartu Siswa')

@section('content')
@php
    $field = 'w-full rounded-xl border bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-800 dark:text-slate-100 placeholder:text-slate-400 transition focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500';
    $ok  = 'border-slate-200 dark:border-slate-700';
    $bad = 'border-rose-400 dark:border-rose-500';
    $label = 'block text-sm font-bold text-slate-700 dark:text-slate-200';
@endphp

<div class="mx-auto w-full max-w-3xl space-y-6 sm:space-y-8">

    @include('admin.partials.page-header', [
        'title' => 'Ajukan Kartu untuk Siswa',
        'eyebrow' => 'Smart EduHub Wallet',
        'subtitle' => 'Admin dapat membuat pengajuan kartu atas nama siswa.',
        'icon' => 'badge',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.finance.requests') }}"
           class="inline-flex items-center gap-1.5 rounded-lg py-1 pr-2 text-sm font-bold text-slate-500 transition hover:text-orange-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali ke Pengajuan Kartu
        </a>
    </div>

    {{-- Ringkasan error --}}
    @if($errors->any())
    <div role="alert" class="flex gap-3 rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 p-4 sm:p-5 text-rose-700 dark:text-rose-300">
        <span class="material-symbols-outlined shrink-0 text-xl">error</span>
        <div class="min-w-0">
            <p class="text-sm font-bold">Pengajuan belum bisa disimpan. Periksa isian berikut:</p>
            <ul class="mt-1.5 list-disc list-inside space-y-0.5 text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form id="cardRequestForm" action="{{ route('admin.finance.requests.store') }}" method="POST" enctype="multipart/form-data"
          class="overflow-hidden rounded-3xl border border-slate-200/70 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        @csrf

        {{-- Siswa --}}
        <fieldset class="p-5 sm:p-8 space-y-4">
            <legend class="flex items-center gap-2 text-base font-black text-slate-900 dark:text-white">
                <span class="material-symbols-outlined text-xl text-orange-600">school</span>
                Data siswa
            </legend>

            <div class="space-y-2">
                <label for="student_id" class="{{ $label }}">Siswa</label>
                <div class="relative">
                    <select id="student_id" name="student_id" required
                        class="{{ $field }} {{ $errors->has('student_id') ? $bad : $ok }} appearance-none pr-11">
                        <option value="">Pilih siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nama_lengkap }} - {{ $student->nis_siswa ?: ($student->nisn ?: 'Tanpa NIS') }}
                            </option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">expand_more</span>
                </div>
                @error('student_id')<p class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>
        </fieldset>

        <hr class="border-slate-100 dark:border-slate-800">

        {{-- Orang tua --}}
        <fieldset class="p-5 sm:p-8 space-y-4">
            <legend class="flex items-center gap-2 text-base font-black text-slate-900 dark:text-white">
                <span class="material-symbols-outlined text-xl text-orange-600">family_restroom</span>
                Data orang tua / wali
            </legend>

            <div class="grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="parent_name" class="{{ $label }}">Nama orang tua</label>
                    <input id="parent_name" type="text" name="parent_name" value="{{ old('parent_name') }}" required maxlength="255"
                        autocomplete="name" placeholder="Nama orang tua / wali"
                        class="{{ $field }} {{ $errors->has('parent_name') ? $bad : $ok }}">
                    @error('parent_name')<p class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="parent_phone" class="{{ $label }}">No. telepon orang tua</label>
                    <input id="parent_phone" type="tel" inputmode="tel" name="parent_phone" value="{{ old('parent_phone') }}" required maxlength="20"
                        autocomplete="tel" placeholder="08xxxxxxxxxx"
                        class="{{ $field }} {{ $errors->has('parent_phone') ? $bad : $ok }}">
                    @error('parent_phone')<p class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>

        <hr class="border-slate-100 dark:border-slate-800">

        {{-- Dokumen --}}
        <fieldset class="p-5 sm:p-8 space-y-4">
            <legend class="flex items-center gap-2 text-base font-black text-slate-900 dark:text-white">
                <span class="material-symbols-outlined text-xl text-orange-600">description</span>
                Dokumen
            </legend>

            <div class="space-y-2">
                <label for="statement_file" class="{{ $label }}">
                    Surat pernyataan <span class="font-medium text-slate-400">(opsional)</span>
                </label>

                <div id="dropzone"
                     class="relative flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed px-4 py-8 text-center transition
                            focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10
                            {{ $errors->has('statement_file') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }}
                            bg-slate-50/60 dark:bg-slate-800/40 hover:border-orange-400 hover:bg-orange-50/40 dark:hover:bg-orange-500/5">
                    <input id="statement_file" type="file" name="statement_file" accept=".pdf,.jpg,.jpeg,.png"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                        <span id="dzIcon" class="material-symbols-outlined text-2xl">upload_file</span>
                    </span>
                    <p id="dzTitle" class="text-sm font-bold text-slate-700 dark:text-slate-200">Pilih file atau seret ke sini</p>
                    <p id="dzHint" class="text-xs text-slate-500 dark:text-slate-400">PDF, JPG, atau PNG. Maksimal 5 MB.</p>
                </div>

                <div id="dzFile" class="hidden items-center justify-between gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="material-symbols-outlined text-xl text-emerald-600">check_circle</span>
                        <div class="min-w-0">
                            <p id="dzName" class="truncate text-sm font-bold text-slate-800 dark:text-slate-100"></p>
                            <p id="dzSize" class="text-xs text-slate-500 dark:text-slate-400"></p>
                        </div>
                    </div>
                    <button type="button" id="dzClear"
                        class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                        aria-label="Hapus file">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <p id="dzError" class="hidden text-xs font-semibold text-rose-600 dark:text-rose-400"></p>
                @error('statement_file')<p class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>
        </fieldset>

        {{-- Aksi --}}
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/30 px-5 py-4 sm:flex-row sm:justify-end sm:px-8">
            <a href="{{ route('admin.finance.requests') }}"
               class="inline-flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 px-5 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 transition hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                Batal
            </a>
            <button id="submitBtn" type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-bold text-white shadow-sm shadow-orange-600/20 transition hover:bg-orange-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-70">
                <span id="submitIcon" class="material-symbols-outlined text-lg">add_card</span>
                <span id="submitText">Buat pengajuan</span>
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    var MAX = 5 * 1024 * 1024;
    var form = document.getElementById('cardRequestForm');
    var input = document.getElementById('statement_file');
    var box = document.getElementById('dzFile');
    var nameEl = document.getElementById('dzName');
    var sizeEl = document.getElementById('dzSize');
    var errEl = document.getElementById('dzError');

    function fmt(b) { return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; }
    function reset() { input.value = ''; box.classList.add('hidden'); box.classList.remove('flex'); }

    input.addEventListener('change', function () {
        errEl.classList.add('hidden');
        var f = input.files && input.files[0];
        if (!f) { reset(); return; }
        if (f.size > MAX) {
            reset();
            errEl.textContent = 'Ukuran file ' + fmt(f.size) + ' melebihi batas 5 MB.';
            errEl.classList.remove('hidden');
            return;
        }
        nameEl.textContent = f.name;
        sizeEl.textContent = fmt(f.size);
        box.classList.remove('hidden');
        box.classList.add('flex');
    });

    document.getElementById('dzClear').addEventListener('click', function () {
        errEl.classList.add('hidden');
        reset();
    });

    // Cegah klik ganda saat mengirim
    form.addEventListener('submit', function () {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        document.getElementById('submitIcon').textContent = 'progress_activity';
        document.getElementById('submitIcon').classList.add('animate-spin');
        document.getElementById('submitText').textContent = 'Menyimpan...';
    });
})();
</script>
@endsection