@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')

@php
    /* =========================================================
     |  SETTINGS PAGE — Tema Oranye "Edu Hub" (v2)
     |  - Tab sticky + scroll per section (scrollspy akurat)
     |  - Progress bar scroll, tombol kembali ke atas
     |  - Mobile: tombol simpan melayang
     ========================================================= */

    /* Class reusable */
    $inputClass = 'w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-600/10 focus:border-orange-600 outline-none transition-all';
    $cardClass = 'bg-white rounded-3xl border border-slate-100 shadow-sm shadow-slate-200/60 overflow-hidden';
    $headClass = 'p-5 sm:p-6 border-b border-slate-100 bg-white flex flex-wrap items-center justify-between gap-3';
    $bodyClass  = 'p-5 sm:p-8';
    $iconBadge = 'w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0';
    $labelClass = 'flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide';

    /* Daftar tabel backup — VALUE JANGAN DIUBAH (dipakai controller) */
    $backupTables = [
        'students'                  => 'Siswa',
        'attendances'               => 'Absensi',
        'points'                    => 'Point',
        'exam_questions'            => 'Soal Ujian',
        'exam_results'              => 'Hasil Ujian',
        'courses'                   => 'Kursus',
        'modules'                   => 'Modul',
        'student_permits'           => 'Izin Siswa',
        'letters'                   => 'Surat',
        'registrations'             => 'Pendaftaran',
        'student_cards'             => 'Kartu Siswa',
        'student_card_requests'     => 'Permintaan Kartu',
        'student_card_transactions' => 'Transaksi Keuangan',
        'holidays'                  => 'Hari Libur',
        'angkatans'                 => 'Angkatan',
        'point_rules'               => 'Aturan Point',
        'class_rooms'               => 'Kelas',
        'academic_years'            => 'Tahun Ajaran',
    ];

    /* Form reset memakai daftar tabel yang sama dengan backup */
    $resetTables = $backupTables;

    /* Tab navigasi (urutan harus sama dengan urutan section di bawah) */
    $tabs = [
        ['id' => 'tahun-ajaran', 'icon' => 'calendar_month',      'label' => 'Tahun Ajaran'],
        ['id' => 'sekolah',      'icon' => 'school',              'label' => 'Sekolah'],
        ['id' => 'sistem',       'icon' => 'settings_applications','label' => 'Sistem'],
        ['id' => 'trainer',      'icon' => 'badge',               'label' => 'Trainer'],
        ['id' => 'maintenance',  'icon' => 'build',               'label' => 'Maintenance'],
        ['id' => 'backup',       'icon' => 'cloud_download',      'label' => 'Backup'],
    ];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* Tema SEH: latar lavender muda + font Inter */
    body{background-color:#f4f4fa;font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif}

    .no-scrollbar::-webkit-scrollbar{display:none}
    .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}

    @keyframes fadeInDown{from{opacity:0;transform:translateY(-16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
    .animate-fade-in-down{animation:fadeInDown .28s cubic-bezier(.16,1,.3,1)}

    @keyframes slideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
    .animate-slide-up{animation:slideUp .35s ease-out both}

    html{scroll-behavior:smooth}

    /* Efek muncul per section (hanya aktif jika JS jalan) */
    .js-reveal .settings-section{opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s cubic-bezier(.16,1,.3,1)}
    .js-reveal .settings-section.is-visible{opacity:1;transform:none}
    @media (prefers-reduced-motion:reduce){
        html{scroll-behavior:auto}
        .js-reveal .settings-section{opacity:1;transform:none;transition:none}
    }

    /* Tab pill */
    .tab-link{transition:all .25s ease}
    .tab-link.active{
        background:#ea580c;
        color:#fff;border-color:transparent;
        box-shadow:0 10px 22px -10px rgba(234,88,12,.6)
    }

    /* Section yang sedang aktif: ring halus */
    .settings-section.is-current > div:first-child{
        box-shadow:0 0 0 2px rgba(234,88,12,.20),0 14px 30px -14px rgba(234,88,12,.35)
    }
    .settings-section > div:first-child{transition:box-shadow .3s ease}

    /* Progress bar scroll */
    #scrollProgress{transform-origin:left;transform:scaleX(0);transition:transform .1s linear}
</style>

{{-- ============ HERO HEADER (tema SEH: oranye -> merah) ============ --}}
<div class="relative overflow-hidden rounded-3xl lg:rounded-[2.5rem] bg-gradient-to-br from-orange-500 via-red-500 to-red-800 px-5 sm:px-10 pt-5 sm:pt-7 pb-10 sm:pb-14 mb-4 shadow-xl shadow-red-500/20">
    <div class="absolute -top-20 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
    <div class="absolute -bottom-28 -left-16 w-72 h-72 bg-red-900/25 rounded-full"></div>
    <div class="absolute bottom-0 right-1/4 w-40 h-40 bg-white/5 rounded-full hidden sm:block"></div>

    <div class="relative">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                    <span class="material-symbols-outlined text-white">school</span>
                </div>
                <span class="text-white font-extrabold tracking-tight">SEH</span>
            </div>
            <span class="inline-flex items-center gap-1.5 bg-white rounded-full px-3.5 py-2 text-xs font-extrabold text-slate-900 shadow-sm">
                <span class="material-symbols-outlined text-sm text-orange-500">swap_horiz</span>
                Semester {{ $settings['semester'] == 'Odd' ? 'Ganjil' : 'Genap' }}
            </span>
        </div>

        <div class="text-center mt-8 sm:mt-10">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-200">System Settings</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mt-1.5">{{ __('Settings') }}</h2>
            <p class="text-orange-100/90 text-sm sm:text-base mt-2 max-w-xl mx-auto">Konfigurasi preferensi aplikasi &amp; informasi sekolah secara terpusat.</p>
        </div>
    </div>
</div>

{{-- Pill tahun akademik + tombol aksi cepat --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-3 px-4 flex items-center justify-center gap-2 text-sm font-bold text-slate-900">
        <span class="material-symbols-outlined text-lg">calendar_month</span> Tahun Akademik {{ $settings['academic_year'] }}
    </div>
    <a href="{{ route('settings.backup-all') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-2xl py-3 px-4 flex items-center justify-center gap-2 text-sm font-bold shadow-md shadow-orange-600/25 active:scale-[0.99] transition-all">
        <span class="material-symbols-outlined text-lg">cloud_download</span> Backup Semua Data
    </a>
</div>

{{-- ============ STAT CARDS ============ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @php
        $orange = 'bg-orange-50 text-orange-600';
        $green  = 'bg-emerald-50 text-emerald-600';
        $chipG  = 'bg-emerald-50 text-emerald-600';
        $chipS  = 'bg-slate-100 text-slate-500';
        $chipA  = 'bg-amber-50 text-amber-600';
        $statCards = [
            ['icon' => 'calendar_month', 'label' => 'Tahun Aktif', 'value' => $settings['academic_year'],                                'chip' => 'Aktif',      'chipClass' => $chipG, 'tone' => $orange],
            ['icon' => 'inventory_2',    'label' => 'Total Tahun', 'value' => count($academicYears) . ' tahun',                          'chip' => 'Terdaftar',  'chipClass' => $chipS, 'tone' => $orange],
            ['icon' => 'swap_horiz',     'label' => 'Semester',    'value' => $settings['semester'] == 'Odd' ? 'Ganjil' : 'Genap',      'chip' => 'Berjalan',   'chipClass' => $chipS, 'tone' => $green],
            ['icon' => 'shield',         'label' => 'Mode Sistem', 'value' => $settings['maintenance_mode'] ? 'Maintenance' : 'Normal', 'chip' => $settings['maintenance_mode'] ? 'Hanya admin' : 'Semua pengguna', 'chipClass' => $settings['maintenance_mode'] ? $chipA : $chipG, 'tone' => $settings['maintenance_mode'] ? $orange : $green],
        ];
    @endphp
    @foreach($statCards as $stat)
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all animate-slide-up" style="animation-delay: {{ $loop->index * 60 }}ms">
        <div class="flex items-start justify-between gap-2">
            <div class="w-11 h-11 rounded-2xl {{ $stat['tone'] }} flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">{{ $stat['icon'] }}</span>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $stat['chipClass'] }}">{{ $stat['chip'] }}</span>
        </div>
        <p class="text-xs font-bold text-slate-500 mt-4">{{ $stat['label'] }}</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-0.5 truncate">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- ============ TAB NAVIGASI STICKY ============ --}}
<div id="settingsTabs" class="sticky top-0 lg:top-16 z-30 mb-6 bg-white/90 backdrop-blur-md border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div id="tabScroller" class="flex gap-2 overflow-x-auto no-scrollbar px-2 py-3">
        @foreach($tabs as $tab)
        <a href="#{{ $tab['id'] }}" data-target="{{ $tab['id'] }}"
           class="tab-link {{ $loop->first ? 'active' : '' }} shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-slate-200 bg-white hover:text-orange-600 hover:border-orange-300">
            <span class="material-symbols-outlined text-base">{{ $tab['icon'] }}</span> {{ $tab['label'] }}
        </a>
        @endforeach
    </div>
    <div class="h-0.5 bg-orange-100/60">
        <div id="scrollProgress" class="h-full w-full bg-gradient-to-r from-orange-500 to-red-600"></div>
    </div>
</div>

<div class="max-w-5xl space-y-6">

    {{-- ===================== MANAJEMEN TAHUN AJARAN ===================== --}}
    <section id="tahun-ajaran" class="settings-section">
        <div class="{{ $cardClass }}">
            <div class="{{ $headClass }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">calendar_today</span></div>
                    <div>
                        <h3 class="font-bold text-slate-900">Manajemen Tahun Ajaran</h3>
                        <p class="text-xs text-slate-400 hidden sm:block">Kelola tahun ajaran sekolah</p>
                    </div>
                </div>
                <button type="button" onclick="openModal('addYearModal')"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-orange-600/25 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">add</span> Tambah Tahun
                </button>
            </div>

            {{-- Desktop: tabel --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tahun Ajaran</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($academicYears as $year)
                        <tr class="hover:bg-orange-50/40 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $year->name }}</td>
                            <td class="px-6 py-4">
                                @if($year->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-600 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-50 text-slate-400 border border-slate-100">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                @if(!$year->is_active)
                                    <form action="{{ route('settings.academic-years.activate', $year) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline">Aktifkan</button>
                                    </form>
                                    <form action="{{ route('settings.academic-years.destroy', $year) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm italic">Belum ada data tahun ajaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu list --}}
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($academicYears as $year)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $year->name }}</p>
                        <div class="mt-1.5">
                            @if($year->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-50 text-slate-400 border border-slate-100">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                    @if(!$year->is_active)
                    <div class="flex flex-col gap-2 shrink-0">
                        <form action="{{ route('settings.academic-years.activate', $year) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-[11px] font-bold text-white bg-orange-500 hover:bg-orange-600 px-4 py-1.5 rounded-lg transition-colors">Aktifkan</button>
                        </form>
                        <form action="{{ route('settings.academic-years.destroy', $year) }}" method="POST" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                            @csrf
                            <button type="submit" class="w-full text-[11px] font-bold text-red-500 bg-red-50 hover:bg-red-100 px-4 py-1.5 rounded-lg transition-colors">Hapus</button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-orange-200">event_busy</span>
                    <p class="text-sm text-slate-400 italic mt-2">Belum ada data tahun ajaran.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== FORM UTAMA ===================== --}}
    <form id="settingsForm" action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ===== School Information ===== --}}
        <section id="sekolah" class="settings-section">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">school</span></div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ __('School Information') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Identitas sekolah</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">apartment</span> School Name
                            </label>
                            <input type="text" name="school_name" value="{{ $settings['school_name'] }}" class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">calendar_month</span> Active Academic Year
                            </label>
                            <input type="text" value="{{ $settings['academic_year'] }}" class="{{ $inputClass }} bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                            <p class="text-[10px] text-slate-400 italic">* Diatur di tabel Manajemen Tahun Ajaran.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">swap_horiz</span> Semester
                            </label>
                            <select name="semester" class="{{ $inputClass }}">
                                <option value="Odd" {{ $settings['semester'] == 'Odd' ? 'selected' : '' }}>Ganjil (Odd)</option>
                                <option value="Even" {{ $settings['semester'] == 'Even' ? 'selected' : '' }}>Genap (Even)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">location_on</span> Address
                            </label>
                            <textarea name="school_address" rows="2" class="{{ $inputClass }} resize-y">{{ $settings['school_address'] }}</textarea>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">chat</span> WhatsApp Group Link
                            </label>
                            <input type="url" name="whatsapp_group_link" value="{{ $settings['whatsapp_group_link'] }}" placeholder="https://chat.whatsapp.com/..." class="{{ $inputClass }}">
                            <p class="text-[11px] text-slate-400 italic">Link grup WhatsApp untuk siswa baru</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== System Preferences ===== --}}
        <section id="sistem" class="settings-section">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">settings_applications</span></div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ __('System Preferences') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Preferensi sistem aplikasi</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">schedule</span> {{ __('Default Timezone') }}
                            </label>
                            <select name="timezone" class="{{ $inputClass }}">
                                <option value="Asia/Jakarta" {{ $settings['timezone'] == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (GMT+7)</option>
                                <option value="Asia/Makassar" {{ $settings['timezone'] == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (GMT+8)</option>
                                <option value="Asia/Jayapura" {{ $settings['timezone'] == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (GMT+9)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">translate</span> {{ __('Language') }}
                            </label>
                            <select name="language" class="{{ $inputClass }}">
                                <option value="id" {{ $settings['language'] == 'id' ? 'selected' : '' }}>Indonesian</option>
                                <option value="en" {{ $settings['language'] == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ $settings['language'] == 'ar' ? 'selected' : '' }}>Arabic (العربية)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">login</span> {{ __('Check-in Time') }}
                            </label>
                            <input type="time" name="check_in_time" value="{{ $settings['check_in_time'] }}" class="{{ $inputClass }}">
                            <p class="text-[11px] text-slate-400 italic">* Siswa yang absen setelah jam ini akan dianggap "Telat".</p>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">payments</span> {{ __('Fast Payment Amount') }}
                            </label>
                            <input type="number" name="fast_payment_amount" value="{{ $settings['fast_payment_amount'] }}" class="{{ $inputClass }}">
                            <p class="text-[11px] text-slate-400 italic">* Jumlah yang dipotong otomatis pada pembayaran cepat (misal: parkir).</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Pengaturan Trainer ===== --}}
        <section id="trainer" class="settings-section">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">badge</span></div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ __('Pengaturan Trainer Axioo Class Program') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Nilai default cetak Raport Kelas Industri</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }}">
                    <div class="flex items-start gap-2 p-3.5 rounded-xl bg-orange-50/70 border border-orange-100 text-xs text-orange-700 mb-5">
                        <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                        Data Trainer ini akan digunakan sebagai nilai default pada cetak Raport Siswa Kelas Industri.
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">person</span> Nama Trainer
                            </label>
                            <input type="text" name="trainer_name" value="{{ $settings['trainer_name'] }}" placeholder="Contoh: Deni Wahyudi, S.Kom." class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">workspace_premium</span> No. Sertifikasi
                            </label>
                            <input type="text" name="trainer_no_serti" value="{{ $settings['trainer_no_serti'] }}" placeholder="Contoh: CERT-AXIOO-2024-001" class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $labelClass }}">
                                <span class="material-symbols-outlined text-sm text-orange-500">badge</span> NBM / NIP
                            </label>
                            <input type="text" name="trainer_nbm" value="{{ $settings['trainer_nbm'] }}" placeholder="Contoh: 12345678" class="{{ $inputClass }}">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Mode Maintenance ===== --}}
        <section id="maintenance" class="settings-section">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-amber-100 shadow-sm shadow-amber-100/50 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-amber-100 bg-gradient-to-r from-amber-50/80 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30 shrink-0">
                            <span class="material-symbols-outlined">build</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ __('Mode Maintenance') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Kontrol akses sistem</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }} space-y-5">
                    <div class="p-4 bg-amber-50 border border-amber-200/70 rounded-xl flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 mt-0.5">warning</span>
                        <div>
                            <p class="text-sm font-bold text-amber-900">Perhatian!</p>
                            <p class="text-xs text-amber-700 mt-1">Saat Mode Maintenance aktif, semua pengguna <strong>kecuali Admin</strong> tidak dapat mengakses sistem dan akan dialihkan ke halaman "Sistem Dalam Perbaikan".</p>
                        </div>
                    </div>

                    <label class="flex items-center gap-4 p-4 bg-slate-50/80 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors border border-slate-200">
                        <div class="relative shrink-0">
                            <input type="checkbox" id="maintenance_toggle" name="maintenance_mode" value="1" class="sr-only peer" {{ $settings['maintenance_mode'] ? 'checked' : '' }}>
                            <div class="w-14 h-8 bg-slate-300 peer-checked:bg-red-500 rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow transition-all peer-checked:translate-x-6"></div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $settings['maintenance_mode'] ? 'Mode Maintenance: AKTIF' : 'Mode Maintenance: Nonaktif' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $settings['maintenance_mode'] ? 'Saat ini hanya admin yang bisa mengakses sistem.' : 'Sistem dapat diakses oleh semua pengguna.' }}</p>
                        </div>
                    </label>

                    <div id="maintenance_msg_box" class="space-y-2 transition-opacity {{ $settings['maintenance_mode'] ? '' : 'opacity-50' }}">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Pesan Maintenance Opsional</label>
                        <textarea id="maintenance_msg" name="maintenance_message" rows="3" {{ $settings['maintenance_mode'] ? '' : 'disabled' }}
                                  placeholder="Contoh: Sistem sedang diperbaiki untuk penambahan fitur baru. Akan kembali normal pada pukul 20.00 WIB. Terima kasih."
                                  class="{{ $inputClass }} focus:ring-amber-500/10 focus:border-amber-500 disabled:cursor-not-allowed">{{ $settings['maintenance_message'] }}</textarea>
                        <p class="text-[11px] text-slate-400 italic">* Pesan ini akan ditampilkan di halaman maintenance ke semua pengguna non-admin.</p>
                    </div>

                    @if($settings['maintenance_mode'])
                    <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 hover:underline">
                        <span class="material-symbols-outlined text-sm">open_in_new</span> Lihat Halaman Maintenance (preview)
                    </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Tombol aksi (desktop) --}}
        <div class="hidden lg:flex justify-end gap-3">
            <button type="reset" class="px-8 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-all">{{ __('Reset') }}</button>
            <button type="submit" class="px-10 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> {{ __('Save Changes') }}
            </button>
        </div>

        {{-- Tombol simpan melayang (mobile) --}}
        <div class="lg:hidden fixed bottom-24 inset-x-4 z-30">
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/35 flex items-center justify-center gap-2 active:scale-[0.98] transition-transform">
                <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- ===================== DATA BACKUP & RESET ===================== --}}
    <section id="backup" class="settings-section">
        <div class="{{ $cardClass }}">
            <div class="{{ $headClass }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">data_object</span></div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ __('Data Backup & Reset') }}</h3>
                        <p class="text-xs text-slate-400 hidden sm:block">Kelola dan amankan data aplikasi</p>
                    </div>
                </div>
                <div class="flex gap-2.5">
                    <a href="{{ route('settings.backup-all') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-orange-600/25 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">cloud_download</span> <span class="hidden sm:inline">Backup</span> Semua Data
                    </a>
                    <button type="button" onclick="openModal('resetAllModal')" class="bg-red-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0 hover:bg-red-600 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">delete_sweep</span> <span class="hidden sm:inline">Reset</span> Semua Data
                    </button>
                </div>
            </div>

            <div class="{{ $bodyClass }} space-y-8">

                {{-- Backup --}}
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-orange-500">backup</span> Backup Data
                        </h4>
                        <button type="button" onclick="toggleGroup('backup', this)" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">done_all</span> <span>Pilih Semua</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500">Pilih tabel yang ingin Anda backup, kemudian klik tombol Backup.</p>
                    <form action="{{ route('settings.backup') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 mb-5">
                            @foreach($backupTables as $table => $label)
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="tables[]" value="{{ $table }}" data-group="backup" class="peer sr-only" {{ $table === 'students' ? 'checked' : '' }}>
                                <div class="px-4 py-2.5 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-600 text-xs font-bold text-center truncate
                                            peer-checked:border-transparent peer-checked:bg-orange-600 peer-checked:text-white
                                            peer-checked:shadow-md peer-checked:shadow-orange-600/25 peer-focus-visible:ring-4 peer-focus-visible:ring-orange-500/20 hover:border-orange-300 transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-md shadow-orange-600/25 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">download</span> Backup Data
                        </button>
                    </form>
                </div>

                {{-- Reset --}}
                <div class="space-y-4 pt-6 border-t border-slate-100">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="text-sm font-bold text-red-600 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">delete_forever</span> Reset Data
                        </h4>
                        <button type="button" onclick="toggleGroup('reset', this)" class="text-xs font-bold text-red-500 hover:text-red-600 hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">done_all</span> <span>Pilih Semua</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500">Pilih tabel yang ingin Anda reset, kemudian klik tombol Reset. <span class="text-red-500 font-bold">Tindakan ini tidak dapat dibatalkan!</span></p>
                    <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Anda yakin ingin mereset data yang dipilih? Tindakan ini tidak dapat dibatalkan!')">
                        @csrf
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 mb-5">
                            @foreach($resetTables as $table => $label)
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="tables[]" value="{{ $table }}" data-group="reset" class="peer sr-only">
                                <div class="px-4 py-2.5 rounded-xl border-2 border-red-100 bg-red-50/40 text-red-600/80 text-xs font-bold text-center truncate
                                            peer-checked:border-transparent peer-checked:bg-gradient-to-r peer-checked:from-red-500 peer-checked:to-rose-500 peer-checked:text-white
                                            peer-checked:shadow-md peer-checked:shadow-red-500/30 peer-focus-visible:ring-4 peer-focus-visible:ring-red-500/20 hover:border-red-300 transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="bg-red-500 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0 hover:bg-red-600 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">delete</span> Reset Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Spacer agar konten tidak tertutup bottom nav layout (mobile) --}}
