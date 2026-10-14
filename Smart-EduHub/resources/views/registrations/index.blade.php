@extends('layouts.app')

@section('title', 'Student Registrations')

@section('content')
<style>
    /* Mobile: sembunyikan navbar putih bawaan layout & rapatkan header ke tepi atas */
    @media (max-width: 767px) {
        body > header, #topbar, #navbar, .topbar, [data-topbar] { display: none !important; }
        main, .main-content, [data-main] { padding-top: 0 !important; }
    }
</style>
@php
    $status = $status ?? 'pending';
    $search = $search ?? '';
    $pendingCount   = $stats['pending']   ?? 0;
    $processedCount = $stats['processed'] ?? 0;
    $badge = fn ($s) => match ($s) {
        'verified', 'approved' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        'pending'              => 'bg-amber-50 text-amber-600 ring-amber-100',
        'rejected'             => 'bg-rose-50 text-rose-600 ring-rose-100',
        default                => 'bg-slate-100 text-slate-600 ring-slate-200',
    };
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
                <span class="material-symbols-outlined text-orange-600 text-[18px]">pending_actions</span>
                <span class="text-[10px] font-bold text-slate-400">Menunggu</span>
                <span class="text-sm font-black">{{ $pendingCount }}</span>
            </div>
        </div>

        <div class="relative mt-4 text-center md:text-left md:-mt-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-100">Pendaftaran Siswa</p>
            <h2 class="mt-1 text-3xl sm:text-4xl font-black tracking-tight">Persetujuan Pendaftaran</h2>
            <p class="mt-1.5 text-xs sm:text-sm text-orange-50/90">Tinjau dan proses pendaftaran siswa baru.</p>
        </div>

        <div class="relative mt-5 flex flex-col sm:flex-row gap-2.5">
            <button type="button" onclick="copyJoinLink()" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-extrabold text-orange-600 shadow-lg hover:bg-orange-50 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">share</span>
                Salin Link Pendaftaran
            </button>
            <a href="{{ route('registrations.report.pdf', ['status' => $status, 'search' => $search]) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-bold text-white ring-1 ring-white/30 backdrop-blur hover:bg-white/25 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                Cetak PDF
            </a>
        </div>
    </header>

    {{-- ================= ALERTS ================= --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <span class="material-symbols-outlined text-rose-600">error</span>
            <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ================= CARI & FILTER ================= --}}
    <section class="rounded-3xl border border-slate-100 bg-white p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-600">search</span>
            <h3 class="text-base font-extrabold text-slate-900">Cari Pendaftar</h3>
        </div>

        <form method="GET" action="{{ route('registrations') }}" class="grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-2.5">
            <input type="hidden" name="status" value="{{ $status }}">
            <label class="relative">
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">badge</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama, NISN, email, atau nomor telepon"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-3 text-sm font-semibold text-slate-700 placeholder:font-medium placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10">
            </label>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-200 hover:bg-orange-700 active:scale-[.98] transition">
                <span class="material-symbols-outlined text-[20px]">search</span>
                Cari
            </button>
            @if($search)
                <a href="{{ route('registrations', ['status' => $status]) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-200 transition">Reset</a>
            @endif
        </form>

        {{-- Tab status --}}
        <div class="grid grid-cols-2 gap-1 rounded-2xl bg-slate-100 p-1 sm:inline-grid sm:grid-cols-[auto_auto]">
            <a href="{{ route('registrations', ['status' => 'pending', 'search' => $search]) }}"
               class="rounded-xl px-5 py-2.5 text-center text-sm font-bold transition {{ $status === 'pending' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                Menunggu ({{ $pendingCount }})
            </a>
            <a href="{{ route('registrations', ['status' => 'processed', 'search' => $search]) }}"
               class="rounded-xl px-5 py-2.5 text-center text-sm font-bold transition {{ $status === 'processed' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                Diproses ({{ $processedCount }})
            </a>
        </div>

        @if($search)
            <div class="flex items-start gap-2 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-xs sm:text-sm text-slate-600">
                <span class="material-symbols-outlined text-[18px] text-orange-500">info</span>
                <p>Hasil pencarian <strong>"{{ $search }}"</strong>: {{ $registrations->count() }} data ditemukan.</p>
            </div>
        @endif
    </section>

    {{-- ================= TABEL ================= --}}
    <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-4 sm:px-6">
            <span class="material-symbols-outlined text-orange-600">how_to_reg</span>
            <h3 class="text-base font-extrabold text-slate-900">Daftar Pendaftaran</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left">
                <thead class="bg-slate-50/80">
                    <tr class="text-xs font-bold text-slate-400">
                        <th class="px-4 sm:px-6 py-3.5">Siswa</th>
                        <th class="hidden lg:table-cell px-6 py-3.5">Kontak</th>
                        <th class="hidden md:table-cell px-6 py-3.5">Pendidikan</th>
                        <th class="hidden sm:table-cell px-4 sm:px-6 py-3.5">Tanggal</th>
                        <th class="px-4 sm:px-6 py-3.5">Status</th>
                        <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $reg)
                        @php
                            $parts = preg_split('/\s+/', trim((string) $reg->name));
                            $initials = strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));
                        @endphp
                        <tr class="transition-colors hover:bg-orange-50/40">
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-xs font-black text-white shadow-md shadow-orange-200/60">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-extrabold text-slate-900">{{ $reg->name }}</p>
                                        <p class="truncate text-xs text-slate-500">NISN {{ $reg->nisn ?: '-' }}</p>
                                        <p class="truncate text-xs text-slate-400 hidden sm:block">NIK {{ $reg->no_nik ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-3.5">
                                <div class="space-y-1 text-xs text-slate-600">
                                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-slate-400">mail</span><span class="truncate max-w-[200px]">{{ $reg->email ?: '-' }}</span></div>
                                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-slate-400">call</span>{{ $reg->phone ?: '-' }}</div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-6 py-3.5">
                                <span class="whitespace-nowrap rounded-full bg-orange-50 px-2.5 py-1 text-xs font-black text-orange-600">Angkatan {{ $reg->angkatan ?? '-' }}</span>
                                <p class="mt-1 max-w-[160px] truncate text-xs text-slate-500">{{ $reg->previous_school ?: '-' }}</p>
                            </td>
                            <td class="hidden sm:table-cell px-4 sm:px-6 py-3.5 text-sm text-slate-600 whitespace-nowrap">{{ optional($reg->created_at)->format('d M Y') }}</td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <span class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-black ring-1 {{ $badge($reg->status) }}">{{ ucfirst($reg->status) }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="viewDetails({{ $reg->id }})" class="rounded-xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-50" title="Lihat detail">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>

                                    @if($reg->status !== 'approved' && $reg->status !== 'verified')
                                        <form action="{{ route('registrations.status', $reg) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="verified">
                                            <button type="submit" class="rounded-xl bg-emerald-50 p-2 text-emerald-600 transition hover:bg-emerald-100" title="Verifikasi">
                                                <span class="material-symbols-outlined text-[20px]">verified</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if($reg->status === 'verified')
                                        <form action="{{ route('registrations.status', $reg) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="rounded-xl bg-orange-50 p-2 text-orange-600 transition hover:bg-orange-100" title="Setujui &amp; buat siswa">
                                                <span class="material-symbols-outlined text-[20px]">person_add</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if($reg->status !== 'rejected')
                                        <form action="{{ route('registrations.status', $reg) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pendaftaran ini?');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="rounded-xl bg-amber-50 p-2 text-amber-600 transition hover:bg-amber-100" title="Tolak">
                                                <span class="material-symbols-outlined text-[20px]">cancel</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('registrations.destroy', $reg) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-xl bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300">inbox</span>
                                <p class="mt-2 font-bold text-slate-600">Belum ada pendaftaran</p>
                                <p class="text-xs text-slate-400">Bagikan link pendaftaran agar calon siswa bisa mendaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($registrations, 'links'))
            <div class="border-t border-slate-100 p-4 sm:p-6">{{ $registrations->links() }}</div>
        @endif
    </section>
</div>

{{-- ================= MODAL DETAIL (bottom sheet di mobile, dialog di desktop) ================= --}}
<div id="detailModal" class="fixed inset-0 z-[70] hidden items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm sm:p-4" onclick="if(event.target===this) closeModal()">
    <div class="flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-[2rem] sm:rounded-3xl bg-white shadow-2xl">
        <div class="mx-auto mt-3 h-1.5 w-12 shrink-0 rounded-full bg-slate-200 sm:hidden"></div>

        <div class="flex shrink-0 items-center justify-between px-5 py-4 sm:px-7 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-orange-50 text-orange-600">
                    <span class="material-symbols-outlined">person</span>
                </span>
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Detail Pendaftar</h3>
            </div>
            <button type="button" onclick="closeModal()" class="grid h-9 w-9 place-items-center rounded-full text-slate-400 hover:bg-slate-100" aria-label="Tutup">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-6 sm:px-7">
            <div id="modalContent"></div>
        </div>

        <div class="shrink-0 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-7">
            <button type="button" onclick="closeModal()" class="w-full sm:w-auto sm:float-right rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Tutup</button>
            <div class="clear-both"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const detailModal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');

    const loader = `<div class="flex justify-center py-12"><div class="h-8 w-8 animate-spin rounded-full border-4 border-orange-500 border-t-transparent"></div></div>`;

    // Escape HTML agar data pendaftar tidak bisa menyisipkan script
    function esc(v) {
        return String(v ?? '-').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function row(label, value) {
        return `<div><dt class="text-xs text-slate-400">${label}</dt><dd class="mt-0.5 text-sm font-bold text-slate-900 break-words">${esc(value || '-')}</dd></div>`;
    }
    function group(title, rows) {
        return `<div><p class="mb-3 text-xs font-extrabold text-orange-600">${title}</p><dl class="space-y-3">${rows.join('')}</dl></div>`;
    }

    function viewDetails(id) {
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
        modalContent.innerHTML = loader;

        fetch(`/registrations/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(d => {
                modalContent.innerHTML = `
                    <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 sm:gap-8">
                        <div class="space-y-6">
                            ${group('Data Pribadi', [
                                row('Nama lengkap', d.name),
                                row('Email', d.email),
                                row('Nomor telepon', d.phone),
                                row('NISN / NIK', `${d.nisn || '-'} / ${d.no_nik || '-'}`)
                            ])}
                            ${group('Kelahiran', [
                                row('Tempat, tanggal lahir', `${d.place_of_birth || '-'}, ${d.dateofbirth || '-'}`),
                                row('Jenis kelamin / agama', `${d.gender === 'L' ? 'Laki-laki' : 'Perempuan'} / ${d.religion || '-'}`)
                            ])}
                        </div>
                        <div class="space-y-6">
                            ${group('Akademik', [
                                row('Sekolah asal', d.previous_school),
                                row('Angkatan', `Angkatan ${d.angkatan || '-'}`)
                            ])}
                            ${group('Keluarga & Alamat', [
                                row('Nama ibu', d.mother_name),
                                row('Alamat', d.address)
                            ])}
                        </div>
                    </div>`;
            })
            .catch(err => {
                modalContent.innerHTML = `<p class="py-8 text-center font-bold text-rose-600">Gagal memuat data. Silakan coba lagi.</p>`;
                console.error('Error:', err);
            });
    }

    function closeModal() {
        detailModal.classList.add('hidden');
        detailModal.classList.remove('flex');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    function copyJoinLink() {
        const fullUrl = window.location.origin + "{{ route('registrations.form', [], false) }}";

        navigator.clipboard.writeText(fullUrl).then(() => {
            alert('Link pendaftaran berhasil disalin ke clipboard!');
        }).catch(() => {
            const ta = document.createElement('textarea');
            ta.value = fullUrl;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            alert('Link pendaftaran berhasil disalin!');
        });
    }

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
@endpush
@endsection