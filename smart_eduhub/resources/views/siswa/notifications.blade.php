@extends('layouts.siswa')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@section('content')
@php
    $unreadCount = $notifications->where('read', false)->count();
@endphp

<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6 pb-28 px-2 sm:px-0">

    <!-- ===== Header ===== -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-6 pb-7 px-5 sm:px-8 text-white overflow-hidden shadow-xl shadow-orange-900/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-7 lg:pb-7">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="relative w-12 h-12 shrink-0 rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/20 shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">notifications</span>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1.5 rounded-full bg-white text-[#EB4325] text-[10px] font-black flex items-center justify-center shadow">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-white/70 block uppercase tracking-[0.2em] leading-none">Notifikasi</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-1.5 truncate">Pemberitahuan</h1>
                    <p class="text-xs text-white/80 font-medium mt-1">
                        {{ $unreadCount > 0 ? $unreadCount . ' pesan belum dibaca' : 'Semua pesan sudah dibaca' }}
                    </p>
                </div>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('siswa.notifications.read-all') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-[#EB4325] rounded-xl text-xs font-extrabold uppercase tracking-wider shadow-lg shadow-black/10 hover:bg-orange-50 hover:-translate-y-0.5 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">done_all</span>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- ===== Filter Tabs ===== -->
    @if($notifications->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar -mx-2 px-2 sm:mx-0 sm:px-0" id="notif-filters" role="tablist">
            <button type="button" data-filter="all" role="tab" aria-selected="true"
                class="notif-tab shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold border transition-all bg-slate-900 text-white border-slate-900">
                Semua
                <span class="px-1.5 py-0.5 rounded-full bg-white/20 text-[10px]">{{ $notifications->count() }}</span>
            </button>
            <button type="button" data-filter="unread" role="tab" aria-selected="false"
                class="notif-tab shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold border transition-all bg-white text-slate-600 border-slate-200 hover:border-orange-300">
                Belum dibaca
                <span class="px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-600 text-[10px]">{{ $unreadCount }}</span>
            </button>
        </div>
    @endif

    <!-- ===== Notifications List ===== -->
    @if($notifications->count() > 0)
        <div class="space-y-3" id="notif-list">
            @foreach($notifications as $notification)
                @php
                    $attachment = $notification->data['attachment'] ?? null;
                    $previewMessage = \Illuminate\Support\Str::limit($notification->message, 120);

                    $type = $notification->type;
                    if ($type === 'finance') {
                        $iconBox = 'bg-emerald-100 text-emerald-600';
                        $icon = 'account_balance_wallet';
                        $label = 'Keuangan';
                        $labelCls = 'bg-emerald-50 text-emerald-600';
                    } elseif ($type === 'point') {
                        $iconBox = 'bg-amber-100 text-amber-600';
                        $icon = 'grade';
                        $label = 'Poin';
                        $labelCls = 'bg-amber-50 text-amber-600';
                    } else {
                        $iconBox = 'bg-orange-100 text-orange-600';
                        $icon = 'campaign';
                        $label = 'Pengumuman';
                        $labelCls = 'bg-orange-50 text-orange-600';
                    }
                @endphp

                <a href="{{ route('siswa.notifications.show', $notification->id) }}"
                   data-read="{{ $notification->read ? '1' : '0' }}"
                   class="notif-item {{ $loop->iteration > 5 ? 'hidden' : '' }} group relative flex items-start gap-3 sm:gap-4 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-200/70 active:scale-[0.99]
                          {{ $notification->read ? 'bg-white border-slate-100' : 'bg-gradient-to-r from-orange-50/80 to-white border-orange-100' }}">

                    {{-- Aksen garis untuk notifikasi baru --}}
                    @if(!$notification->read)
                        <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-[#F6863B] to-[#EB4325]"></span>
                    @endif

                    <!-- Icon -->
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $iconBox }} group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[22px] sm:text-2xl">{{ $icon }}</span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <h3 class="font-bold text-slate-800 text-sm sm:text-[15px] leading-snug line-clamp-1">{{ $notification->title }}</h3>
                                @if(!$notification->read)
                                    <span class="relative flex h-2.5 w-2.5 shrink-0">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-60"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#EB4325]"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="hidden sm:inline-flex shrink-0 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $notification->read ? 'bg-slate-100 text-slate-400' : 'bg-orange-100 text-orange-600' }}">
                                {{ $notification->read ? 'Terbaca' : 'Baru' }}
                            </span>
                        </div>

                        <p class="text-slate-500 text-[13px] sm:text-sm leading-relaxed mt-1 line-clamp-2 break-words">{{ $previewMessage }}</p>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $labelCls }}">{{ $label }}</span>

                            @if($attachment)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider text-slate-600 bg-slate-100">
                                    <span class="material-symbols-outlined text-[13px]">attach_file</span> PDF
                                </span>
                            @endif

                            <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                            @if(!$notification->read)
                                <span class="sm:hidden ml-auto text-[10px] font-extrabold uppercase tracking-wider text-[#EB4325]">Baru</span>
                            @endif
                        </div>
                    </div>

                    <!-- Chevron -->
                    <span class="material-symbols-outlined hidden sm:block self-center text-slate-300 group-hover:text-[#EB4325] group-hover:translate-x-1 transition-all">chevron_right</span>
                </a>
            @endforeach
        </div>

        <!-- Empty state untuk filter "Belum dibaca" -->
        <div id="notif-filter-empty" class="hidden bg-white rounded-3xl border border-slate-100 p-10 text-center">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-3xl">mark_email_read</span>
            </div>
            <h3 class="text-sm font-bold text-slate-800 mb-1">Semua sudah dibaca</h3>
            <p class="text-xs text-slate-400 font-medium">Tidak ada notifikasi yang belum dibaca di halaman ini.</p>
        </div>

        <!-- Tombol Lihat Semua / Tampilkan Lebih Sedikit -->
        <button type="button" id="notif-toggle"
            class="{{ $notifications->count() > 5 ? '' : 'hidden' }} w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:border-orange-300 hover:text-[#EB4325] hover:shadow-md active:scale-[0.99] transition-all">
            <span id="notif-toggle-label">Lihat Semua ({{ $notifications->count() }})</span>
            <span id="notif-toggle-icon" class="material-symbols-outlined text-lg transition-transform duration-300">expand_more</span>
        </button>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-3 sm:p-4 overflow-x-auto">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-10 sm:p-16 text-center">
            <div class="relative w-20 h-20 mx-auto mb-5">
                <div class="absolute inset-0 bg-orange-100 rounded-3xl rotate-6"></div>
                <div class="relative w-full h-full bg-gradient-to-br from-orange-50 to-white border border-orange-100 text-orange-300 rounded-3xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">notifications_off</span>
                </div>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Tidak Ada Notifikasi</h3>
            <p class="text-sm text-slate-400 font-medium max-w-xs mx-auto">Anda belum memiliki notifikasi apapun. Info terbaru akan muncul di sini.</p>
        </div>
    @endif
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    @media (hover: none) {
        .notif-item:hover { transform: none; box-shadow: none; }
    }
