@extends('layouts.siswa')

@section('title', 'Profil Saya')
@section('page_title', 'Informasi Pribadi')

@section('content')
@php
    $avatar = $student->foto
        ? asset('storage/' . $student->foto)
        : 'https://ui-avatars.com/api/?name=' . urlencode($student->nama_lengkap) . '&background=fff1e8&color=EB4325&size=256&bold=true';

    $personal = [
        ['label' => 'Alamat Email',           'value' => $student->email ?? '-', 'icon' => 'mail',         'wrap' => true],
        ['label' => 'Nomor HP / WhatsApp',    'value' => $student->phone ?? '-', 'icon' => 'phone_iphone', 'wrap' => false],
        ['label' => 'Tempat, Tanggal Lahir',  'value' => ($student->place_of_birth ?? '-') . ', ' . ($student->dateofbirth ?? '-'), 'icon' => 'cake', 'wrap' => false],
    ];
@endphp

<style>
    /* QR selalu menyesuaikan lebar panel (bukan ukuran tetap) */
    .qr-box svg { width: 100%; height: auto; max-width: 200px; display: block; margin: 0 auto; }
    /* Hindari tertutup home-indicator / bar navigasi di HP */
    .safe-bottom { padding-bottom: max(1.5rem, env(safe-area-inset-bottom)); }
    /* Mencegah kotak data melebar karena teks panjang di grid/flex */
    .min-w-0-all > * { min-width: 0; }
    @media (hover: none) { .hover-lift:hover { transform: none !important; } }
    @media (prefers-reduced-motion: reduce) { .hover-lift { transition: none !important; } }
</style>

