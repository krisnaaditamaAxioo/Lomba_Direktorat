@php
    $parentUser = Auth::guard('parent')->user();
    $unread     = $parentUser ? $parentUser->unreadNotifications()->count() : 0;

    // [route, ikon, label desktop, label mobile (null = tidak tampil di bottom nav)]
    $nav = [
        ['parent.dashboard',     'dashboard',            'Dashboard',            'Home'],
        ['parent.attendance',    'event_available',      'Presensi Anak',        'Absen'],
        ['parent.finance',       'account_balance_wallet', 'Keuangan Siswa',     'Keuangan'],
        ['parent.points',        'military_tech',        'Poin Kedisiplinan',    'Poin'],
        ['parent.permit',        'assignment_late',      'Pengajuan Izin Sakit', 'Izin'],
        ['parent.letters',       'description',          'Surat Resmi',          null],
        ['parent.notifications', 'notifications',        'Notifikasi',           'Notif'],
        ['parent.complaints',    'report',               'Layanan Pengaduan',    null],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#059669"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Wali Murid') - Smart Edu Hub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        // Warna "brand" dipetakan ke hijau, jadi kelas brand-* di halaman lain ikut hijau
        tailwind.config = {
            theme: { extend: { colors: { brand: {
                50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399',
                500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22'
            } } } }
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4faf6; -webkit-font-smoothing: antialiased; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar-item-active {
            background-color: #ecfdf5;
            color: #047857;
            border-left: 4px solid #059669;
        }
        /* Logo mode web: diwarnai hijau (#059669) dengan tetap mempertahankan bentuk/transparansi logo */
        .logo-green { filter: url(#logo-green); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex overflow-x-hidden">
    <!-- Filter warna logo (hijau) -->
    <svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false">
        <defs>
            <filter id="logo-green" color-interpolation-filters="sRGB" x="0" y="0" width="100%" height="100%">
                <feFlood flood-color="#059669" result="green"/>
                <feComposite in="green" in2="SourceAlpha" operator="in"/>
            </filter>
        </defs>
    </svg>

    <!-- SideNavBar (Desktop Only) -->
    <aside class="hidden lg:flex flex-col w-64 h-screen bg-white border-r border-emerald-100 fixed left-0 top-0 z-50">
        <div class="p-8">
            <div class="flex items-center justify-start mb-2">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="logo-green h-10 w-auto object-contain">
            </div>
            <p class="text-xs font-bold text-emerald-600 ml-1">Portal Wali Murid</p>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar" aria-label="Menu utama">
            @foreach($nav as [$route, $icon, $label, $mobileLabel])
                @php $active = request()->routeIs($route); @endphp
                <a href="{{ route($route) }}"
                   @if($active) aria-current="page" @endif
                   class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 {{ $active ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined">{{ $icon }}</span>
                        <span class="text-sm">{{ $label }}</span>
                    </span>
                    @if($route === 'parent.notifications' && $unread > 0)
                        <span class="min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="p-6 mt-auto">
            <form action="{{ route('parent.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-500 hover:bg-rose-50 transition-all font-bold text-sm">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 flex flex-col min-h-screen pb-24 lg:pb-0 overflow-x-hidden">
        <!-- TopAppBar -->
        <header class="hidden lg:flex h-16 bg-white/80 backdrop-blur-md border-b border-emerald-100 sticky top-0 z-40 px-8 items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-sm font-semibold text-slate-500">
                    @yield('page_title', 'Dashboard Wali Murid')
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-900 leading-none">{{ $parentUser->name }}</p>
                        <p class="text-xs text-emerald-600 font-semibold mt-1">Wali dari: {{ $parentUser->student?->nama_lengkap ?? '-' }}</p>
                    </div>
                    <img alt="Profile" class="w-10 h-10 rounded-full border-2 border-emerald-100 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($parentUser->name) }}&background=059669&color=fff"/>
                </div>
            </div>
        </header>

        @unless(request()->routeIs('parent.dashboard'))
            {{-- Ponsel: header menyatu dengan sisi atas layar (sampai di bawah status bar), seperti aplikasi.
                 Desktop: tetap kartu melayang dengan margin. --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-[#059669] via-[#047857] to-[#064e3b] px-5 pb-8 pt-[max(1rem,env(safe-area-inset-top))] text-white shadow-lg shadow-emerald-900/15 rounded-b-[2rem] lg:mx-8 lg:mt-6 lg:rounded-[2rem] lg:px-6 lg:pt-6 lg:shadow-xl">
                <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-12 left-1/3 h-32 w-32 rounded-full bg-white/5"></div>

                {{-- Bar aplikasi (ponsel saja) --}}
                <div class="relative z-10 flex items-center justify-end gap-3 pb-5 lg:hidden">

                    <div class="flex items-center gap-2">
                        <a href="{{ route('parent.notifications') }}" aria-label="Notifikasi{{ $unread > 0 ? ', ' . $unread . ' belum dibaca' : '' }}"
                           class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white/15 backdrop-blur transition active:scale-95">
                            <span class="material-symbols-outlined text-[22px]">notifications</span>
                            @if($unread > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black ring-2 ring-emerald-700">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </a>
                        <img alt="" class="h-10 w-10 rounded-full border-2 border-white/60 shadow-sm"
                             src="https://ui-avatars.com/api/?name={{ urlencode($parentUser->name) }}&background=ffffff&color=047857&bold=true"/>
                    </div>
                </div>

                <div class="relative z-10 space-y-1 lg:space-y-4">
                    <div class="hidden items-center gap-3 lg:flex">
                        <span class="material-symbols-outlined text-emerald-200">family_restroom</span>
                        <p class="text-sm font-semibold text-emerald-100">Halaman Wali Murid</p>
                    </div>
                    <p class="truncate text-sm text-emerald-100/80 lg:hidden">Wali dari {{ $parentUser->student?->nama_lengkap ?? '-' }}</p>
                    <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">@yield('page_title', 'Halaman Wali Murid')</h1>
                </div>
            </div>
        @endunless

        <div class="p-4 lg:p-8">
            @yield('content')
        </div>

        <footer class="mt-auto p-8 border-t border-emerald-100 text-center hidden lg:block">
            <p class="text-xs text-slate-400 font-medium">© {{ date('Y') }} Smart Axioo Class Program. Portal Resmi Wali Murid.</p>
        </footer>
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav aria-label="Menu utama"
         class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-emerald-100 flex justify-around items-center px-2 pt-3 z-50 shadow-[0_-8px_30px_rgb(4,120,87,0.08)]"
         style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
        @foreach($nav as [$route, $icon, $label, $mobileLabel])
            @continue(!$mobileLabel)
            @php $active = request()->routeIs($route); @endphp
            <a href="{{ route($route) }}"
               @if($active) aria-current="page" @endif
               class="relative flex flex-col items-center gap-1 w-16 transition-colors {{ $active ? 'text-emerald-700 font-bold' : 'text-slate-400 hover:text-emerald-600' }}">
                <span class="flex h-8 w-12 items-center justify-center rounded-full transition-colors {{ $active ? 'bg-emerald-100' : '' }}">
                    <span class="material-symbols-outlined text-[24px]" @if($active) style="font-variation-settings:'FILL' 1;" @endif>{{ $icon }}</span>
                </span>
                <span class="text-[11px] leading-none">{{ $mobileLabel }}</span>
                @if($route === 'parent.notifications' && $unread > 0)
                    <span class="absolute top-0 right-2 min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">{{ $unread }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    @auth('parent')
    <script>
        (function () {
            try {
                const KEY = 'smart_acp_login_history';
                const MAX = 20;
                const p = @json(auth()->guard('parent')->user());
                const student = p.student || null;
                const currentUser = {
                    id: p.id,
                    type: 'parent',
                    guard: 'parent',
                    role_label: 'Wali Murid',
                    name: p.nama_lengkap || p.name || '',
                    secondary: p.email || '',
                    extra: { 
                        phone: p.phone || p.no_hp || '',
                        student_name: student ? (student.nama_lengkap || student.name || '') : ''
                    },
                    last_login: new Date().toISOString()
                };
                let list = [];
                try { list = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch(e) { list = []; }
                list = list.filter(x => x && !(String(x.id) === String(currentUser.id) && x.type === currentUser.type));
                list.unshift(currentUser);
                list = list.slice(0, MAX);
                localStorage.setItem(KEY, JSON.stringify(list));
            } catch(e) { /* ignore */ }
        })();
    </script>
    @endauth
</body>
</html>