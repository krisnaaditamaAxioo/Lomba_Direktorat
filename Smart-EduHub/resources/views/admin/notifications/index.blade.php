@extends('layouts.app')

@section('title', 'Kelola Notifikasi')

@section('styles')
<style>
    /* Header menempel ke tepi atas layar di ponsel; beri ruang untuk notch/status bar */
    #pageHero { padding-top: max(1.25rem, calc(env(safe-area-inset-top, 0px) + 1rem)); }
    @media (min-width: 1024px) { #pageHero { padding-top: 1.5rem; } }
    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
</style>
@endsection

@section('content')
@php
    $showAll = request()->boolean('all');
    $isPaginator = $notifications instanceof \Illuminate\Contracts\Pagination\Paginator;
    $allRows = $isPaginator ? collect($notifications->items()) : collect($notifications);
    $rows = $showAll ? $allRows : $allRows->take(5);
    $totalNotifications = ($isPaginator && method_exists($notifications, 'total')) ? $notifications->total() : $allRows->count();
    $unreadInView = $rows->where('read', false)->count();

    $types = [
        'finance' => ['label' => 'Keuangan', 'icon' => 'account_balance_wallet', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10'],
        'point'   => ['label' => 'Poin',     'icon' => 'stars',                  'class' => 'bg-amber-50 text-amber-700 ring-amber-600/10'],
    ];
    $defaultType = ['label' => 'Admin', 'icon' => 'campaign', 'class' => 'bg-orange-50 text-orange-700 ring-orange-600/10'];

    $allUrl    = route('admin.notifications.index', array_merge(request()->except('page'), ['all' => 1]));
    $recentUrl = route('admin.notifications.index', request()->except(['all', 'page']));

    $statCards = [
        ['label' => 'Total',        'value' => $totalNotifications, 'icon' => 'notifications',     'tile' => 'bg-orange-50 text-orange-600'],
        ['label' => 'Ditampilkan',  'value' => $rows->count(),      'icon' => 'visibility',        'tile' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Belum dibaca', 'value' => $unreadInView,       'icon' => 'mark_email_unread', 'tile' => 'bg-rose-50 text-rose-600'],
    ];
@endphp

<div class="flex w-full min-w-0 flex-col gap-5">

    {{-- ================= HEADER =================
         Ponsel: penuh ke tepi atas/kiri/kanan (class `hero-bleed` dari layout), sudut bawah membulat.
         Desktop: kartu bersudut membulat di bawah top bar. --}}
    <header id="pageHero"
            class="hero-bleed relative overflow-hidden rounded-b-[2.5rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-4 pb-8 text-white shadow-xl shadow-[#EB4325]/20 sm:px-6 lg:rounded-[2rem] lg:px-8 lg:pb-9">
        <div class="pointer-events-none absolute -right-10 -top-12 h-56 w-56 rounded-full bg-white/10" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-12 top-10 h-40 w-40 rounded-full bg-white/10" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/4 h-44 w-44 rounded-full bg-black/10 blur-2xl" aria-hidden="true"></div>

        <div class="relative z-10 w-full">
            {{-- Baris atas: kembali + ringkas --}}
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('dashboard') }}" aria-label="Kembali ke dashboard"
                   class="focus-ring grid h-10 w-10 place-items-center rounded-full bg-white/20 text-white transition hover:bg-white/30 active:scale-95">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">arrow_back</span>
                </a>
                <div class="flex items-center gap-2 rounded-full bg-white px-3.5 py-2 shadow-lg shadow-black/10">
                    <span class="material-symbols-outlined text-[18px] text-orange-600" aria-hidden="true">mark_email_unread</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Belum dibaca</span>
                    <span class="text-sm font-extrabold tabular-nums text-slate-900">{{ number_format($unreadInView) }}</span>
                </div>
            </div>

            {{-- Judul --}}
            <div class="mt-3 flex flex-col items-center gap-4 text-center lg:mt-1">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-white/80 sm:text-xs">Pusat Pesan</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight sm:text-4xl">Kelola Notifikasi</h1>
                    <p class="mx-auto mt-1.5 max-w-xl text-xs text-white/85 sm:text-sm">Lihat dan kelola semua notifikasi yang dikirim ke siswa.</p>
                </div>

                <a href="{{ route('admin.notifications.create') }}"
                   class="focus-ring inline-flex h-12 w-full shrink-0 items-center justify-center gap-2 rounded-2xl bg-white px-6 text-sm font-bold text-[#C2331A] shadow-lg shadow-black/10 transition hover:bg-white/90 active:scale-95 sm:w-auto">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">add</span>
                    Kirim notifikasi baru
                </a>
            </div>
        </div>
    </header>

    {{-- ================= RINGKASAN ================= --}}
    <section aria-label="Ringkasan notifikasi" class="grid w-full grid-cols-3 gap-3">
        @foreach($statCards as $card)
            <div class="flex min-w-0 flex-col items-start gap-2 rounded-3xl border border-slate-100 bg-white p-3.5 shadow-sm sm:flex-row sm:items-center sm:gap-3 sm:p-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl sm:h-11 sm:w-11 {{ $card['tile'] }}" aria-hidden="true">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">{{ $card['icon'] }}</span>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold leading-none tabular-nums text-slate-900 sm:text-2xl">{{ number_format($card['value']) }}</p>
                    <p class="mt-1 truncate text-[11px] font-semibold text-slate-500 sm:text-xs">{{ $card['label'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ================= DAFTAR NOTIFIKASI ================= --}}
    <section aria-labelledby="listTitle" class="w-full min-w-0 overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">

        <!-- Toolbar -->
        <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="min-w-0">
                <h2 id="listTitle" class="flex items-center gap-2 text-lg font-black text-slate-900">
                    <span class="material-symbols-outlined text-[22px] text-orange-600" aria-hidden="true">notifications_active</span>
                    Daftar notifikasi
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    @if($showAll)
                        Menampilkan semua <span class="font-bold text-slate-700">{{ $totalNotifications }}</span> notifikasi
                    @else
                        Menampilkan <span class="font-bold text-slate-700">{{ $rows->count() }}</span> terbaru dari <span class="font-bold text-slate-700">{{ $totalNotifications }}</span> notifikasi
                    @endif
                    @if($unreadInView > 0)
                        <span class="mx-1 text-slate-300" aria-hidden="true">|</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-orange-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>{{ $unreadInView }} belum dibaca
                        </span>
                    @endif
                </p>
            </div>

            @if($totalNotifications > 5 || $showAll)
            <div class="inline-flex shrink-0 self-start rounded-2xl bg-slate-100 p-1 text-sm font-bold sm:self-auto" role="group" aria-label="Jumlah notifikasi yang ditampilkan">
                <a href="{{ $recentUrl }}" @if(!$showAll) aria-current="true" @endif
                   class="focus-ring rounded-xl px-4 py-2 transition {{ !$showAll ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    5 terbaru
                </a>
                <a href="{{ $allUrl }}" @if($showAll) aria-current="true" @endif
                   class="focus-ring rounded-xl px-4 py-2 transition {{ $showAll ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    All
                </a>
            </div>
            @endif
        </div>

        @if($rows->count() > 0)

        <!-- Tabel: tablet ke atas -->
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/70 text-xs font-bold text-slate-500">
                        <th scope="col" class="px-6 py-3.5">Siswa</th>
                        <th scope="col" class="px-4 py-3.5">Tipe</th>
                        <th scope="col" class="px-4 py-3.5">Judul</th>
                        <th scope="col" class="px-4 py-3.5">Status</th>
                        <th scope="col" class="px-4 py-3.5">Tanggal</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rows as $notification)
                    @php
                        $name = $notification->student?->nama_lengkap ?? 'Siswa tidak ditemukan';
                        $type = $types[$notification->type] ?? $defaultType;
                    @endphp
                    <tr class="transition-colors hover:bg-orange-50/30">
                        <td class="px-6 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-sm font-black text-orange-600" aria-hidden="true">
                                    {{ mb_strtoupper(mb_substr($notification->student?->nama_lengkap ?? '?', 0, 1)) }}
                                    @unless($notification->read)
                                        <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border-2 border-white bg-orange-500"></span>
                                    @endunless
                                </span>
                                <span class="max-w-[12rem] truncate text-sm font-bold text-slate-800 2xl:max-w-xs">{{ $name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $type['class'] }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $type['icon'] }}</span>
                                {{ $type['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="max-w-[16rem] truncate text-sm text-slate-600 xl:max-w-sm 2xl:max-w-xl" title="{{ $notification->title }}">{{ $notification->title }}</p>
                        </td>
                        <td class="px-4 py-4">
                            @if($notification->read)
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold text-slate-500">
                                    <span class="material-symbols-outlined text-[16px]">done_all</span> Sudah dibaca
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold text-orange-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span> Belum dibaca
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm tabular-nums text-slate-500">{{ $notification->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.notifications.edit', $notification) }}" aria-label="Edit notifikasi" title="Edit"
                                   class="focus-ring rounded-xl p-2 text-slate-400 transition-colors hover:bg-orange-50 hover:text-primary">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Hapus notifikasi" title="Hapus"
                                            class="focus-ring rounded-xl p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-error">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Kartu: ponsel -->
        <ul class="divide-y divide-slate-100 md:hidden">
            @foreach($rows as $notification)
            @php
                $name = $notification->student?->nama_lengkap ?? 'Siswa tidak ditemukan';
                $type = $types[$notification->type] ?? $defaultType;
            @endphp
            <li class="px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="relative mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-sm font-black text-orange-600" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($notification->student?->nama_lengkap ?? '?', 0, 1)) }}
                        @unless($notification->read)
                            <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border-2 border-white bg-orange-500"></span>
                        @endunless
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <p class="break-words text-sm font-bold text-slate-900">{{ $name }}</p>
                            <p class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->format('d M, H:i') }}</p>
                        </div>

                        <p class="mt-1 line-clamp-2 break-words text-sm text-slate-600">{{ $notification->title }}</p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $type['class'] }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $type['icon'] }}</span>
                                {{ $type['label'] }}
                            </span>
                            @if($notification->read)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                                    <span class="material-symbols-outlined text-[16px]">done_all</span> Sudah dibaca
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold text-orange-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span> Belum dibaca
                                </span>
                            @endif
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.notifications.edit', $notification) }}"
                               class="focus-ring inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-100 px-3 py-2.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-200">
                                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                            </a>
                            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="focus-ring inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-rose-50 px-3 py-2.5 text-xs font-bold text-rose-600 transition-colors hover:bg-rose-100">
                                    <span class="material-symbols-outlined text-[18px]">delete</span> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination (hanya pada mode All) -->
        @if($showAll && $isPaginator && $notifications->hasPages())
            <div class="border-t border-slate-100 px-5 py-5 sm:px-6">
                {{ $notifications->links() }}
            </div>
        @endif

        @else
            <div class="px-6 py-16 text-center">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-50">
                    <span class="material-symbols-outlined text-4xl text-slate-300">inbox</span>
                </span>
                <p class="mt-4 text-sm font-bold text-slate-600">Belum ada notifikasi</p>
                <p class="mt-1 text-sm text-slate-400">Kirim notifikasi pertama Anda ke siswa.</p>
                <a href="{{ route('admin.notifications.create') }}" class="focus-ring mt-5 inline-flex items-center gap-2 rounded-2xl bg-primary px-5 py-2.5 text-sm font-bold text-white transition-all hover:bg-orange-700">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Kirim notifikasi baru
                </a>
            </div>
        @endif
    </section>
</div>
@endsection