<div class="h-40 lg:h-16"></div>

{{-- Tombol kembali ke atas --}}
<button id="backToTop" type="button" aria-label="Kembali ke atas"
        class="fixed z-30 right-4 bottom-44 lg:bottom-8 lg:right-8 w-11 h-11 rounded-full bg-white text-orange-600 border border-orange-200 shadow-lg shadow-orange-500/20 flex items-center justify-center opacity-0 pointer-events-none translate-y-3 transition-all hover:bg-orange-50">
    <span class="material-symbols-outlined">keyboard_arrow_up</span>
</button>

{{-- ===================== MODAL: TAMBAH TAHUN AJARAN ===================== --}}
<div id="addYearModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 bg-gradient-to-br from-orange-500 via-red-500 to-red-700 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Tambah Tahun Ajaran
            </h3>
            <button type="button" onclick="closeModal('addYearModal')" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('settings.academic-years.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Nama Tahun Ajaran</label>
                <input type="text" name="name" required placeholder="Contoh: 2025/2026" class="{{ $inputClass }}">
                <p class="text-[10px] text-slate-400">Gunakan format "Tahun/Tahun" (misal: 2025/2026).</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('addYearModal')"
                        class="bg-slate-100 text-slate-600 font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md shadow-orange-600/25 hover:-translate-y-0.5 active:translate-y-0 transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL: KONFIRMASI RESET SEMUA ===================== --}}
<div id="resetAllModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 bg-gradient-to-br from-red-500 to-rose-500 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined">warning</span> Konfirmasi Reset Semua Data
            </h3>
            <button type="button" onclick="closeModal('resetAllModal')" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-red-50 border border-red-100 p-4 rounded-2xl">
                <h4 class="font-bold text-red-700 mb-2">Perhatian Penting!</h4>
                <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
                    <li>Semua data siswa akan dihapus</li>
                    <li>Semua data absensi, point, dan ujian akan dihapus</li>
                    <li>Semua data keuangan akan dihapus</li>
                    <li>Data admin dan manajemen tetap tersimpan</li>
                    <li>Tindakan ini <strong>TIDAK BISA DIBATALKAN</strong></li>
                </ul>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 p-4 rounded-2xl">
                <h4 class="font-bold text-yellow-700 mb-1.5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">lightbulb</span> Saran
                </h4>
                <p class="text-sm text-yellow-700">Pastikan Anda sudah melakukan <strong>Backup Semua Data</strong> sebelum mereset!</p>
            </div>
            <form action="{{ route('settings.reset-all') }}" method="POST" class="flex flex-wrap justify-end gap-3 pt-2">
                @csrf
                <button type="button" onclick="closeModal('resetAllModal')"
                        class="bg-slate-100 text-slate-600 font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="bg-red-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-lg shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0 hover:bg-red-600 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete_forever</span> Ya, Reset Semua Data
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL: PESAN SUKSES/ERROR ===================== --}}
@if(session('success') || session('error'))
<div id="messageModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 flex justify-between items-center {{ session('success') ? 'bg-gradient-to-br from-green-500 to-emerald-500' : 'bg-gradient-to-br from-red-500 to-rose-500' }}">
            <h3 class="font-bold text-white flex items-center gap-2">
                @if(session('success'))
                    <span class="material-symbols-outlined">check_circle</span> Berhasil!
                @else
                    <span class="material-symbols-outlined">error</span> Gagal!
                @endif
            </h3>
            <button type="button" onclick="closeModal('messageModal')" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <p class="text-slate-700">{{ session('success') ?? session('error') }}</p>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeModal('messageModal')"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md shadow-orange-600/25 hover:-translate-y-0.5 active:translate-y-0 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
