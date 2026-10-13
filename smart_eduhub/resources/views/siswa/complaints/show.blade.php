@extends('layouts.siswa')

@section('title', 'Detail Aduan')

@push('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* Delete confirmation: bottom sheet on phones, centered dialog from sm */
    .delete-dialog {
        width: 100%; max-width: 28rem; max-height: 100dvh;
        padding: 0; border: 0; background: transparent; overflow: visible;
    }
    .delete-dialog::backdrop, .photo-dialog::backdrop { background: rgba(15, 23, 42, .55); backdrop-filter: blur(4px); }
    .delete-sheet { padding-bottom: calc(1.5rem + env(safe-area-inset-bottom)); }
    @media (max-width: 639.98px) { .delete-dialog { margin: auto auto 0; } }
    .delete-dialog[open] .delete-sheet { animation: sheet-in .28s cubic-bezier(.32, .72, 0, 1); }
    @keyframes sheet-in { from { transform: translateY(24px); opacity: 0; } to { transform: none; opacity: 1; } }

    /* Photo lightbox */
    .photo-dialog {
        width: min(96vw, 64rem); max-width: none; max-height: 96dvh;
        padding: 0; border: 0; background: transparent; overflow: visible;
    }
    .photo-dialog::backdrop { background: rgba(2, 6, 23, .85); }
    .photo-dialog img { display: block; margin: 0 auto; max-width: 100%; max-height: 84dvh; border-radius: 1rem; }

    @media (prefers-reduced-motion: reduce) {
        .delete-dialog[open] .delete-sheet { animation: none; }
    }
</style>
@endpush

@section('content')
@php
    $statusMeta = [
        'pending'     => ['label' => 'Menunggu', 'icon' => 'schedule',     'pill' => 'bg-amber-50 text-amber-800 border-amber-200',       'reply' => 'border-sky-200 bg-sky-50 text-sky-900'],
        'in_progress' => ['label' => 'Diproses', 'icon' => 'autorenew',    'pill' => 'bg-sky-50 text-sky-800 border-sky-200',             'reply' => 'border-sky-200 bg-sky-50 text-sky-900'],
        'resolved'    => ['label' => 'Selesai',  'icon' => 'check_circle', 'pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'reply' => 'border-emerald-200 bg-emerald-50 text-emerald-900'],
        'rejected'    => ['label' => 'Ditolak',  'icon' => 'cancel',       'pill' => 'bg-rose-50 text-rose-800 border-rose-200',          'reply' => 'border-rose-200 bg-rose-50 text-rose-900'],
    ];

    $key = in_array($complaint->status, ['pending', 'in_progress', 'resolved']) ? $complaint->status : 'rejected';
    $st  = $statusMeta[$key];
    $fmt = fn ($date) => $date->translatedFormat('j M Y, H:i');

    // Progress steps derived from the status
    $steps = [];
    $steps[] = [
        'label' => 'Aduan terkirim',
        'note'  => $fmt($complaint->created_at),
        'state' => $key === 'pending' ? 'current' : 'done',
    ];
    if ($key === 'rejected') {
        $steps[] = [
            'label' => 'Ditolak',
            'note'  => $complaint->handled_at ? $fmt($complaint->handled_at) : ($complaint->response ? 'Lihat tanggapan untuk alasannya' : ''),
            'state' => 'rejected',
        ];
    } else {
        $steps[] = [
            'label' => 'Sedang diproses',
            'note'  => $key === 'pending' ? 'Belum dimulai' : ($key === 'in_progress' ? 'Aduan sedang ditangani' : 'Sudah ditinjau'),
            'state' => $key === 'pending' ? 'upcoming' : ($key === 'in_progress' ? 'current' : 'done'),
        ];
        $steps[] = [
            'label' => 'Selesai',
            'note'  => $key === 'resolved' ? ($complaint->handled_at ? $fmt($complaint->handled_at) : 'Aduan telah selesai') : 'Belum selesai',
            'state' => $key === 'resolved' ? 'done' : 'upcoming',
        ];
    }

    $circle = [
        'done'     => 'bg-emerald-500 text-white',
        'current'  => 'bg-[#F6863B] text-slate-900',
        'upcoming' => 'border-2 border-slate-200 bg-white text-slate-300',
        'rejected' => 'bg-rose-500 text-white',
    ];
    $circleIcon = ['done' => 'check', 'current' => 'schedule', 'upcoming' => '', 'rejected' => 'close'];
@endphp

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
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-black tracking-tight sm:text-2xl">Detail Aduan</h1>
                <p class="mt-0.5 text-xs leading-snug text-white/85 sm:text-[13px]">Lihat status dan tanggapan aduan Anda</p>
            </div>
            <button type="button" onclick="openDelete()" aria-label="Hapus aduan"
                    class="focus-ring inline-flex h-11 shrink-0 items-center justify-center gap-1.5 rounded-xl border border-white/10 bg-rose-500/25 px-3 text-sm font-bold text-rose-50 backdrop-blur-md transition hover:bg-rose-500/40 active:scale-95">
                <span class="material-symbols-outlined text-xl" aria-hidden="true">delete</span>
                <span class="hidden sm:inline">Hapus</span>
            </button>
        </div>
    </header>

    {{-- Content overlaps the header --}}
    <div class="relative z-10 -mt-10 grid gap-5 md:mx-auto md:max-w-2xl lg:max-w-none lg:grid-cols-5 lg:gap-6">

        {{-- Main column: complaint + response --}}
        <div class="space-y-5 lg:col-span-3">

            <article class="rounded-3xl border border-slate-100 bg-white p-5 shadow-lg shadow-slate-900/5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                    <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $st['pill'] }}">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">{{ $st['icon'] }}</span>
                        {{ $complaint->status_text ?: $st['label'] }}
                    </span>
                    <time datetime="{{ $complaint->created_at->toIso8601String() }}" class="text-xs tabular-nums text-slate-500">
                        {{ $fmt($complaint->created_at) }}
                    </time>
                </div>

                <h2 class="mt-4 break-words text-xl font-extrabold leading-snug tracking-tight text-slate-900 sm:text-2xl">{{ $complaint->title }}</h2>

                <p class="mt-3 whitespace-pre-line break-words text-[15px] leading-relaxed text-slate-700">{{ $complaint->description }}</p>

                @if($complaint->proof_photo)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <h3 class="mb-2.5 text-xs font-semibold text-slate-600">Bukti foto</h3>
                        <button type="button" onclick="openPhoto()" aria-label="Perbesar foto bukti"
                                class="focus-ring group relative block w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img src="{{ asset('storage/' . $complaint->proof_photo) }}" alt="Bukti aduan" loading="lazy"
                                 class="max-h-72 w-full object-cover sm:max-h-96">
                            <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1 rounded-full bg-slate-900/70 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                                <span class="material-symbols-outlined text-base" aria-hidden="true">zoom_in</span>
                                Perbesar
                            </span>
                        </button>
                    </div>
                @endif
            </article>

            {{-- Response --}}
            @if($complaint->response)
                <section aria-labelledby="responseTitle" class="rounded-3xl border p-5 sm:p-6 {{ $st['reply'] }}">
                    <h3 id="responseTitle" class="flex items-center gap-2 text-sm font-bold">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">forum</span>
                        Tanggapan
                    </h3>
                    <p class="mt-3 whitespace-pre-line break-words text-sm leading-relaxed">{{ $complaint->response }}</p>
                    @if($complaint->handled_at)
                        <p class="mt-3 text-xs tabular-nums opacity-80">Ditangani pada {{ $fmt($complaint->handled_at) }}</p>
                    @endif
                </section>
            @else
                <section class="flex items-start gap-3 rounded-3xl border border-dashed border-slate-200 bg-white p-5 sm:p-6">
                    <span class="material-symbols-outlined mt-0.5 text-slate-300" aria-hidden="true">forum</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700">Belum ada tanggapan</h3>
                        <p class="mt-0.5 text-[13px] leading-relaxed text-slate-500">Tanggapan akan muncul di sini setelah aduan Anda ditinjau.</p>
                    </div>
                </section>
            @endif
        </div>

        {{-- Side column: progress --}}
        <aside class="lg:col-span-2 lg:sticky lg:top-6 lg:self-start">
            <section aria-labelledby="progressTitle" class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                <h2 id="progressTitle" class="text-base font-extrabold tracking-tight text-slate-900">Perkembangan aduan</h2>

                <ol class="mt-5" aria-label="Tahapan aduan">
                    @foreach($steps as $i => $step)
                        @php
                            $next   = $steps[$i + 1]['state'] ?? null;
                            $lineOn = $step['state'] === 'done' && $next && $next !== 'upcoming';
                        @endphp
                        <li class="relative flex gap-3 {{ $loop->last ? '' : 'pb-6' }}" @if($step['state'] === 'current') aria-current="step" @endif>
                            @unless($loop->last)
                                <span class="absolute left-4 top-8 -ml-px h-[calc(100%-2rem)] w-0.5 {{ $lineOn ? 'bg-emerald-300' : 'bg-slate-200' }}" aria-hidden="true"></span>
                            @endunless

                            <span class="relative z-10 grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $circle[$step['state']] }}" aria-hidden="true">
                                @if($circleIcon[$step['state']])
                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1, 'wght' 600;">{{ $circleIcon[$step['state']] }}</span>
                                @endif
                            </span>

                            <div class="min-w-0 pt-0.5">
                                <p class="text-sm font-bold {{ $step['state'] === 'upcoming' ? 'text-slate-400' : 'text-slate-900' }}">{{ $step['label'] }}</p>
                                @if($step['note'])
                                    <p class="mt-0.5 text-xs tabular-nums {{ $step['state'] === 'upcoming' ? 'text-slate-400' : 'text-slate-500' }}">{{ $step['note'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </aside>
    </div>

    {{-- Space so the content clears the bottom nav on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>

{{-- Delete confirmation --}}
<dialog id="deleteDialog" class="delete-dialog" aria-labelledby="deleteTitle" aria-describedby="deleteDesc">
    <form id="deleteForm" action="{{ route('siswa.complaints.destroy', $complaint) }}" method="POST"
          class="delete-sheet rounded-t-[28px] bg-white p-5 shadow-2xl sm:rounded-3xl">
        @csrf
        @method('DELETE')
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>
        <div class="flex items-start gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-500" aria-hidden="true">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">delete</span>
            </div>
            <div class="min-w-0">
                <h2 id="deleteTitle" class="text-base font-extrabold text-slate-900">Hapus aduan ini?</h2>
                <p id="deleteDesc" class="mt-1 text-[13px] leading-relaxed text-slate-600">
                    <span class="break-words font-semibold text-slate-800">“{{ $complaint->title }}”</span>
                    akan dihapus dan tidak bisa dikembalikan.
                </p>
            </div>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-3">
            <button type="button" id="deleteCancel"
                    class="focus-ring h-12 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-700 transition hover:bg-slate-50 active:scale-[.98]">
                Batal
            </button>
            <button type="submit" id="deleteConfirm"
                    class="focus-ring h-12 rounded-2xl bg-rose-600 text-sm font-bold text-white transition hover:bg-rose-700 active:scale-[.98] disabled:opacity-60">
                Ya, hapus
            </button>
        </div>
    </form>
</dialog>

{{-- Photo lightbox --}}
@if($complaint->proof_photo)
    <dialog id="photoDialog" class="photo-dialog" aria-label="Foto bukti aduan">
        <div class="relative">
            <button type="button" id="photoClose" aria-label="Tutup foto"
                    class="focus-ring absolute -top-12 right-0 grid h-10 w-10 place-items-center rounded-full bg-white/15 text-white backdrop-blur-md hover:bg-white/25">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
            <img src="{{ asset('storage/' . $complaint->proof_photo) }}" alt="Bukti aduan (diperbesar)">
        </div>
    </dialog>
@endif
@endsection

@push('scripts')
<script>
(function () {
    /* ----- Delete confirmation ----- */
    const dialog     = document.getElementById('deleteDialog');
    const form       = document.getElementById('deleteForm');
    const confirmBtn = document.getElementById('deleteConfirm');
    const cancelBtn  = document.getElementById('deleteCancel');

    window.openDelete = function () {
        // Fallback for browsers without <dialog>
        if (typeof dialog.showModal !== 'function') {
            if (confirm('Apakah Anda yakin ingin menghapus aduan ini?')) form.submit();
            return;
        }
        confirmBtn.disabled = false;
        dialog.showModal();
    };

    cancelBtn.addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); });
    form.addEventListener('submit', function () { confirmBtn.disabled = true; });
    window.addEventListener('pageshow', function () { confirmBtn.disabled = false; });

    /* ----- Photo lightbox ----- */
    const photo = document.getElementById('photoDialog');
    window.openPhoto = function () {
        if (photo && typeof photo.showModal === 'function') photo.showModal();
        else if (photo) window.open(photo.querySelector('img').src, '_blank', 'noopener');
    };
    if (photo) {
        document.getElementById('photoClose').addEventListener('click', function () { photo.close(); });
        photo.addEventListener('click', function (e) { if (e.target === photo) photo.close(); });
    }
})();
</script>
@endpush