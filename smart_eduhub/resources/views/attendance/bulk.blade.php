@extends('layouts.app')

@section('title', 'Absen Massal Per Angkatan')

@section('content')
<div class="mb-8">
    <nav class="flex items-center space-x-2 text-outline mb-2">
        <a href="{{ route('attendance') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Pemantauan Kehadiran</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-label-sm text-label-sm text-primary">Absen Massal</span>
    </nav>
    <h2 class="font-display-xl text-display-xl text-on-surface">Absen Massal Per Angkatan</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Lakukan pencatatan kehadiran sekaligus untuk seluruh siswa dalam satu angkatan.</p>
</div>

<!-- Ringkasan Per Angkatan -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-50 p-6 mb-8">
    <h4 class="font-headline-md text-headline-md mb-5 flex items-center gap-2">
        <span class="material-symbols-outlined text-orange-600">table_chart</span>
        Ringkasan Per Angkatan - {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($angkatanSummary as $summary)
            <div class="border border-slate-100 rounded-xl p-4 hover:border-orange-200 transition-colors bg-gradient-to-br from-slate-50 to-white">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h5 class="font-bold text-lg text-slate-800">{{ $summary['angkatan']->name }}</h5>
                        <p class="text-xs text-slate-500">{{ $summary['angkatan']->code }} - {{ $summary['angkatan']->tahun_masuk }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $summary['pending'] > 0 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $summary['pending'] > 0 ? $summary['pending'] . ' belum absen' : 'Selesai' }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Siswa Aktif</span>
                        <span class="font-bold text-slate-800">{{ $summary['total_students'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Sudah Absen</span>
                        <span class="font-bold text-emerald-600">{{ $summary['already_absent'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Belum Absen</span>
                        <span class="font-bold text-amber-600">{{ $summary['pending'] }}</span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $summary['total_students'] > 0 ? ($summary['already_absent'] / $summary['total_students']) * 100 : 0 }}%"></div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-slate-400">
                <span class="material-symbols-outlined text-4xl mb-2 block">group_off</span>
                <p>Belum ada data angkatan aktif.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Form Absen Massal -->
<div class="max-w-4xl">
    <form action="{{ route('attendance.bulk.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 md:p-8 rounded-2xl custom-shadow border border-slate-100 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <span class="material-symbols-outlined text-[28px]">playlist_add_check</span>
                </div>
                <div>
                    <h3 class="font-headline-md text-headline-md">Form Absen Massal</h3>
                    <p class="text-sm text-slate-500">Isi detail di bawah untuk melakukan absen sekaligus</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Angkatan Selection -->
                <div class="space-y-2 md:col-span-2">
                    <label for="angkatan_id" class="block text-sm font-bold text-slate-700">Pilih Angkatan <span class="text-error">*</span></label>
                    <select name="angkatan_id" id="angkatan_id" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('angkatan_id') border-error @enderror" required>
                        <option value="">-- Pilih Angkatan --</option>
                        @foreach($angkatanSummary as $summary)
                            <option value="{{ $summary['angkatan']->id }}" {{ old('angkatan_id') == $summary['angkatan']->id ? 'selected' : '' }}>
                                {{ $summary['angkatan']->name }} ({{ $summary['angkatan']->code }}) - {{ $summary['total_students'] }} siswa, {{ $summary['pending'] }} belum absen
                            </option>
                        @endforeach
                    </select>
                    @error('angkatan_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <label for="date" class="block text-sm font-bold text-slate-700">Tanggal Absen <span class="text-error">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $date) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('date') border-error @enderror" required>
                    @error('date') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="status" class="block text-sm font-bold text-slate-700">Status Kehadiran <span class="text-error">*</span></label>
                    <select name="status" id="status" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('status') border-error @enderror" required>
                        <option value="present" {{ old('status', 'present') == 'present' ? 'selected' : '' }}>Hadir (Tepat Waktu/Terlambat)</option>
                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Alpa (Tidak Hadir)</option>
                        <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Izin / Sakit</option>
                    </select>
                    <p id="status-hint" class="text-[11px] text-orange-600 font-medium hidden">
                        <span class="material-symbols-outlined text-[14px] align-middle">info</span>
                        Waktu check-in akan otomatis menentukan status: tepat waktu atau terlambat
                    </p>
                    @error('status') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Check-in Time -->
                <div class="space-y-2">
                    <label for="check_in" class="block text-sm font-bold text-slate-700">Waktu Check-in</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('check_in') border-error @enderror">
                    <p class="text-[11px] text-slate-400 font-medium">
                        <span class="material-symbols-outlined text-[14px] align-middle">info</span>
                        Kosongkan untuk status Alpa/Izin. Untuk status Hadir, akan otomatis diisi jam sekarang jika dikosongkan.
                    </p>
                    @error('check_in') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Location -->
                <div class="space-y-2">
                    <label for="location" class="block text-sm font-bold text-slate-700">Lokasi / Keterangan</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('location') border-error @enderror" placeholder="e.g. Kelas, Online, Rumah Sakit, dll">
                    <p class="text-[11px] text-slate-400 font-medium">
                        <span class="material-symbols-outlined text-[14px] align-middle">info</span>
                        Opsional. Default: "Bulk Absen - [Nama Angkatan]"
                    </p>
                    @error('location') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Warning Box -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 text-[24px] flex-shrink-0">warning</span>
                <div class="text-sm">
                    <p class="font-bold text-amber-800 mb-1">Perhatian</p>
                    <ul class="text-amber-700 space-y-1 text-[13px]">
                        <li>• Siswa yang <strong>sudah tercatat absen</strong> pada tanggal yang sama akan otomatis dilewati (tidak diduplikasi).</li>
                        <li>• Proses ini juga akan otomatis menerapkan <strong>reward & penalty poin</strong> sesuai aturan yang berlaku.</li>
                        <li>• Absen tidak bisa dilakukan pada <strong>hari libur</strong> yang sudah terdaftar di sistem.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-3">
            <a href="{{ route('attendance') }}" class="px-6 py-3 rounded-xl font-label-md text-label-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all text-center order-2 sm:order-1">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl font-label-md text-label-md text-white bg-primary hover:bg-orange-700 custom-shadow transition-all flex items-center justify-center gap-2 order-1 sm:order-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                Proses Absen Massal
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const checkInInput = document.getElementById('check_in');
    const statusSelect = document.getElementById('status');
    const statusHint = document.getElementById('status-hint');
    const checkInLimit = "{{ \App\Models\Setting::get('check_in_time', '08:00') }}";

    function updateStatusHint() {
        const statusValue = statusSelect.value;

        if (statusValue === 'present' || statusValue === 'late') {
            statusHint.classList.remove('hidden');
        } else {
            statusHint.classList.add('hidden');
        }
    }

    statusSelect.addEventListener('change', updateStatusHint);
    updateStatusHint();
</script>
@endpush
