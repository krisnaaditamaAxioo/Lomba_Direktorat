@extends('layouts.siswa')

@section('title', 'Buat Aduan Baru')

@push('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    .field {
        width: 100%;
        min-height: 48px;
        padding: .75rem .9rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 1rem; /* 16px on phones prevents iOS zoom on focus */
        line-height: 1.5;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .field::placeholder { color: #94a3b8; }
    .field:focus { border-color: #F6863B; background: #fff; box-shadow: 0 0 0 4px rgba(246, 134, 59, .15); }
    .field.is-invalid { border-color: #f43f5e; background: #fff; }
    .field.is-invalid:focus { box-shadow: 0 0 0 4px rgba(244, 63, 94, .15); }
    @media (min-width: 640px) { .field { font-size: .9375rem; } }

    /* Photo dropzone */
    #proof_photo:focus-visible + .drop { outline: 2px solid #EB4325; outline-offset: 2px; }

    #proofPreview[hidden], #proofDrop[hidden] { display: none !important; }
</style>
@endpush

@section('content')
<div class="w-full">

    {{-- Curved gradient header --}}
    <header class="relative -mx-4 -mt-4 overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pb-16 pt-8 text-white shadow-xl shadow-[#EB4325]/15 sm:px-6 lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pb-14 lg:pt-6">
        <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/5" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-10 left-16 h-28 w-28 rounded-full bg-white/5" aria-hidden="true"></div>

        <div class="relative z-10 flex items-center gap-3 sm:gap-4">
            <a href="{{ route('siswa.complaints.index') }}" aria-label="Kembali ke daftar aduan"
               class="focus-ring flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/15 backdrop-blur-md transition hover:bg-white/25 active:scale-95">
                <span class="material-symbols-outlined text-white">arrow_back</span>
            </a>
            <div class="min-w-0">
                <h1 class="text-xl font-black tracking-tight sm:text-2xl">Buat Aduan Baru</h1>
                <p class="mt-0.5 text-xs leading-snug text-white/85 sm:text-[13px]">Laporkan masalah atau keluhan Anda</p>
            </div>
        </div>
    </header>

    {{-- Form card overlaps the header; tips sit beside it on desktop --}}
    <div class="relative z-10 -mt-10 grid gap-5 md:mx-auto md:max-w-2xl lg:max-w-none lg:grid-cols-3 lg:gap-6">

        <form id="complaintForm" action="{{ route('siswa.complaints.store') }}" method="POST" enctype="multipart/form-data"
              class="space-y-5 rounded-3xl border border-slate-100 bg-white p-5 shadow-lg shadow-slate-900/5 sm:p-6 lg:col-span-2">
            @csrf

            @if($errors->any())
                <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-rose-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-600" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">error</span>
                        <p class="text-sm font-semibold">Aduan belum terkirim</p>
                    </div>
                    <ul class="mt-1.5 list-disc space-y-0.5 pl-9 text-[13px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Title --}}
            <div>
                <label for="title" class="mb-1.5 block text-xs font-semibold text-slate-600">Judul aduan</label>
                <input type="text" id="title" name="title" required value="{{ old('title') }}" autocomplete="off" enterkeyhint="next"
                       placeholder="Contoh: AC kelas XI-2 tidak menyala"
                       aria-describedby="titleHelp"
                       @error('title') aria-invalid="true" @enderror
                       class="field @error('title') is-invalid @enderror">
                @error('title')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
                <p id="titleHelp" class="mt-1.5 text-xs text-slate-500">Tulis singkat dan spesifik agar mudah dipahami.</p>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi</label>
                <textarea id="description" name="description" required rows="6"
                          placeholder="Ceritakan apa yang terjadi, kapan, dan di mana"
                          aria-describedby="descHelp"
                          @error('description') aria-invalid="true" @enderror
                          class="field resize-y @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
                <p id="descHelp" class="mt-1.5 text-xs text-slate-500">Semakin jelas kronologinya, semakin mudah aduan ditinjau.</p>
            </div>

            {{-- Photo --}}
            <div>
                <span class="mb-1.5 block text-xs font-semibold text-slate-600">Bukti foto <span class="font-medium text-slate-400">(opsional)</span></span>

                <input type="file" id="proof_photo" name="proof_photo" accept="image/*" class="sr-only" onchange="previewProof(this)">

                <label for="proof_photo" id="proofDrop"
                       class="drop flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-4 transition hover:border-[#F6863B]/60 hover:bg-[#FFF3EA]/60">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-slate-400 shadow-sm">
                        <span class="material-symbols-outlined" aria-hidden="true">add_a_photo</span>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-800">Tambahkan foto</span>
                        <span class="block text-xs text-slate-500">Ambil foto langsung atau pilih dari galeri</span>
                    </span>
                </label>

                <div id="proofPreview" hidden class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                    <img id="proofImg" alt="Pratinjau foto bukti" class="max-h-60 w-full object-cover">
                    <div class="flex items-center justify-between gap-2 p-2.5 pl-3.5">
                        <div class="min-w-0">
                            <p id="proofName" class="truncate text-sm font-semibold text-slate-800"></p>
                            <p id="proofSize" class="text-xs tabular-nums text-slate-500"></p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <label for="proof_photo"
                                   class="focus-ring inline-flex min-h-[40px] cursor-pointer items-center rounded-full px-3 text-xs font-bold text-[#C2331A] hover:bg-orange-50">Ganti</label>
                            <button type="button" onclick="removeProof()" aria-label="Hapus foto"
                                    class="focus-ring grid h-10 w-10 place-items-center rounded-full text-rose-500 hover:bg-rose-50">
                                <span class="material-symbols-outlined text-xl" aria-hidden="true">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
                @error('proof_photo')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('siswa.complaints.index') }}"
                   class="focus-ring inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-sm font-bold text-slate-700 transition hover:bg-slate-50 active:scale-[.98]">
                    Batal
                </a>
                <button type="submit" id="complaintSubmit"
                        class="focus-ring inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#F6863B] px-7 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-[#F3782A] active:scale-[.98] disabled:opacity-60">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">send</span>
                    <span class="label">Kirim aduan</span>
                </button>
            </div>
        </form>

        {{-- Tips (desktop only; phones get inline helper text) --}}
        <aside class="hidden self-start rounded-3xl border border-[#FBDCC4] bg-[#FFF3EA] p-6 lg:sticky lg:top-6 lg:block">
            <h2 class="text-base font-extrabold tracking-tight text-slate-900">Tips menulis aduan</h2>
            <ul class="mt-4 space-y-4">
                <li class="flex gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#F6863B] shadow-sm" aria-hidden="true">
                        <span class="material-symbols-outlined text-xl">title</span>
                    </span>
                    <p class="text-[13px] leading-relaxed text-slate-700"><span class="block font-bold text-slate-900">Judul yang spesifik</span>Sebutkan inti masalahnya dalam satu kalimat pendek.</p>
                </li>
                <li class="flex gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#F6863B] shadow-sm" aria-hidden="true">
                        <span class="material-symbols-outlined text-xl">location_on</span>
                    </span>
                    <p class="text-[13px] leading-relaxed text-slate-700"><span class="block font-bold text-slate-900">Waktu dan tempat</span>Tulis kapan dan di mana kejadiannya, serta siapa yang terlibat jika perlu.</p>
                </li>
                <li class="flex gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-[#F6863B] shadow-sm" aria-hidden="true">
                        <span class="material-symbols-outlined text-xl">photo_camera</span>
                    </span>
                    <p class="text-[13px] leading-relaxed text-slate-700"><span class="block font-bold text-slate-900">Lampirkan foto</span>Foto membantu memperjelas kondisi yang Anda laporkan.</p>
                </li>
            </ul>
        </aside>
    </div>

    {{-- Space so the form clears the bottom nav on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('proof_photo');
    const drop    = document.getElementById('proofDrop');
    const preview = document.getElementById('proofPreview');
    const img     = document.getElementById('proofImg');
    const nameEl  = document.getElementById('proofName');
    const sizeEl  = document.getElementById('proofSize');
    let objectUrl = null;

    function reset() {
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
        img.removeAttribute('src');
        preview.hidden = true;
        drop.hidden = false;
    }

    window.previewProof = function (el) {
        const file = el.files && el.files[0];
        if (!file) { reset(); return; }

        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        img.src = objectUrl;
        nameEl.textContent = file.name;
        sizeEl.textContent = file.size >= 1048576
            ? (file.size / 1048576).toFixed(1) + ' MB'
            : Math.max(1, Math.round(file.size / 1024)) + ' KB';
        drop.hidden = true;
        preview.hidden = false;
    };

    window.removeProof = function () {
        input.value = '';
        reset();
    };

    /* ----- Prevent double submit ----- */
    const form   = document.getElementById('complaintForm');
    const submit = document.getElementById('complaintSubmit');
    form.addEventListener('submit', function () {
        submit.disabled = true;
        submit.querySelector('.label').textContent = 'Mengirim…';
    });
    window.addEventListener('pageshow', function () {
        submit.disabled = false;
        submit.querySelector('.label').textContent = 'Kirim aduan';
    });
})();
</script>
@endpush