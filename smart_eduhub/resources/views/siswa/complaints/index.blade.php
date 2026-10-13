@extends('layouts.siswa')

@section('title', 'Aduan Saya')

@push('styles')
<style>
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* hidden attribute must win over flex/grid utilities */
    .complaint-card[hidden], #filterEmpty[hidden], #clearFilter[hidden] { display: none !important; }

    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* Whole card is tappable through the title link */
    .stretched::after { content: ''; position: absolute; inset: 0; }

    /* Summary segments (double as filters) */
    .stat { color: #0f172a; transition: background .15s, color .15s; }
    .stat-label { color: #475569; }
    .stat:hover { background: #f8fafc; }
    .stat.is-active { background: #0f172a; color: #fff; }
    .stat.is-active .stat-label { color: #e2e8f0; }

    /* Delete confirmation: bottom sheet on phones, centered dialog from sm */
    .delete-dialog {
        width: 100%; max-width: 28rem; max-height: 100dvh;
        padding: 0; border: 0; background: transparent; overflow: visible;
    }
    .delete-dialog::backdrop { background: rgba(15, 23, 42, .5); backdrop-filter: blur(4px); }
    .delete-sheet { padding-bottom: calc(1.5rem + env(safe-area-inset-bottom)); }
    @media (max-width: 639.98px) {
        .delete-dialog { margin: auto auto 0; }
    }
    .delete-dialog[open] .delete-sheet { animation: sheet-in .28s cubic-bezier(.32, .72, 0, 1); }
    @keyframes sheet-in { from { transform: translateY(24px); opacity: 0; } to { transform: none; opacity: 1; } }
    @media (prefers-reduced-motion: reduce) {
        .delete-dialog[open] .delete-sheet { animation: none; }
    }
</style>
@endpush

@section('content')
@php
    // Works whether $complaints is a paginator or a collection.
    $complaintList = $complaints instanceof \Illuminate\Pagination\AbstractPaginator ? collect($complaints->items()) : collect($complaints);

    $statusMeta = [
        'pending'     => ['label' => 'Menunggu', 'icon' => 'schedule',    'pill' => 'bg-amber-50 text-amber-800 border-amber-200',   'dot' => 'bg-amber-500'],
        'in_progress' => ['label' => 'Diproses', 'icon' => 'autorenew',   'pill' => 'bg-sky-50 text-sky-800 border-sky-200',         'dot' => 'bg-sky-500'],
        'resolved'    => ['label' => 'Selesai',  'icon' => 'check_circle','pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'],
        'rejected'    => ['label' => 'Ditolak',  'icon' => 'cancel',      'pill' => 'bg-rose-50 text-rose-800 border-rose-200',      'dot' => 'bg-rose-500'],
    ];

    $keyOf = fn ($c) => in_array($c->status, ['pending', 'in_progress', 'resolved']) ? $c->status : 'rejected';

    $counts = ['all' => $complaintList->count()];
    foreach ($statusMeta as $key => $_) {
        $counts[$key] = $complaintList->filter(fn ($c) => $keyOf($c) === $key)->count();
    }
@endphp

<div class="w-full space-y-5 sm:space-y-6">

    {{-- Header + summary (kept outside space-y so the card can overlap the header) --}}
    <div>
        <header class="relative -mx-4 -mt-4 overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pt-8 text-white shadow-xl shadow-[#EB4325]/15 sm:px-6 lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-6 {{ $counts['all'] > 0 ? 'pb-16' : 'pb-8' }}">
            {{-- Abstract circular glow overlays --}}
            <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/5" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-10 left-16 h-28 w-28 rounded-full bg-white/5" aria-hidden="true"></div>

            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <a href="{{ route('siswa.dashboard') }}" aria-label="Kembali ke dashboard"
                       class="focus-ring flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/15 backdrop-blur-md transition active:scale-95 hover:bg-white/25">
                        <span class="material-symbols-outlined text-white">arrow_back</span>
                    </a>
                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-black tracking-tight sm:text-2xl">Aduan Saya</h1>
                        <p class="mt-0.5 text-xs leading-snug text-white/85 sm:text-[13px]">Kelola aduan dan laporan Anda</p>
                    </div>
                </div>

                {{-- On phones this action lives in the floating button instead --}}
                <a href="{{ route('siswa.complaints.create') }}"
                   class="focus-ring hidden h-11 shrink-0 items-center gap-2 rounded-xl bg-white px-4 text-sm font-black text-[#C2331A] shadow-sm transition hover:bg-orange-50 active:scale-95 sm:inline-flex">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Aduan baru
                </a>
            </div>
        </header>

        @if($counts['all'] > 0)
            <section aria-label="Ringkasan aduan" class="relative z-10 -mt-10 lg:max-w-xl">
                <div class="grid grid-cols-4 gap-1 rounded-3xl border border-slate-100 bg-white p-1.5 shadow-lg shadow-slate-900/5">
                    @foreach($statusMeta as $key => $sm)
                        <button type="button" data-filter="{{ $key }}" data-label="{{ $sm['label'] }}" aria-pressed="false"
                                class="stat focus-ring flex min-h-[64px] flex-col items-center justify-center gap-1.5 rounded-[1.1rem] px-1 py-3">
                            <span class="text-xl font-extrabold leading-none tabular-nums">{{ $counts[$key] }}</span>
                            <span class="stat-label flex items-center gap-1 text-[11px] font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full {{ $sm['dot'] }}" aria-hidden="true"></span>
                                {{ $sm['label'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
                @if($complaints instanceof \Illuminate\Pagination\AbstractPaginator && $complaints->hasPages())
                    <p class="mt-2 px-2 text-xs text-slate-500">Ringkasan untuk aduan di halaman ini.</p>
                @endif
            </section>
        @endif
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3.5 text-emerald-900">
            <span class="material-symbols-outlined mt-0.5 text-emerald-600" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <p class="flex-1 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
            <button type="button" aria-label="Tutup pemberitahuan" onclick="this.closest('[role=status]').remove()"
                    class="focus-ring -m-1 grid h-9 w-9 shrink-0 place-items-center rounded-full text-emerald-700 hover:bg-emerald-100">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-rose-900">
            <span class="material-symbols-outlined mt-0.5 text-rose-600" style="font-variation-settings: 'FILL' 1;">error</span>
            <p class="flex-1 text-sm font-medium leading-relaxed">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Complaints list --}}
    <section aria-labelledby="listTitle" class="space-y-3">
        @if($counts['all'] > 0)
            <div class="flex items-center justify-between gap-3 px-1">
                <h2 class="text-base font-extrabold tracking-tight text-slate-900">
                    <span id="listTitle">Semua aduan</span>
                    <span id="listCount" class="ml-1 text-sm font-semibold tabular-nums text-slate-500">{{ $counts['all'] }}</span>
                </h2>
                <button type="button" id="clearFilter" hidden
                        class="focus-ring -mr-2 inline-flex min-h-[40px] items-center rounded-full px-3 text-xs font-bold text-[#C2331A] hover:bg-orange-50">
                    Tampilkan semua
                </button>
            </div>
        @endif

        <div id="complaintList" class="grid gap-3 sm:gap-4 md:grid-cols-2 2xl:grid-cols-3">
            @forelse($complaints as $complaint)
                @php
                    $key = $keyOf($complaint);
                    $st  = $statusMeta[$key];
                @endphp

                <article class="complaint-card relative flex flex-col rounded-3xl border border-slate-100 bg-white p-4 transition hover:border-[#F6863B]/40 active:bg-slate-50 sm:p-5" data-status="{{ $key }}">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $st['pill'] }}">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">{{ $st['icon'] }}</span>
                            {{ $complaint->status_text ?: $st['label'] }}
                        </span>
                        <time datetime="{{ $complaint->created_at->toIso8601String() }}" class="shrink-0 text-xs tabular-nums text-slate-500">
                            {{ $complaint->created_at->translatedFormat('j M Y, H:i') }}
                        </time>
                    </div>

                    <h3 class="mt-3 text-[15px] font-bold leading-snug text-slate-900">
                        <a href="{{ route('siswa.complaints.show', $complaint) }}" class="stretched focus-ring rounded-md">{{ $complaint->title }}</a>
                    </h3>
                    <p class="clamp-2 mb-4 mt-1.5 text-[13px] leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($complaint->description, 100) }}</p>

                    <div class="relative z-10 mt-auto flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-slate-500">
                            Lihat detail
                            <span class="material-symbols-outlined text-lg" aria-hidden="true">chevron_right</span>
                        </span>
                        <button type="button"
                                data-delete-url="{{ route('siswa.complaints.destroy', $complaint) }}"
                                data-delete-title="{{ $complaint->title }}"
                                aria-label="Hapus aduan {{ $complaint->title }}"
                                class="focus-ring -mr-2 grid h-11 w-11 place-items-center rounded-full text-rose-500 transition hover:bg-rose-50 active:scale-95">
                            <span class="material-symbols-outlined text-xl" aria-hidden="true">delete</span>
                        </button>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center">
                    <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-slate-50 text-slate-300">
                        <span class="material-symbols-outlined text-3xl">inbox</span>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-800">Belum ada aduan</h3>
                    <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">Anda belum membuat aduan apa pun. Mulai dengan menulis aduan pertama Anda.</p>
                    <a href="{{ route('siswa.complaints.create') }}"
                       class="focus-ring mt-6 inline-flex h-12 items-center gap-2 rounded-2xl bg-[#F6863B] px-6 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-[#F3782A] active:scale-95">
                        <span class="material-symbols-outlined text-xl">add</span>
                        Buat aduan pertama
                    </a>
                </div>
            @endforelse

            {{-- Shown when a filter has no matches --}}
            <div id="filterEmpty" hidden class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-10 text-center text-[13px] text-slate-500">
                Tidak ada aduan dengan status ini.
            </div>
        </div>

        @if(method_exists($complaints, 'links'))
            <div class="overflow-x-auto pt-2">{{ $complaints->links() }}</div>
        @endif
    </section>

    {{-- Space so the last card clears the bottom nav and the button on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>

{{-- Mobile: extended floating button --}}
<div class="fixed bottom-20 right-4 z-40 sm:hidden" style="margin-bottom: env(safe-area-inset-bottom);">
    <a href="{{ route('siswa.complaints.create') }}"
       class="focus-ring inline-flex h-12 items-center gap-2 rounded-full bg-[#F6863B] pl-4 pr-5 text-sm font-bold text-slate-900 shadow-lg shadow-[#F6863B]/40 transition hover:bg-[#F3782A] active:scale-95">
        <span class="material-symbols-outlined text-xl">add</span>
        Aduan baru
    </a>
</div>

{{-- Delete confirmation --}}
<dialog id="deleteDialog" class="delete-dialog" aria-labelledby="deleteTitle" aria-describedby="deleteDesc">
    <form id="deleteForm" method="POST" class="delete-sheet rounded-t-[28px] bg-white p-5 shadow-2xl sm:rounded-3xl">
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
                    <span id="deleteName" class="break-words font-semibold text-slate-800"></span>
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
@endsection

@push('scripts')
<script>
(function () {
    /* ----- Status filter (summary segments) ----- */
    const stats = document.querySelectorAll('[data-filter]');
    const cards = document.querySelectorAll('.complaint-card');
    const emptyMsg = document.getElementById('filterEmpty');
    const listTitle = document.getElementById('listTitle');
    const listCount = document.getElementById('listCount');
    const clearBtn  = document.getElementById('clearFilter');

    function applyFilter(filter, label) {
        let visible = 0;
        stats.forEach(function (s) {
            const on = s.dataset.filter === filter;
            s.classList.toggle('is-active', on);
            s.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        cards.forEach(function (card) {
            const show = filter === 'all' || card.dataset.status === filter;
            card.hidden = !show;
            if (show) visible++;
        });
        if (emptyMsg) emptyMsg.hidden = visible !== 0 || cards.length === 0;
        if (listTitle) listTitle.textContent = filter === 'all' ? 'Semua aduan' : 'Aduan ' + label.toLowerCase();
        if (listCount) listCount.textContent = visible;
        if (clearBtn) clearBtn.hidden = filter === 'all';
    }

    stats.forEach(function (s) {
        s.addEventListener('click', function () {
            // Tapping the active segment again clears the filter
            if (s.classList.contains('is-active')) applyFilter('all', '');
            else applyFilter(s.dataset.filter, s.dataset.label);
        });
    });
    if (clearBtn) clearBtn.addEventListener('click', function () { applyFilter('all', ''); });

    /* ----- Delete confirmation ----- */
    const dialog  = document.getElementById('deleteDialog');
    const form    = document.getElementById('deleteForm');
    const nameEl  = document.getElementById('deleteName');
    const confirmBtn = document.getElementById('deleteConfirm');
    const cancelBtn  = document.getElementById('deleteCancel');

    document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = btn.dataset.deleteUrl;
            const title = btn.dataset.deleteTitle;

            // Fallback for browsers without <dialog>
            if (typeof dialog.showModal !== 'function') {
                if (confirm('Apakah Anda yakin ingin menghapus aduan ini?')) {
                    form.action = url;
                    form.submit();
                }
                return;
            }
            form.action = url;
            nameEl.textContent = '“' + title + '”';
            confirmBtn.disabled = false;
            dialog.showModal();
        });
    });

    if (dialog) {
        cancelBtn.addEventListener('click', function () { dialog.close(); });
        // Click on the backdrop closes the dialog
        dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); });
        form.addEventListener('submit', function () { confirmBtn.disabled = true; });
    }
    window.addEventListener('pageshow', function () { if (confirmBtn) confirmBtn.disabled = false; });
})();
</script>
@endpush