(function () {
    /* ===== Modal helper ===== */
    window.openModal  = id => { const m = document.getElementById(id); if (m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; } };
    window.closeModal = id => { const m = document.getElementById(id); if (m) { m.classList.add('hidden'); document.body.style.overflow = ''; } };

    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(modal.id); });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(m => closeModal(m.id));
    });
    /* modal pesan tampil otomatis -> kunci scroll body */
    if (document.getElementById('messageModal')) document.body.style.overflow = 'hidden';

    /* ===== Scroll per section (tab + scrollspy) ===== */
    const tabsBar    = document.getElementById('settingsTabs');
    const scroller   = document.getElementById('tabScroller');
    const tabLinks   = Array.from(document.querySelectorAll('.tab-link'));
    const sections   = tabLinks.map(l => document.getElementById(l.dataset.target)).filter(Boolean);
    const progress   = document.getElementById('scrollProgress');
    const backToTop  = document.getElementById('backToTop');
    let lockUntil    = 0;   // kunci scrollspy saat scroll programatik
    let currentId    = null;
    let ticking      = false;

    /* Jarak dari atas viewport sampai bawah tab bar sticky */
    function stickyOffset() {
        const top = parseFloat(getComputedStyle(tabsBar).top) || 0;
        return top + tabsBar.offsetHeight + 16;
    }

    function setActive(id) {
        if (id === currentId) return;
        currentId = id;
        tabLinks.forEach(t => t.classList.toggle('active', t.dataset.target === id));
        sections.forEach(s => s.classList.toggle('is-current', s.id === id));
        const link = tabLinks.find(t => t.dataset.target === id);
        if (link) {
            /* geser tab horizontal SAJA (tidak ikut menggeser halaman) */
            const left = link.offsetLeft - (scroller.clientWidth - link.offsetWidth) / 2;
            scroller.scrollTo({ left: Math.max(0, left), behavior: 'smooth' });
        }
    }

    function goTo(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const y = el.getBoundingClientRect().top + window.pageYOffset - stickyOffset() + 8;
        lockUntil = Date.now() + 900;
        setActive(id);
        window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
        history.replaceState(null, '', '#' + id);
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', e => { e.preventDefault(); goTo(link.dataset.target); });
    });

    function spy() {
        ticking = false;

        /* progress bar */
        const doc  = document.documentElement;
        const max  = doc.scrollHeight - window.innerHeight;
        const pct  = max > 0 ? Math.min(1, Math.max(0, window.pageYOffset / max)) : 0;
        progress.style.transform = 'scaleX(' + pct + ')';

        /* tombol kembali ke atas */
        const showTop = window.pageYOffset > 500;
        backToTop.classList.toggle('opacity-0', !showTop);
        backToTop.classList.toggle('pointer-events-none', !showTop);
        backToTop.classList.toggle('translate-y-3', !showTop);

        if (Date.now() < lockUntil) return;

        /* section aktif = section terakhir yang bagian atasnya sudah lewat garis sticky */
        const line = stickyOffset() + 24;
        let active = sections[0].id;
        sections.forEach(s => { if (s.getBoundingClientRect().top <= line) active = s.id; });

        /* di dasar halaman -> paksa section terakhir aktif */
        if (window.innerHeight + window.pageYOffset >= doc.scrollHeight - 6) active = sections[sections.length - 1].id;

        setActive(active);
    }

    window.addEventListener('scroll', () => {
        if (!ticking) { ticking = true; requestAnimationFrame(spy); }
    }, { passive: true });
    window.addEventListener('resize', spy);

    backToTop.addEventListener('click', () => {
        lockUntil = Date.now() + 900;
        setActive(sections[0].id);
        window.scrollTo({ top: 0, behavior: 'smooth' });
        history.replaceState(null, '', location.pathname + location.search);
    });

    /* Efek muncul per section */
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.documentElement.classList.add('js-reveal');
        const reveal = new IntersectionObserver(entries => {
            entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('is-visible'); reveal.unobserve(en.target); } });
        }, { threshold: 0.08 });
        sections.forEach(s => reveal.observe(s));
    }

    /* Buka langsung ke section dari URL hash */
    if (location.hash && document.getElementById(location.hash.slice(1))) {
        setTimeout(() => goTo(location.hash.slice(1)), 150);
    } else {
        spy();
    }

    /* ===== Maintenance toggle (sinkron saat ubah & reset form) ===== */
    const mToggle = document.getElementById('maintenance_toggle');
    const mBox    = document.getElementById('maintenance_msg_box');
    const mMsg    = document.getElementById('maintenance_msg');
    function syncMaintenance() {
        mBox.classList.toggle('opacity-50', !mToggle.checked);
        mMsg.disabled = !mToggle.checked;
    }
    mToggle.addEventListener('change', syncMaintenance);
    document.getElementById('settingsForm').addEventListener('reset', () => setTimeout(syncMaintenance, 0));

    /* ===== Pilih semua / batalkan (chips backup & reset) ===== */
    window.toggleGroup = function (group, btn) {
        const boxes = document.querySelectorAll('input[data-group="' + group + '"]');
        const anyUnchecked = Array.from(boxes).some(b => !b.checked);
        boxes.forEach(b => b.checked = anyUnchecked);
        const label = btn.querySelector('span:last-child');
        if (label) label.textContent = anyUnchecked ? 'Batalkan' : 'Pilih Semua';
    };
})();
</script>

@endsection