</style>

<script>
    (function () {
        const tabs = document.querySelectorAll('.notif-tab');
        const items = document.querySelectorAll('.notif-item');
        const emptyBox = document.getElementById('notif-filter-empty');
        if (!tabs.length) return;

        const LIMIT = 5;
        let filter = 'all';
        let expanded = false;
        const toggleBtn = document.getElementById('notif-toggle');
        const toggleLabel = document.getElementById('notif-toggle-label');
        const toggleIcon = document.getElementById('notif-toggle-icon');

        function apply() {
            let matched = 0;
            items.forEach(item => {
                const match = filter === 'all' || item.dataset.read === '0';
                if (match) matched++;
                const show = match && (expanded || matched <= LIMIT);
                item.classList.toggle('hidden', !show);
            });

            if (emptyBox) emptyBox.classList.toggle('hidden', matched > 0);

            if (toggleBtn) {
                toggleBtn.classList.toggle('hidden', matched <= LIMIT);
                if (toggleLabel) toggleLabel.textContent = expanded ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua (' + matched + ')';
                if (toggleIcon) toggleIcon.style.transform = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        toggleBtn?.addEventListener('click', () => {
            expanded = !expanded;
            apply();
            if (!expanded) document.getElementById('notif-list')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        apply();

        const active = ['bg-slate-900', 'text-white', 'border-slate-900'];
        const inactive = ['bg-white', 'text-slate-600', 'border-slate-200'];

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                filter = tab.dataset.filter;
                expanded = false;

                tabs.forEach(t => {
                    const on = t === tab;
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                    t.classList.remove(...(on ? inactive : active));
                    t.classList.add(...(on ? active : inactive));
                });

                apply();
            });
        });
    })();
</script>
@endsection