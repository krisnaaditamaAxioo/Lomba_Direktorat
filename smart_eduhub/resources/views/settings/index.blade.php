@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')

@php
    /* =========================================================
     |  SETTINGS PAGE — Tema Oranye "Edu Hub"
     |  Desktop : hero gradient oranye + tab sticky + kartu modern
     |  Mobile  : bottom navigation + tombol simpan melayang
     ========================================================= */

    /* Class reusable */
    $inputClass = 'w-full bg-slate-50/80 border border-slate-200 rounded-xl py-3 px-4 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all';
    $cardClass  = 'bg-white rounded-2xl sm:rounded-3xl border border-orange-100/70 shadow-sm shadow-orange-100/50 overflow-hidden';
    $headClass  = 'p-5 sm:p-6 border-b border-orange-50 bg-gradient-to-r from-orange-50/70 to-transparent flex flex-wrap items-center justify-between gap-3';
    $bodyClass  = 'p-5 sm:p-8';
    $iconBadge  = 'w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30 shrink-0';

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

    /* Form reset lama memakai key "attendance" (singular) — dipertahankan */
    $resetTables = $backupTables;
    unset($resetTables['attendances']);
    $resetTables['attendance'] = 'Absensi';
@endphp

<style>
    .no-scrollbar::-webkit-scrollbar{display:none}
    .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}

    @keyframes fadeInDown{from{opacity:0;transform:translateY(-16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
    .animate-fade-in-down{animation:fadeInDown .28s cubic-bezier(.16,1,.3,1)}

    @keyframes slideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
    .animate-slide-up{animation:slideUp .35s ease-out both}

    html{scroll-behavior:smooth;scroll-padding-top:110px}

    /* Tab pill aktif — oranye Edu Hub */
    .tab-link.active{
        background:linear-gradient(135deg,#f97316,#f59e0b);
        color:#fff;border-color:transparent;
        box-shadow:0 10px 22px -8px rgba(249,115,22,.55)
    }
</style>

{{-- ============ HERO HEADER (gradient oranye, gaya gambar 1) ============ --}}
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 via-orange-500 to-amber-400 px-6 sm:px-10 py-8 sm:py-12 mb-6 shadow-xl shadow-orange-500/25">
    <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full blur-sm"></div>
    <div class="absolute -bottom-24 -left-12 w-72 h-72 bg-orange-600/30 rounded-full blur-md"></div>
    <div class="absolute top-8 right-1/3 w-14 h-14 bg-white/10 rounded-full hidden sm:block"></div>

    <div class="relative">
        <nav class="flex items-center gap-2 text-orange-50/80 mb-2">
            <span class="material-symbols-outlined text-base">settings</span>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em]">System</span>
        </nav>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white">{{ __('Settings') }}</h2>
        <p class="text-orange-50/90 text-sm sm:text-base mt-2 max-w-xl">Configure general application preferences and school information.</p>
    </div>
</div>

{{-- ============ STAT CARDS (gaya kartu gambar 2) ============ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @php
        $statCards = [
            ['icon' => 'calendar_month', 'label' => 'Tahun Aktif',    'value' => $settings['academic_year'],                                          'sub' => 'Tahun ajaran berjalan'],
            ['icon' => 'inventory_2',    'label' => 'Total Tahun',    'value' => count($academicYears) . ' tahun',                                    'sub' => 'Tahun terdaftar'],
            ['icon' => 'swap_horiz',     'label' => 'Semester',       'value' => $settings['semester'] == 'Odd' ? 'Ganjil' : 'Genap',                'sub' => 'Semester aktif'],
            ['icon' => 'shield',         'label' => 'Mode Sistem',    'value' => $settings['maintenance_mode'] ? 'Maintenance' : 'Normal',            'sub' => $settings['maintenance_mode'] ? 'Hanya admin' : 'Semua pengguna'],
        ];
    @endphp
    @foreach($statCards as $stat)
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-orange-100/80 shadow-sm hover:shadow-md hover:shadow-orange-100/60 hover:-translate-y-0.5 transition-all">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-100/70 text-orange-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-lg sm:text-xl">{{ $stat['icon'] }}</span>
        </div>
        <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider mt-3">{{ $stat['label'] }}</p>
        <p class="text-base sm:text-xl font-bold text-slate-800 truncate">{{ $stat['value'] }}</p>
        <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">{{ $stat['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ============ TAB NAVIGASI STICKY ============ --}}
<div class="sticky top-0 z-30 mb-6 py-3 bg-white/90 backdrop-blur-md border border-orange-100/80 rounded-2xl shadow-sm">
    <div class="flex gap-2 overflow-x-auto no-scrollbar px-2">
        <a href="#tahun-ajaran" class="tab-link active shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">calendar_month</span> Tahun Ajaran
        </a>
        <a href="#sekolah" class="tab-link shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">school</span> Sekolah
        </a>
        <a href="#sistem" class="tab-link shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">settings_applications</span> Sistem
        </a>
        <a href="#trainer" class="tab-link shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">badge</span> Trainer
        </a>
        <a href="#maintenance" class="tab-link shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">build</span> Maintenance
        </a>
        <a href="#backup" class="tab-link shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-500 border border-orange-100 bg-orange-50/50 hover:text-orange-600 transition-all">
            <span class="material-symbols-outlined text-base">cloud_download</span> Backup
        </a>
    </div>
</div>

<div class="max-w-5xl space-y-6">

    {{-- ===================== MANAJEMEN TAHUN AJARAN ===================== --}}
    <section id="tahun-ajaran" class="settings-section scroll-mt-28">
        <div class="{{ $cardClass }}">
            <div class="{{ $headClass }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">calendar_today</span></div>
                    <div>
                        <h3 class="font-bold text-slate-800">Manajemen Tahun Ajaran</h3>
                        <p class="text-xs text-slate-400 hidden sm:block">Kelola tahun ajaran sekolah</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('addYearModal').classList.remove('hidden')"
                        class="bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">add</span> Tambah Tahun
                </button>
            </div>

            {{-- Desktop: tabel --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-orange-50/60">
                            <th class="px-6 py-4 text-[11px] font-bold text-orange-900/60 uppercase tracking-wider">Tahun Ajaran</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-orange-900/60 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-orange-900/60 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-50">
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
            <div class="md:hidden divide-y divide-orange-50">
                @forelse($academicYears as $year)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $year->name }}</p>
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
        <section id="sekolah" class="settings-section scroll-mt-28">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">school</span></div>
                        <div>
                            <h3 class="font-bold text-slate-800">{{ __('School Information') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Identitas sekolah</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">apartment</span> School Name
                            </label>
                            <input type="text" name="school_name" value="{{ \App\Models\Setting::get('school_name', 'Smart Axioo School') }}" class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">calendar_month</span> Active Academic Year
                            </label>
                            <input type="text" value="{{ $settings['academic_year'] }}" class="{{ $inputClass }} bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                            <p class="text-[10px] text-slate-400 italic">* Diatur di tabel Manajemen Tahun Ajaran.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">swap_horiz</span> Semester
                            </label>
                            <select name="semester" class="{{ $inputClass }}">
                                <option value="Odd" {{ $settings['semester'] == 'Odd' ? 'selected' : '' }}>Ganjil (Odd)</option>
                                <option value="Even" {{ $settings['semester'] == 'Even' ? 'selected' : '' }}>Genap (Even)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">location_on</span> Address
                            </label>
                            <textarea name="school_address" rows="1" class="{{ $inputClass }}">{{ \App\Models\Setting::get('school_address', 'Jl. Pendidikan No. 123, Jakarta, Indonesia') }}</textarea>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
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
        <section id="sistem" class="settings-section scroll-mt-28">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">settings_applications</span></div>
                        <div>
                            <h3 class="font-bold text-slate-800">{{ __('System Preferences') }}</h3>
                            <p class="text-xs text-slate-400 hidden sm:block">Preferensi sistem aplikasi</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $bodyClass }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">schedule</span> {{ __('Default Timezone') }}
                            </label>
                            <select name="timezone" class="{{ $inputClass }}">
                                <option value="Asia/Jakarta" {{ $settings['timezone'] == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (GMT+7)</option>
                                <option value="Asia/Makassar" {{ $settings['timezone'] == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (GMT+8)</option>
                                <option value="Asia/Jayapura" {{ $settings['timezone'] == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (GMT+9)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">translate</span> {{ __('Language') }}
                            </label>
                            <select name="language" class="{{ $inputClass }}">
                                <option value="id" {{ $settings['language'] == 'id' ? 'selected' : '' }}>Indonesian</option>
                                <option value="en" {{ $settings['language'] == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ $settings['language'] == 'ar' ? 'selected' : '' }}>Arabic (العربية)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">login</span> {{ __('Check-in Time') }}
                            </label>
                            <input type="time" name="check_in_time" value="{{ $settings['check_in_time'] }}" class="{{ $inputClass }}">
                            <p class="text-[11px] text-slate-400 italic">* Siswa yang absen setelah jam ini akan dianggap "Telat".</p>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
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
        <section id="trainer" class="settings-section scroll-mt-28">
            <div class="{{ $cardClass }}">
                <div class="{{ $headClass }}">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">badge</span></div>
                        <div>
                            <h3 class="font-bold text-slate-800">{{ __('Pengaturan Trainer Axioo Class Program') }}</h3>
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
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">person</span> Nama Trainer
                            </label>
                            <input type="text" name="trainer_name" value="{{ \App\Models\Setting::get('trainer_name', '') }}" placeholder="Contoh: Deni Wahyudi, S.Kom." class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">workspace_premium</span> No. Sertifikasi
                            </label>
                            <input type="text" name="trainer_no_serti" value="{{ \App\Models\Setting::get('trainer_no_serti', '') }}" placeholder="Contoh: CERT-AXIOO-2024-001" class="{{ $inputClass }}">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-sm text-orange-500">badge</span> NBM / NIP
                            </label>
                            <input type="text" name="trainer_nbm" value="{{ \App\Models\Setting::get('trainer_nbm', '') }}" placeholder="Contoh: 12345678" class="{{ $inputClass }}">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Mode Maintenance ===== --}}
        <section id="maintenance" class="settings-section scroll-mt-28">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-amber-100 shadow-sm shadow-amber-100/50 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-amber-100 bg-gradient-to-r from-amber-50/80 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30 shrink-0">
                            <span class="material-symbols-outlined">build</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">{{ __('Mode Maintenance') }}</h3>
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
                            <input type="checkbox" id="maintenance_toggle" name="maintenance_mode" value="1" class="sr-only peer" {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                                   onchange="document.getElementById('maintenance_msg_box').classList.toggle('opacity-50'); document.getElementById('maintenance_msg').disabled = !this.checked;">
                            <div class="w-14 h-8 bg-slate-300 peer-checked:bg-red-500 rounded-full transition-colors"></div>
                            <div class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow transition-all peer-checked:translate-x-6"></div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $settings['maintenance_mode'] ? 'Mode Maintenance: AKTIF' : 'Mode Maintenance: Nonaktif' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $settings['maintenance_mode'] ? 'Saat ini hanya admin yang bisa mengakses sistem.' : 'Sistem dapat diakses oleh semua pengguna.' }}</p>
                        </div>
                    </label>

                    <div id="maintenance_msg_box" class="space-y-2 {{ $settings['maintenance_mode'] ? '' : 'opacity-50' }}">
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
            <button type="submit" class="px-10 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> {{ __('Save Changes') }}
            </button>
        </div>

        {{-- Tombol simpan melayang (mobile) --}}
        <div class="lg:hidden fixed bottom-24 inset-x-4 z-30">
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-xl shadow-orange-500/40 flex items-center justify-center gap-2 active:scale-[0.98] transition-transform">
                <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- ===================== DATA BACKUP & RESET ===================== --}}
    <section id="backup" class="settings-section scroll-mt-28">
        <div class="{{ $cardClass }}">
            <div class="{{ $headClass }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $iconBadge }}"><span class="material-symbols-outlined">data_object</span></div>
                    <div>
                        <h3 class="font-bold text-slate-800">{{ __('Data Backup & Reset') }}</h3>
                        <p class="text-xs text-slate-400 hidden sm:block">Kelola dan amankan data aplikasi</p>
                    </div>
                </div>
                <div class="flex gap-2.5">
                    <a href="{{ route('settings.backup-all') }}" class="bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">cloud_download</span> <span class="hidden sm:inline">Backup</span> Semua Data
                    </a>
                    <button type="button" onclick="document.getElementById('resetAllModal').classList.remove('hidden')" class="bg-red-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0 hover:bg-red-600 transition-all flex items-center gap-1.5">
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
                                <div class="px-4 py-2.5 rounded-xl border-2 border-orange-100 bg-orange-50/40 text-slate-600 text-xs font-bold text-center truncate
                                            peer-checked:border-transparent peer-checked:bg-gradient-to-r peer-checked:from-orange-500 peer-checked:to-amber-500 peer-checked:text-white
                                            peer-checked:shadow-md peer-checked:shadow-orange-500/30 hover:border-orange-300 transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">download</span> Backup Data
                        </button>
                    </form>
                </div>

                {{-- Reset --}}
                <div class="space-y-4 pt-6 border-t border-orange-50">
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
                                            peer-checked:shadow-md peer-checked:shadow-red-500/30 hover:border-red-300 transition-all">
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

{{-- Spacer agar konten tidak tertutup bottom nav (mobile) --}}
<div class="h-32 lg:h-0"></div>

{{-- ===================== BOTTOM NAVIGATION (mobile, gaya gambar 2) ===================== --}}
{{-- TIPS: sebaiknya pindahkan blok ini ke layouts/app.blade.php agar tampil di semua halaman --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-lg border-t border-orange-100 shadow-[0_-8px_24px_rgba(249,115,22,0.10)]" style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="grid grid-cols-5">
        {{-- Ganti href="#" dengan route asli, mis. {{ route('dashboard') }} --}}
        <a href="#" class="flex flex-col items-center gap-0.5 py-2.5 text-slate-400 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-[22px]">home</span>
            <span class="text-[10px] font-bold">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 py-2.5 text-slate-400 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-[22px]">stars</span>
            <span class="text-[10px] font-bold">Poin</span>
        </a>
        <div class="relative">
            <a href="#" aria-label="Menu" class="absolute -top-5 left-1/2 -translate-x-1/2 w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center shadow-lg shadow-orange-500/40 ring-4 ring-white active:scale-95 transition-transform">
                <span class="material-symbols-outlined">menu</span>
            </a>
            <p class="text-center text-[10px] font-bold text-orange-600 pt-8 pb-2.5">Menu</p>
        </div>
        <a href="#" class="flex flex-col items-center gap-0.5 py-2.5 text-slate-400 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-[22px]">fact_check</span>
            <span class="text-[10px] font-bold">Presensi</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 py-2.5 text-slate-400 hover:text-orange-600 transition-colors">
            <span class="material-symbols-outlined text-[22px]">person</span>
            <span class="text-[10px] font-bold">Profil</span>
        </a>
    </div>
</nav>

{{-- ===================== MODAL: TAMBAH TAHUN AJARAN ===================== --}}
<div id="addYearModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 bg-gradient-to-br from-orange-500 to-amber-500 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Tambah Tahun Ajaran
            </h3>
            <button onclick="document.getElementById('addYearModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
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
                <button type="button" onclick="document.getElementById('addYearModal').classList.add('hidden')"
                        class="bg-slate-100 text-slate-600 font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL: KONFIRMASI RESET SEMUA ===================== --}}
<div id="resetAllModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 bg-gradient-to-br from-red-500 to-rose-500 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined">warning</span> Konfirmasi Reset Semua Data
            </h3>
            <button onclick="document.getElementById('resetAllModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
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
                <button type="button" onclick="document.getElementById('resetAllModal').classList.add('hidden')"
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
<div id="messageModal" class="modal-backdrop fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden animate-fade-in-down shadow-2xl">
        <div class="p-5 sm:p-6 flex justify-between items-center {{ session('success') ? 'bg-gradient-to-br from-green-500 to-emerald-500' : 'bg-gradient-to-br from-red-500 to-rose-500' }}">
            <h3 class="font-bold text-white flex items-center gap-2">
                @if(session('success'))
                    <span class="material-symbols-outlined">check_circle</span> Berhasil!
                @else
                    <span class="material-symbols-outlined">error</span> Gagal!
                @endif
            </h3>
            <button onclick="document.getElementById('messageModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <p class="text-slate-700">{{ session('success') ?? session('error') }}</p>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('messageModal').classList.add('hidden')"
                        class="bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    /* ===== Tab navigasi + scrollspy ===== */
    const tabLinks = document.querySelectorAll('.tab-link');
    tabLinks.forEach(link => {
        link.addEventListener('click', () => {
            tabLinks.forEach(t => t.classList.remove('active'));
            link.classList.add('active');
        });
    });

    const sections = document.querySelectorAll('.settings-section');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const link = document.querySelector('.tab-link[href="#' + entry.target.id + '"]');
                if (link) {
                    tabLinks.forEach(t => t.classList.remove('active'));
                    link.classList.add('active');
                    link.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
                }
            }
        });
    }, { rootMargin: '-25% 0px -65% 0px' });
    sections.forEach(s => observer.observe(s));

    /* ===== Pilih semua / batalkan (chips backup & reset) ===== */
    function toggleGroup(group, btn) {
        const boxes = document.querySelectorAll('input[data-group="' + group + '"]');
        const anyUnchecked = Array.from(boxes).some(b => !b.checked);
        boxes.forEach(b => b.checked = anyUnchecked);
        const label = btn.querySelector('span:last-child');
        if (label) label.textContent = anyUnchecked ? 'Batalkan' : 'Pilih Semua';
    }

    /* ===== Tutup modal saat klik backdrop ===== */
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>

@endsection