<div class="w-full max-w-2xl lg:max-w-5xl mx-auto space-y-4 sm:space-y-6 pb-28 px-2 sm:px-0 min-w-0">

    <!-- ===== Header ===== -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 sm:pt-6 pb-7 sm:pb-8 px-4 sm:px-8 text-white overflow-hidden shadow-xl shadow-orange-900/10 rounded-b-[1.75rem] sm:rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-7 lg:pb-8">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-5">
            <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/20 shadow-lg">
                    <span class="material-symbols-outlined text-white text-xl sm:text-2xl">account_circle</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold text-white/70 block uppercase tracking-[0.18em] sm:tracking-[0.2em] leading-none truncate">Portal Identitas Siswa</span>
                    <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight mt-1.5 truncate">Profil Digital Saya</h1>
                </div>
            </div>

            <a href="{{ route('siswa.profile.edit') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-[#EB4325] rounded-xl text-xs font-extrabold uppercase tracking-wider shadow-lg shadow-black/10 hover:bg-orange-50 hover:-translate-y-0.5 active:scale-95 transition-all hover-lift">
                <span class="material-symbols-outlined text-base">edit_square</span>
                Ubah Profil
            </a>
        </div>
    </div>

    <!-- ===== Success Alert ===== -->
    @if(session('success'))
        <div id="profile-alert" class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-3.5 sm:p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-emerald-600 shrink-0">check_circle</span>
            <span class="text-xs font-bold tracking-tight flex-1 min-w-0 break-words">{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('profile-alert').remove()" class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-emerald-500 hover:bg-emerald-100 transition" aria-label="Tutup">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    @endif

    <!-- ===== Layout: 1 kolom (HP/tablet) → 2 kolom (laptop) ===== -->
    <div class="grid lg:grid-cols-12 gap-4 sm:gap-6 items-start min-w-0">

        <!-- ===== KOLOM KIRI: Kartu Identitas (sticky di laptop) ===== -->
        <aside class="lg:col-span-5 lg:sticky lg:top-6 min-w-0">
            <div class="bg-white rounded-2xl sm:rounded-[2.2rem] border border-slate-100 shadow-sm overflow-hidden">
                <!-- Cover -->
                <div class="relative h-20 min-[400px]:h-24 sm:h-28 bg-gradient-to-r from-orange-100 via-orange-50 to-amber-50 overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-gradient-to-br from-[#F6863B]/30 to-[#EB4325]/20 blur-xl"></div>
                    <div class="absolute left-10 -bottom-10 w-28 h-28 rounded-full bg-[#F6863B]/20 blur-xl"></div>
                    <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(rgba(235,67,37,.18) 1px, transparent 1px); background-size: 16px 16px;"></div>
                </div>

                <div class="px-4 min-[400px]:px-5 sm:px-8 lg:px-6 pb-5 sm:pb-8 lg:pb-6">
                    <div class="flex flex-col sm:flex-row lg:flex-col items-center sm:items-end lg:items-center gap-3 sm:gap-6 lg:gap-4 -mt-10 min-[400px]:-mt-12 sm:-mt-14">
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <img src="{{ $avatar }}" class="w-20 h-20 min-[400px]:w-24 min-[400px]:h-24 sm:w-28 sm:h-28 rounded-3xl border-4 border-white object-cover shadow-xl shadow-orange-900/10 bg-white" alt="Foto Profil">
                            <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-[3px] border-white rounded-full"></span>
                        </div>

                        <!-- Nama & badge -->
                        <div class="flex-1 min-w-0 w-full text-center sm:text-left lg:text-center sm:pb-1">
                            <h2 class="text-lg min-[400px]:text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight break-words">{{ $student->nama_lengkap }}</h2>
                            <div class="flex flex-wrap items-center justify-center sm:justify-start lg:justify-center gap-2 mt-2.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-50 text-[#EB4325] border border-orange-100 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $student->status ?? 'Siswa Aktif' }}
                                </span>
                                <span class="px-2.5 py-1 bg-slate-50 text-slate-500 border border-slate-100 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest">
                                    Angkatan {{ $student->angkatan ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- NISN & NIS -->
                    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2.5 sm:gap-3 mt-5 sm:mt-6 min-w-0-all">
                        <div class="flex items-center justify-between gap-2 p-3 sm:p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">NISN</span>
                                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5 truncate">{{ $student->nisn }}</p>
                            </div>
                            <button type="button" data-copy="{{ $student->nisn }}" class="copy-btn shrink-0 w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-[#EB4325] hover:border-orange-300 flex items-center justify-center transition active:scale-90" aria-label="Salin NISN">
                                <span class="material-symbols-outlined text-lg">content_copy</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-2 p-3 sm:p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">NIS</span>
                                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5 truncate">{{ $student->nis_siswa ?? '-' }}</p>
                            </div>
                            <button type="button" data-copy="{{ $student->nis_siswa ?? '' }}" class="copy-btn shrink-0 w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-[#EB4325] hover:border-orange-300 flex items-center justify-center transition active:scale-90" aria-label="Salin NIS">
                                <span class="material-symbols-outlined text-lg">content_copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol QR -->
                    <button type="button" id="qrOpen" class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-3.5 bg-gradient-to-r from-[#F6863B] to-[#EB4325] text-white rounded-2xl text-[11px] sm:text-xs font-extrabold uppercase tracking-widest shadow-lg shadow-orange-500/25 hover:-translate-y-0.5 hover:shadow-xl active:scale-[0.98] transition-all hover-lift">
                        <span class="material-symbols-outlined text-lg">qr_code_scanner</span>
                        Tampilkan QR Code NIS
                    </button>
                </div>
            </div>
        </aside>

        <!-- ===== KOLOM KANAN: Data ===== -->
        <div class="lg:col-span-7 space-y-4 sm:space-y-6 min-w-0">

            <!-- Informasi Personal -->
            <section class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/60 border-b border-slate-100 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-orange-100 text-[#EB4325] flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-lg">badge</span>
                    </span>
                    <h3 class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-[0.15em]">Informasi Personal</h3>
                </div>

                <div class="p-2 sm:p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-1 sm:gap-3 min-w-0-all">
                    @foreach($personal as $row)
                        <div class="flex items-center gap-3 sm:gap-3.5 p-3 sm:p-3.5 rounded-2xl hover:bg-orange-50/50 transition {{ $loop->last ? 'md:col-span-2 lg:col-span-1 xl:col-span-2' : '' }}">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#EB4325] flex items-center justify-center shrink-0 border border-orange-100/60">
                                <span class="material-symbols-outlined text-xl">{{ $row['icon'] }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ $row['label'] }}</span>
                                <p class="font-bold text-slate-800 text-[13px] sm:text-sm mt-0.5 {{ $row['wrap'] ? 'break-all' : 'break-words' }}">{{ $row['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Data Akademis -->
            <section class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/60 border-b border-slate-100 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-lg">school</span>
                    </span>
                    <h3 class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-[0.15em]">Data Akademis &amp; Instansi</h3>
                </div>

                <div class="p-2 sm:p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-1 sm:gap-3 min-w-0-all">
                    <div class="flex items-center gap-3 sm:gap-3.5 p-3 sm:p-3.5 rounded-2xl hover:bg-emerald-50/50 transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100/60">
                            <span class="material-symbols-outlined text-xl">domain</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Sekolah Asal</span>
                            <p class="font-bold text-slate-800 text-[13px] sm:text-sm mt-0.5 break-words">{{ $student->previous_school ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-3.5 p-3 sm:p-3.5 rounded-2xl hover:bg-emerald-50/50 transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100/60">
                            <span class="material-symbols-outlined text-xl">workspace_premium</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Sertifikasi Terdaftar</span>
                            <p class="font-bold text-slate-800 text-[13px] sm:text-sm mt-0.5 flex items-start gap-1">
                                <span class="material-symbols-outlined text-base text-emerald-500 shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                                <span class="break-words min-w-0">Smart Axioo Class Program Member</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- ===== QR Modal (bottom sheet di HP, dialog di layar lebar) ===== -->
    <div id="qrModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/60 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="qrTitle">
        <div id="qrPanel" class="safe-bottom bg-white rounded-t-[1.75rem] sm:rounded-[2.5rem] px-5 pt-5 sm:p-8 w-full sm:max-w-sm shadow-2xl relative border border-slate-100 text-center max-h-[92vh] max-h-[92dvh] overflow-y-auto">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-4 sm:hidden"></div>
            <button type="button" id="qrClose" class="absolute top-3 right-3 sm:top-4 sm:right-4 w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors rounded-full hover:bg-slate-50" aria-label="Tutup">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>

            <div class="space-y-4 sm:space-y-5">
                <div class="space-y-2">
                    <h3 id="qrTitle" class="text-lg font-extrabold text-slate-900 tracking-tight">QR Code NIS</h3>
                    <p class="text-xs font-bold text-[#EB4325] font-mono bg-orange-50 border border-orange-100 px-3 py-1 rounded-full w-fit max-w-full mx-auto truncate">{{ $student->nis_siswa ?? '-' }}</p>
                </div>

                <div class="qr-box bg-white p-3 sm:p-4 rounded-3xl border border-slate-100 w-full max-w-[232px] mx-auto shadow-lg shadow-slate-200/60 overflow-hidden">
                    {!! QrCode::size(180)->generate($student->nis_siswa ?? '-') !!}
                </div>

                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider leading-relaxed">Arahkan kamera scanner kasir atau guru untuk membaca identitas kartu</p>

                <button type="button" id="qrClose2" class="sm:hidden w-full px-5 py-3 rounded-2xl bg-slate-900 text-white text-xs font-extrabold uppercase tracking-widest active:scale-95 transition">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('qrModal');
        const openBtn = document.getElementById('qrOpen');
        const panel = document.getElementById('qrPanel');
        if (!modal) return;

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        openBtn?.addEventListener('click', openModal);
        document.getElementById('qrClose')?.addEventListener('click', closeModal);
        document.getElementById('qrClose2')?.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (!panel.contains(e.target)) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });

        // Salin NISN / NIS
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const text = btn.dataset.copy;
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text; document.body.appendChild(ta); ta.select();
                    try { document.execCommand('copy'); } catch (_) {}
                    ta.remove();
                }
                const icon = btn.querySelector('.material-symbols-outlined');
                if (!icon) return;
                icon.textContent = 'check';
                btn.classList.add('text-emerald-600', 'border-emerald-300');
                setTimeout(() => {
                    icon.textContent = 'content_copy';
                    btn.classList.remove('text-emerald-600', 'border-emerald-300');
                }, 1500);
            });
        });
    })();
</script>
@endsection