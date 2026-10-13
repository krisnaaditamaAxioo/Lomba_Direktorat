@extends('layouts.app')

@section('title', 'Pengajuan Kartu')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    // Dukung koleksi biasa maupun paginator
    $items = method_exists($requests, 'items') ? collect($requests->items()) : collect($requests);

    $statusMap = [
        'pending'  => ['label' => 'Menunggu',  'icon' => 'schedule',     'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
        'approved' => ['label' => 'Disetujui', 'icon' => 'check_circle', 'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'rejected' => ['label' => 'Ditolak',   'icon' => 'cancel',       'badge' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
    ];
    $fallbackStatus = ['label' => null, 'icon' => 'help', 'badge' => 'bg-slate-100 text-slate-600 ring-slate-500/20'];

    $count = [
        'all'      => $items->count(),
        'pending'  => $items->where('status', 'pending')->count(),
        'approved' => $items->where('status', 'approved')->count(),
        'rejected' => $items->where('status', 'rejected')->count(),
    ];
@endphp

<div class="space-y-5 lg:space-y-6 pb-28 md:pb-8">

    {{-- ================= HERO HEADER ================= --}}
    <header data-hero class="relative overflow-hidden rounded-b-[2rem] md:rounded-3xl bg-gradient-to-br from-orange-500 via-orange-600 to-red-700 text-white px-5 pt-8 pb-8 sm:px-8 sm:py-9 shadow-xl shadow-orange-200/60">
        <div class="pointer-events-none absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-16 -right-4 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-8 h-36 w-36 rounded-full bg-black/10"></div>

        <div class="relative flex items-start justify-between gap-3">
            <a href="{{ url()->previous() }}" class="md:hidden grid h-10 w-10 place-items-center rounded-full bg-white/20 backdrop-blur hover:bg-white/30 transition" aria-label="Kembali">
                <span class="material-symbols-outlined text-[22px]">arrow_back</span>
            </a>
            <div class="hidden md:block"></div>
            <div class="flex items-center gap-2 rounded-full bg-white text-slate-900 px-3.5 py-2 shadow-lg">
                <span class="material-symbols-outlined text-orange-600 text-[18px]">schedule</span>
                <span class="text-[10px] font-bold text-slate-400">Menunggu</span>
                <span class="text-sm font-black">{{ $count['pending'] }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Smart EduHub Wallet</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Pengajuan Kartu</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Tinjau dan proses pengajuan kartu dompet digital dari siswa.</p>
        </div>

        <div class="relative mt-5">
            <a href="{{ route('admin.finance.requests.create') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                Ajukan untuk siswa
            </a>
        </div>
    </header>

    {{-- ================= NOTIFIKASI ================= --}}
    @if(session('success'))
        <div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-emerald-700">
            <span class="material-symbols-outlined shrink-0 text-xl">check_circle</span>
            <p class="min-w-0 flex-1 text-sm font-bold">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.remove()" class="shrink-0 rounded-md p-0.5 opacity-70 transition hover:opacity-100" aria-label="Tutup">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    {{-- ================= NAVIGASI BAGIAN ================= --}}
    <nav class="grid grid-cols-2 gap-1 rounded-2xl bg-slate-100 p-1 sm:inline-grid sm:grid-cols-[auto_auto]" aria-label="Bagian keuangan kartu">
        <a href="{{ route('admin.finance.requests') }}" aria-current="page"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-orange-600 shadow-sm">
            <span class="material-symbols-outlined text-[20px]">assignment</span>
            Pengajuan
            @if($count['pending'])
                <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-black text-orange-600">{{ $count['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.finance.cards') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-slate-500 transition hover:text-slate-700">
            <span class="material-symbols-outlined text-[20px]">credit_card</span>
            Daftar kartu
        </a>
    </nav>

    {{-- ================= DAFTAR PENGAJUAN ================= --}}
    <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">

        {{-- Cari + filter status --}}
        <div class="space-y-3 border-b border-slate-100 p-4 sm:p-6">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-orange-600">search</span>
                <h3 class="text-base font-extrabold text-slate-900">Cari &amp; Filter</h3>
            </div>

            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <label class="relative w-full lg:max-w-sm">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">badge</span>
                    <input id="reqSearch" type="search" placeholder="Cari siswa atau orang tua"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 transition focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
                </label>

                <div class="-mx-4 flex gap-1.5 overflow-x-auto px-4 pb-0.5 sm:mx-0 sm:px-0" role="group" aria-label="Filter status">
                    @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $text)
                        <button type="button" data-status-filter="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-4 py-2 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                            {{ $text }}
                            <span class="rounded-full bg-black/5 px-1.5 py-px text-[11px] tabular-nums">{{ $count[$key] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabel (tablet & desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/80">
                    <tr class="text-xs font-bold text-slate-400">
                        <th class="px-6 py-3.5">Siswa</th>
                        <th class="px-6 py-3.5">Orang tua</th>
                        <th class="hidden lg:table-cell px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5 text-center">Surat</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $req)
                        @php
                            $name   = $req->student->nama_lengkap ?? $req->student->name ?? '-';
                            $s      = $statusMap[$req->status] ?? $fallbackStatus;
                            $hasDoc = $req->statement_file && $req->statement_file !== 'admin-submitted';
                            $q      = mb_strtolower($name . ' ' . $req->parent_name . ' ' . $req->parent_phone);
                        @endphp
                        <tr data-req data-status="{{ $req->status }}" data-search="{{ $q }}" class="transition hover:bg-orange-50/40">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-sm font-black text-white shadow-md shadow-orange-200/60">
                                        {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-slate-900">{{ $name }}</p>
                                        <p class="text-xs text-slate-500">Angkatan {{ $req->student->angkatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-bold text-slate-900">{{ $req->parent_name }}</p>
                                <p class="text-xs text-slate-500">{{ $req->parent_phone }}</p>
                            </td>
                            <td class="hidden lg:table-cell whitespace-nowrap px-6 py-3.5 text-sm font-medium text-slate-600">{{ optional($req->created_at)->format('d M Y') }}</td>
                            <td class="px-6 py-3.5 text-center">
                                @if($hasDoc)
                                    <a href="{{ Storage::url($req->statement_file) }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-xs font-bold text-orange-600 transition hover:bg-orange-50">
                                        <span class="material-symbols-outlined text-lg">description</span>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-sm text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $s['badge'] }}">
                                    <span class="material-symbols-outlined text-sm">{{ $s['icon'] }}</span>
                                    {{ $s['label'] ?? $req->status }}
                                </span>
                                @if(!empty($req->admin_note))
                                    <p class="mt-1 max-w-[14rem] truncate text-xs text-slate-500" title="{{ $req->admin_note }}">{{ $req->admin_note }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if($req->status === 'pending')
                                        <button type="button" data-action="approve" data-id="{{ $req->id }}" data-name="{{ $name }}"
                                                class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                            <span class="material-symbols-outlined text-base">check</span>
                                            Setujui
                                        </button>
                                        <button type="button" data-action="reject" data-id="{{ $req->id }}" data-name="{{ $name }}"
                                                class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
                                            <span class="material-symbols-outlined text-base">close</span>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Daftar kartu (mobile) --}}
        <ul class="md:hidden space-y-3 p-3">
            @foreach($items as $req)
                @php
                    $name   = $req->student->nama_lengkap ?? $req->student->name ?? '-';
                    $s      = $statusMap[$req->status] ?? $fallbackStatus;
                    $hasDoc = $req->statement_file && $req->statement_file !== 'admin-submitted';
                    $q      = mb_strtolower($name . ' ' . $req->parent_name . ' ' . $req->parent_phone);
                @endphp
                <li data-req data-status="{{ $req->status }}" data-search="{{ $q }}" class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 text-base font-black text-white shadow-md shadow-orange-200/60">
                                {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-extrabold text-slate-900">{{ $name }}</p>
                                <p class="text-xs text-slate-500">Angkatan {{ $req->student->angkatan ?? '-' }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $s['badge'] }}">
                                <span class="material-symbols-outlined text-sm">{{ $s['icon'] }}</span>
                                {{ $s['label'] ?? $req->status }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 rounded-2xl bg-slate-50 p-3 text-xs">
                            <div class="min-w-0">
                                <dt class="font-bold text-slate-400">Orang tua</dt>
                                <dd class="mt-0.5 truncate font-bold text-slate-800">{{ $req->parent_name }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="font-bold text-slate-400">Telepon</dt>
                                <dd class="mt-0.5 truncate font-bold text-slate-800">
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $req->parent_phone) }}" class="hover:text-orange-600">{{ $req->parent_phone }}</a>
                                </dd>
                            </div>
                            <div>
                                <dt class="font-bold text-slate-400">Tanggal</dt>
                                <dd class="mt-0.5 font-bold text-slate-800">{{ optional($req->created_at)->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-slate-400">Surat pernyataan</dt>
                                <dd class="mt-0.5 font-bold">
                                    @if($hasDoc)
                                        <a href="{{ Storage::url($req->statement_file) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-orange-600">
                                            <span class="material-symbols-outlined text-sm">description</span>Lihat
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if(!empty($req->admin_note))
                            <p class="text-xs text-slate-500"><span class="font-bold">Catatan:</span> {{ $req->admin_note }}</p>
                        @endif

                        @if($req->status === 'pending')
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" data-action="approve" data-id="{{ $req->id }}" data-name="{{ $name }}"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-extrabold text-white shadow-md shadow-emerald-200 transition hover:bg-emerald-700 active:scale-[.98]">
                                    <span class="material-symbols-outlined text-lg">check</span>
                                    Setujui
                                </button>
                                <button type="button" data-action="reject" data-id="{{ $req->id }}" data-name="{{ $name }}"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-extrabold text-rose-700 transition hover:bg-rose-100 active:scale-[.98]">
                                    <span class="material-symbols-outlined text-lg">close</span>
                                    Tolak
                                </button>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Kosong: belum ada data --}}
        @if($items->isEmpty())
            <div class="px-6 py-14 text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300">credit_card_off</span>
                <p class="mt-2 font-bold text-slate-600">Belum ada pengajuan kartu</p>
                <a href="{{ route('admin.finance.requests.create') }}" class="mt-2 inline-flex items-center gap-1.5 text-sm font-bold text-orange-600 hover:underline">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Buat pengajuan untuk siswa
                </a>
            </div>
        @endif

        {{-- Kosong: tidak ada hasil filter --}}
        <div id="reqEmpty" hidden class="px-6 py-14 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-300">search_off</span>
            <p class="mt-2 font-bold text-slate-600">Tidak ada pengajuan yang cocok</p>
            <p class="text-xs text-slate-400">Ubah kata kunci atau filter status.</p>
        </div>

        @if(method_exists($requests, 'hasPages') && $requests->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $requests->links() }}</div>
        @endif
    </section>
</div>

{{-- ================= MODAL SETUJUI / TOLAK (bottom sheet di mobile) ================= --}}
<div id="actionModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="amTitle">
    <div id="amBackdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative flex h-full items-end justify-center sm:items-center sm:p-4">
        <div class="max-h-[92vh] w-full max-w-lg overflow-y-auto rounded-t-[2rem] bg-white shadow-2xl sm:rounded-3xl pb-[env(safe-area-inset-bottom)]">
            <div class="mx-auto mt-3 h-1.5 w-12 rounded-full bg-slate-200 sm:hidden"></div>
            <div class="p-5 sm:p-7">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span id="amIconWrap" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl">
                            <span id="amIcon" class="material-symbols-outlined text-2xl"></span>
                        </span>
                        <div class="min-w-0">
                            <h3 id="amTitle" class="text-lg font-black tracking-tight text-slate-900"></h3>
                            <p id="amStudent" class="truncate text-sm text-slate-500"></p>
                        </div>
                    </div>
                    <button type="button" id="amClose" aria-label="Tutup"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="actionForm" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-2">
                        <label id="amLabel" for="amNote" class="block text-sm font-bold text-slate-700"></label>
                        <textarea id="amNote" name="admin_note" rows="4"
                                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 transition focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"></textarea>
                    </div>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
                        <button type="button" id="amCancel"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" id="amSubmit">
                            <span id="amSubmitIcon" class="material-symbols-outlined text-lg"></span>
                            <span id="amSubmitText"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var BASE = @json(url('finance/requests'));

    /* ---------- Modal ---------- */
    var modal = document.getElementById('actionModal');
    var form = document.getElementById('actionForm');
    var note = document.getElementById('amNote');
    var submit = document.getElementById('amSubmit');
    var lastFocus = null;

    var CONFIG = {
        approve: {
            title: 'Setujui pengajuan kartu',
            label: 'Catatan admin (opsional)',
            placeholder: 'Tambahkan catatan jika perlu',
            required: false,
            icon: 'check_circle',
            iconWrap: 'bg-emerald-50 text-emerald-600',
            btn: 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200',
            text: 'Setujui pengajuan'
        },
        reject: {
            title: 'Tolak pengajuan kartu',
            label: 'Alasan penolakan',
            placeholder: 'Jelaskan alasan penolakan',
            required: true,
            icon: 'cancel',
            iconWrap: 'bg-rose-50 text-rose-600',
            btn: 'bg-rose-600 hover:bg-rose-700 shadow-rose-200',
            text: 'Tolak pengajuan'
        }
    };

    var BTN_BASE = 'inline-flex items-center justify-center gap-2 rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-lg transition active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-70 ';

    function openModal(action, id, name) {
        var c = CONFIG[action];
        if (!c) return;
        lastFocus = document.activeElement;

        form.action = BASE + '/' + id + '/' + action;
        document.getElementById('amTitle').textContent = c.title;
        document.getElementById('amStudent').textContent = name;
        document.getElementById('amLabel').textContent = c.label;
        document.getElementById('amIcon').textContent = c.icon;
        document.getElementById('amIconWrap').className = 'grid h-11 w-11 shrink-0 place-items-center rounded-xl ' + c.iconWrap;
        document.getElementById('amSubmitIcon').textContent = c.icon;
        document.getElementById('amSubmitText').textContent = c.text;
        submit.className = BTN_BASE + c.btn;
        submit.disabled = false;

        note.value = '';
        note.placeholder = c.placeholder;
        note.required = c.required;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(function () { note.focus(); }, 50);
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-action]');
        if (btn) openModal(btn.dataset.action, btn.dataset.id, btn.dataset.name);
    });
    document.getElementById('amBackdrop').addEventListener('click', closeModal);
    document.getElementById('amClose').addEventListener('click', closeModal);
    document.getElementById('amCancel').addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // Cegah klik ganda
    form.addEventListener('submit', function () {
        submit.disabled = true;
        document.getElementById('amSubmitText').textContent = 'Memproses...';
    });

    /* ---------- Cari + filter status ---------- */
    var rows = document.querySelectorAll('[data-req]');
    var chips = document.querySelectorAll('[data-status-filter]');
    var search = document.getElementById('reqSearch');
    var empty = document.getElementById('reqEmpty');
    var state = { status: 'all', q: '' };

    var CHIP_ON  = ['bg-orange-600', 'text-white', 'shadow-md', 'shadow-orange-200'];
    var CHIP_OFF = ['bg-slate-100', 'text-slate-600', 'hover:bg-slate-200'];

    function render() {
        var visible = 0;
        rows.forEach(function (row) {
            var okStatus = state.status === 'all' || row.dataset.status === state.status;
            var okQuery = !state.q || row.dataset.search.indexOf(state.q) !== -1;
            row.hidden = !(okStatus && okQuery);
            // hitung satu kali per pengajuan (tabel desktop & kartu mobile berisi data yang sama)
            if (!row.hidden && row.offsetParent !== null) visible++;
        });
        if (empty) empty.hidden = rows.length === 0 || visible > 0;

        chips.forEach(function (chip) {
            var on = chip.dataset.statusFilter === state.status;
            chip.setAttribute('aria-pressed', on);
            CHIP_ON.forEach(function (c) { chip.classList.toggle(c, on); });
            CHIP_OFF.forEach(function (c) { chip.classList.toggle(c, !on); });
        });
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () { state.status = chip.dataset.statusFilter; render(); });
    });
    if (search) search.addEventListener('input', function () { state.q = search.value.trim().toLowerCase(); render(); });
    window.addEventListener('resize', render);

    render();
})();
</script>
@endpush

<script>
    // Mobile: tarik header sampai menempel penuh ke tepi atas/kiri/kanan layar,
    // berapa pun padding yang dipasang layout di atasnya.
    (function () {
        const hero = document.querySelector('[data-hero]');
        if (!hero) return;
        function fit() {
            hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
            if (window.innerWidth >= 768) return;
            const r = hero.getBoundingClientRect();
            const top = r.top + window.scrollY;
            hero.style.marginTop   = -Math.max(top, 0) + 'px';
            hero.style.marginLeft  = -Math.max(r.left, 0) + 'px';
            hero.style.marginRight = -Math.max(window.innerWidth - r.right, 0) + 'px';
        }
        fit();
        window.addEventListener('load', fit);
        window.addEventListener('resize', fit);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    })();
</script>
@endsection