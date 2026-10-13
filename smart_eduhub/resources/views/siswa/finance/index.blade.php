﻿@extends('layouts.siswa')

@section('title', 'Keuangan')
@section('page_title', 'Keuangan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16 px-2 sm:px-0 bg-slate-50/30">
    
    <!-- Notification & Alert Systems -->
    @if(session('success'))
    <div class="mx-1 bg-emerald-50 border border-emerald-100 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm transition-all duration-300">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        <span class="text-xs font-bold tracking-tight">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mx-1 bg-rose-50 border border-rose-100 text-rose-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm transition-all duration-300">
        <span class="material-symbols-outlined text-rose-600">error</span>
        <span class="text-xs font-bold tracking-tight">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mx-1 bg-amber-50 border border-amber-100 text-amber-900 p-4 rounded-2xl shadow-sm transition-all duration-300">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-600">warning</span>
            <div class="space-y-1">
                @foreach($errors->all() as $error)
                <p class="text-xs font-bold">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Purple Curved Header Block (Fintech Style) - Now Full Width on Mobile (Optimized Compact) -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 pb-8 px-5 text-white overflow-hidden shadow-xl shadow-brand-950/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-5 lg:pb-6 transition-all duration-300">
        <!-- Abstract Circular Glow Overlays -->
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-8 left-12 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between">
            <!-- App Logo Branding -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/10">
                    <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-brand-200 block uppercase tracking-wider leading-none">Dompet Digital</span>
                    <span class="text-sm font-black tracking-tight block mt-1">Keuangan Siswa</span>
                </div>
            </div>

            <!-- Compact Notification Bell Widget -->
            <button type="button" class="relative w-9 h-9 rounded-xl bg-white/15 hover:bg-white/25 flex items-center justify-center backdrop-blur-md border border-white/10 transition-all active:scale-95" title="Notifikasi">
                <span class="material-symbols-outlined text-white text-lg">notifications</span>
                <!-- Active dot indicator -->
                <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border border-[#EB4325]"></span>
            </button>
        </div>
    </div>

    <!-- Two Column Dashboard Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Card Details & History (Span 2) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- ===================== KARTU 3D ===================== -->
            @forelse($cards as $card)
            <div class="fc-wrap space-y-4" data-card-wrap data-number="{{ $card->card_number }}">

                <div class="fc-stage">
                    <div class="fc-scene" data-card3d tabindex="0" role="button" aria-pressed="false"
                         aria-label="Kartu milik {{ $student->name }}. Tekan untuk membalik kartu.">

                        <div class="fc-shadow" aria-hidden="true"></div>

                        <div class="fc-float">
                            <div class="fc-tilt">
                                <div class="fc-flip">

                                    <!-- ===== SISI DEPAN ===== -->
                                    <div class="fc-face fc-front" data-face="front">
                                        <div class="fc-bg {{ $card->status === 'active' ? '' : 'is-inactive' }}">
                                            <span class="fc-sheen"></span>
                                            <span class="fc-glare"></span>
                                        </div>

                                        <div class="fc-content">
                                            <!-- Baris atas: chip & status -->
                                            <div class="fc-row fc-z1">
                                                <div class="fc-chipwrap">
                                                    <svg class="fc-chip" viewBox="0 0 48 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                        <rect width="48" height="38" rx="6" fill="url(#chip-grad-{{ $loop->index }})" />
                                                        <rect x="3" y="3" width="12" height="10" rx="2" fill="black" fill-opacity="0.12" />
                                                        <rect x="33" y="3" width="12" height="10" rx="2" fill="black" fill-opacity="0.12" />
                                                        <rect x="3" y="25" width="12" height="10" rx="2" fill="black" fill-opacity="0.12" />
                                                        <rect x="33" y="25" width="12" height="10" rx="2" fill="black" fill-opacity="0.12" />
                                                        <rect x="18" y="3" width="12" height="32" rx="2" fill="black" fill-opacity="0.12" />
                                                        <path d="M12 19H36" stroke="black" stroke-opacity="0.15" stroke-width="1.5" />
                                                        <defs>
                                                            <linearGradient id="chip-grad-{{ $loop->index }}" x1="0" y1="0" x2="48" y2="38" gradientUnits="userSpaceOnUse">
                                                                <stop stop-color="#FFF1B8" />
                                                                <stop offset=".55" stop-color="#FFCB47" />
                                                                <stop offset="1" stop-color="#E69A00" />
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                    <svg class="fc-nfc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                                                        <path d="M5 17.5c1.5-1.5 1.5-4.5 0-6M8 20.5c3-3 3-9 0-12M11 23.5c4.5-4.5 4.5-13.5 0-18" />
                                                    </svg>
                                                </div>

                                                <span class="fc-status">
                                                    <span class="fc-dot {{ $card->status === 'active' ? 'is-ok' : ($card->status === 'frozen' ? 'is-warn' : 'is-off') }}"></span>
                                                    {{ $card->status === 'active' ? 'Aktif' : ($card->status === 'frozen' ? 'Dibekukan' : 'Nonaktif') }}
                                                </span>
                                            </div>

                                            <!-- Saldo -->
                                            <div class="fc-z2">
                                                <span class="fc-label">Sisa saldo</span>
                                                <p class="fc-amount">
                                                    <span class="fc-cur">Rp</span>
                                                    <span class="js-balance" data-full="{{ number_format($card->balance, 0, ',', '.') }}">{{ number_format($card->balance, 0, ',', '.') }}</span>
                                                </p>
                                            </div>

                                            <!-- Nomor kartu -->
                                            <div class="fc-z1">
                                                <span class="fc-label">Nomor kartu / RFID</span>
                                                <p class="fc-number">{{ trim(chunk_split($card->card_number, 4, ' ')) }}</p>
                                            </div>

                                            <!-- Pemilik -->
                                            <div class="fc-row fc-z1 fc-foot">
                                                <div class="min-w-0">
                                                    <span class="fc-label">Pemilik kartu</span>
                                                    <p class="fc-name">{{ $student->name }}</p>
                                                </div>
                                                <span class="fc-brand">Smart EduHub</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ===== SISI BELAKANG ===== -->
                                    <div class="fc-face fc-back" data-face="back" aria-hidden="true">
                                        <div class="fc-bg fc-bg-back">
                                            <span class="fc-glare"></span>
                                        </div>

                                        <div class="fc-content fc-content-back">
                                            <div class="fc-stripe fc-z1"></div>

                                            <div class="fc-backbody fc-z1">
                                                <div class="fc-sign">
                                                    <span class="fc-sign-name">{{ $student->name }}</span>
                                                </div>
                                                <p class="fc-number fc-number-back">{{ trim(chunk_split($card->card_number, 4, ' ')) }}</p>
                                                <p class="fc-note">Kartu ini pribadi. Jaga kerahasiaan nomor kartu dan jangan dipinjamkan ke orang lain.</p>
                                            </div>

                                            <div class="fc-row fc-z1 fc-foot">
                                                <span class="fc-brand">Smart EduHub</span>
                                                <span class="fc-hint">Ketuk untuk membalik</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aksi kartu -->
                <div class="grid grid-cols-3 gap-2 max-w-[27rem] mx-auto">
                    <button type="button" data-act="flip" class="fc-btn">
                        <span class="material-symbols-outlined text-base">flip</span>
                        <span>Balik</span>
                    </button>
                    <button type="button" data-act="copy" class="fc-btn">
                        <span class="fc-btn-copy-icon material-symbols-outlined text-base">content_copy</span>
                        <span class="fc-btn-copy-label">Salin nomor</span>
                    </button>
                    <button type="button" data-act="hide" class="fc-btn" aria-pressed="false">
                        <span class="fc-btn-icon material-symbols-outlined text-base">visibility_off</span>
                        <span class="fc-btn-label">Sembunyikan</span>
                    </button>
                </div>
            </div>
            @empty
            <!-- Card Empty State -->
            <div class="bg-white rounded-[2.2rem] border border-slate-100 p-8 text-center shadow-sm">
                <div class="w-16 h-16 bg-brand-50 text-[#F6863B] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">credit_card_off</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Anda belum memiliki kartu aktif</h3>
                <p class="text-xs text-slate-500 mb-6 max-w-xs mx-auto">Ajukan kartu sekarang untuk mulai menggunakan layanan dompet digital di lingkungan sekolah.</p>
                <a href="{{ route('siswa.finance.request-card') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#F6863B] hover:bg-[#EB4325] text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-brand-100">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Ajukan Kartu Sekarang</span>
                </a>
            </div>
            @endforelse

            <!-- Card Requests Section -->
            @if($requests->count() > 0)
            <div class="bg-white rounded-[2.2rem] border border-slate-100 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Daftar Pengajuan Kartu</h3>
                <div class="space-y-2.5">
                    @foreach($requests as $req)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100/50 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg text-brand-600">contact_mail</span>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">{{ $req->parent_name }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Tanggal: {{ $req->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border {{
                            $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-100/80' :
                            ($req->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-100/80' :
                            'bg-amber-50 text-amber-700 border-amber-100/80')
                        }}">
                            {{ $req->status === 'approved' ? 'Disetujui' : ($req->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Minimal Transaction Preview Section -->
            <div class="bg-white rounded-[2.2rem] border border-slate-100 p-5 sm:p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between gap-4 px-1">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Aktivitas Terakhir</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5">Riwayat transaksi singkat kartu Anda.</p>
                    </div>
                    <a href="{{ route('siswa.finance.history') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-brand-600 rounded-xl text-[10px] font-extrabold uppercase tracking-wider transition-all">
                        <span>Semua</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <!-- Flat Minimalist Divider List Style -->
                <div class="divide-y divide-slate-100/80">
                    @forelse($transactions as $tx)
                    @php($isCredit = in_array($tx->type, ['topup', 'transfer_receive']))
                    <a href="{{ route('siswa.finance.transactions.show', $tx) }}" class="flex items-center justify-between py-4 group first:pt-0 last:pb-0">
                        <div class="flex items-center gap-3.5 min-w-0 pr-2">
                            <!-- Small Minimal Status Indicator Circle -->
                            <div class="w-2 h-2 rounded-full shrink-0 {{ $isCredit ? 'bg-emerald-500' : ($tx->type === 'withdraw' ? 'bg-amber-500' : 'bg-slate-300') }}"></div>
                            
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 group-hover:text-brand-600 transition-colors truncate">
                                    @if($tx->type === 'topup')
                                        Top Up Saldo
                                    @elseif($tx->type === 'payment')
                                        Pembelian / Belanja
                                    @elseif($tx->type === 'transfer')
                                        Transfer Keluar
                                    @elseif($tx->type === 'transfer_receive')
                                        Transfer Masuk
                                    @elseif($tx->type === 'withdraw')
                                        Tarik Tunai
                                    @else
                                        {{ ucfirst($tx->type) }}
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $tx->created_at->format('d M, H:i') }} • {{ $tx->reference_number ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-xs font-black tracking-tight {{ $isCredit ? 'text-emerald-600' : ($tx->type === 'withdraw' ? 'text-amber-600' : 'text-slate-800') }}">
                                {{ $isCredit ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </p>
                            @if($tx->description)
                            <p class="text-[9px] text-slate-400 mt-0.5 max-w-[120px] truncate" title="{{ $tx->description }}">{{ $tx->description }}</p>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2.5">
                            <span class="material-symbols-outlined text-xl">receipt_long</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-bold">Belum ada transaksi terekam.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Sidebar Actions Menu (Hidden on mobile/tablet) -->
        <div class="hidden lg:block space-y-4">
            <div class="bg-white rounded-[2.2rem] border border-slate-100 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Menu Keuangan</h3>
                <div class="space-y-2">
                    <a href="{{ route('siswa.finance.transfer') }}" class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-brand-50/50 rounded-2xl transition-all group border border-slate-50/20 hover:border-brand-100/50">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-[#F6863B] transition-colors">currency_exchange</span>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-[#F6863B] transition-colors">Transfer Saldo</span>
                    </a>
                    <a href="{{ route('siswa.finance.history') }}" class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-brand-50/50 rounded-2xl transition-all group border border-slate-50/20 hover:border-brand-100/50">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-[#F6863B] transition-colors">history</span>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-[#F6863B] transition-colors">Riwayat & Struk</span>
                    </a>
                    <a href="{{ route('siswa.finance.request-card') }}" class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-brand-50/50 rounded-2xl transition-all group border border-slate-50/20 hover:border-brand-100/50">
                        <span class="material-symbols-outlined text-xl text-slate-400 group-hover:text-[#F6863B] transition-colors">add_card</span>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-[#F6863B] transition-colors">Ajukan Kartu Baru</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===================== STYLE KARTU 3D ===================== -->
<style>
    /* Panggung: perspektif + skala tipografi mengikuti lebar kartu (cqw) */
    .fc-stage { padding: .5rem .25rem 1.25rem; }
    .fc-scene {
        position: relative;
        width: 100%;
        max-width: 27rem;
        margin-inline: auto;
        aspect-ratio: 1.586 / 1;
        perspective: 1100px;
        container-type: inline-size;
        cursor: pointer;
        touch-action: pan-y;
        -webkit-user-select: none; user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .fc-scene:focus { outline: none; }
    .fc-scene:focus-visible { outline: 2px solid #EB4325; outline-offset: 8px; border-radius: 5.2cqw; }

    /* Rantai 3D: float > tilt > flip > face */
    .fc-float, .fc-tilt, .fc-flip { position: absolute; inset: 0; transform-style: preserve-3d; }
    .fc-float { animation: fcFloat 6s ease-in-out infinite; }
    @keyframes fcFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .fc-scene.is-active .fc-float { animation-play-state: paused; }

    .fc-tilt {
        transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
        transition: transform .7s cubic-bezier(.16, 1, .3, 1);
    }
    .fc-scene.is-active .fc-tilt { transition: transform .1s linear; }

    .fc-flip { transition: transform .85s cubic-bezier(.2, .8, .2, 1); }
    .fc-scene.is-flipped .fc-flip { transform: rotateY(180deg); }

    /* Bayangan lantai yang bergeser mengikuti kemiringan */
    .fc-shadow {
        position: absolute; left: 9%; right: 9%; bottom: -6%; height: 14%;
        background: radial-gradient(ellipse at center, rgba(138, 30, 30, .55), transparent 70%);
        filter: blur(10px);
        transform: translateX(var(--sx, 0px));
        transition: transform .7s cubic-bezier(.16, 1, .3, 1);
        pointer-events: none;
    }

    /* Sisi kartu */
    .fc-face {
        position: absolute; inset: 0;
        border-radius: 5.2cqw;
        color: #fff;
        transform-style: preserve-3d;
        -webkit-backface-visibility: hidden; backface-visibility: hidden;
    }
    .fc-back { transform: rotateY(180deg); }
    .fc-face * { -webkit-backface-visibility: hidden; backface-visibility: hidden; }

    /* Latar (dipotong membulat di sini agar lapisan 3D di atasnya tidak ikut datar) */
    .fc-bg {
        position: absolute; inset: 0; border-radius: inherit; overflow: hidden;
        background:
            radial-gradient(120% 90% at 0% 0%, rgba(255, 220, 170, .55), transparent 55%),
            repeating-radial-gradient(circle at 108% 118%,
                transparent 0 22%, rgba(255, 255, 255, .07) 22.4% 23.2%,
                transparent 23.6% 34%, rgba(255, 255, 255, .06) 34.4% 35.2%),
            linear-gradient(135deg, #F6863B 0%, #EB4325 50%, #8A1E1E 100%);
        box-shadow: 0 1.2cqw 3cqw -1cqw rgba(138, 30, 30, .45);
    }
    .fc-bg::after {
        content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none;
        box-shadow:
            inset 0 0 0 1px rgba(255, 255, 255, .22),
            inset 0 1px 0 rgba(255, 255, 255, .4),
            inset 0 -6cqw 10cqw rgba(0, 0, 0, .18);
    }
    .fc-bg.is-inactive { filter: saturate(.35) brightness(.92); }
    .fc-bg-back {
        background:
            radial-gradient(90% 70% at 100% 0%, rgba(246, 134, 59, .28), transparent 60%),
            linear-gradient(145deg, #2a2f3a 0%, #151922 60%, #0c0f15 100%);
    }

    /* Kilau mengikuti pointer */
    .fc-glare {
        position: absolute; inset: 0; pointer-events: none;
        background: radial-gradient(circle at var(--gx, 25%) var(--gy, 0%), rgba(255, 255, 255, .34), transparent 55%);
        opacity: var(--go, .55);
        transition: opacity .4s;
    }
    .fc-sheen {
        position: absolute; inset: -25%; pointer-events: none;
        background: linear-gradient(115deg, transparent 38%, rgba(255, 255, 255, .20) 49%, transparent 60%);
        transform: translateX(var(--sheen, 0%));
        transition: transform .7s cubic-bezier(.16, 1, .3, 1);
    }
    .fc-scene.is-active .fc-sheen { transition: transform .1s linear; }

    /* Konten dengan kedalaman (parallax) */
    .fc-content {
        position: absolute; inset: 0;
        padding: 6cqw 6.5cqw 5.5cqw;
        display: flex; flex-direction: column; justify-content: space-between;
        transform-style: preserve-3d;
    }
    .fc-z1 { transform: translateZ(20px); }
    .fc-z2 { transform: translateZ(38px); }
    .fc-row { display: flex; align-items: center; justify-content: space-between; gap: 3cqw; }
    .fc-foot { align-items: flex-end; }

    .fc-chipwrap { display: flex; align-items: center; gap: 3cqw; }
    .fc-chip { width: 11cqw; height: auto; filter: drop-shadow(0 .6cqw .8cqw rgba(0, 0, 0, .3)); }
    .fc-nfc { width: 5.8cqw; height: 5.8cqw; opacity: .8; }

    .fc-status {
        display: inline-flex; align-items: center; gap: 1.6cqw;
        padding: 1.3cqw 3cqw; border-radius: 999px;
        background: rgba(255, 255, 255, .16); border: 1px solid rgba(255, 255, 255, .2);
        font-size: max(9px, 2.6cqw); font-weight: 700; letter-spacing: .02em;
        -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
    }
    .fc-dot { width: 1.8cqw; height: 1.8cqw; min-width: 6px; min-height: 6px; border-radius: 50%; background: #94a3b8; }
    .fc-dot.is-ok { background: #34d399; box-shadow: 0 0 0 .6cqw rgba(52, 211, 153, .25); }
    .fc-dot.is-warn { background: #fbbf24; }
    .fc-dot.is-off { background: #94a3b8; }

    .fc-label {
        display: block; font-size: max(9px, 2.4cqw); font-weight: 600;
        color: rgba(255, 255, 255, .72); letter-spacing: .01em;
    }
    .fc-amount {
        margin: .6cqw 0 0; font-weight: 800; letter-spacing: -.02em; line-height: 1.05;
        font-size: 1.5rem; font-size: clamp(1.4rem, 9cqw, 2.6rem);
        text-shadow: 0 .5cqw 1.4cqw rgba(0, 0, 0, .28);
        font-variant-numeric: tabular-nums;
    }
    .fc-cur { font-size: .5em; font-weight: 700; opacity: .85; margin-right: .15em; letter-spacing: 0; }
    .fc-number {
        margin: .6cqw 0 0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: max(11px, 4cqw); font-weight: 700; letter-spacing: .12em;
        overflow-wrap: anywhere;
        text-shadow: 0 1px 0 rgba(255, 255, 255, .28), 0 -1px 0 rgba(0, 0, 0, .3);
    }
    .fc-name {
        margin: .4cqw 0 0; font-size: max(11px, 3.4cqw); font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .fc-brand { font-size: max(10px, 3cqw); font-weight: 800; letter-spacing: -.01em; opacity: .9; white-space: nowrap; }

    /* Sisi belakang */
    .fc-content-back { padding: 0 0 5.5cqw; }
    .fc-stripe { margin-top: 7cqw; height: 15cqw; background: linear-gradient(#0a0a0a, #1b1b1b); box-shadow: 0 1cqw 2cqw rgba(0, 0, 0, .4); }
    .fc-backbody { padding: 0 6.5cqw; }
    .fc-sign {
        display: flex; align-items: center; height: 10cqw; padding: 0 3cqw; border-radius: 1.6cqw;
        background: repeating-linear-gradient(45deg, #f1f5f9 0 2cqw, #e2e8f0 2cqw 4cqw);
        color: #0f172a;
    }
    .fc-sign-name { font-family: 'Brush Script MT', 'Segoe Script', cursive; font-size: max(13px, 4.6cqw); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .fc-number-back { margin-top: 3cqw; }
    .fc-note { margin: 2.4cqw 0 0; font-size: max(9px, 2.5cqw); line-height: 1.45; color: rgba(255, 255, 255, .6); max-width: 34ch; }
    .fc-content-back .fc-foot { padding: 0 6.5cqw; }
    .fc-hint { font-size: max(9px, 2.4cqw); color: rgba(255, 255, 255, .55); }

    /* Tombol aksi di bawah kartu */
    .fc-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .6rem .5rem; border-radius: .875rem;
        background: #fff; border: 1px solid #e2e8f0;
        font-size: 11px; font-weight: 700; color: #475569;
        transition: background-color .15s, transform .12s, color .15s, border-color .15s;
        white-space: nowrap;
    }
    .fc-btn:hover { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
    .fc-btn:active { transform: scale(.96); }
    .fc-btn:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    @media (prefers-reduced-motion: reduce) {
        .fc-float { animation: none; }
        .fc-tilt, .fc-sheen, .fc-shadow { transition-duration: .01s; }
        .fc-flip { transition-duration: .2s; }
    }
</style>

<!-- ===================== SCRIPT KARTU 3D ===================== -->
<script>
(function () {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var MAX_X = 12, MAX_Y = 16;
    var HIDE_KEY = 'siswa_finance_hide_balance';

    /* ---------- Sembunyikan / tampilkan saldo ---------- */
    var balanceHidden = false;
    try { balanceHidden = localStorage.getItem(HIDE_KEY) === '1'; } catch (e) {}

    function applyBalanceVisibility() {
        document.querySelectorAll('.js-balance').forEach(function (el) {
            el.textContent = balanceHidden ? '••••••' : el.getAttribute('data-full');
        });
        document.querySelectorAll('[data-act="hide"]').forEach(function (btn) {
            btn.setAttribute('aria-pressed', String(balanceHidden));
            btn.querySelector('.fc-btn-icon').textContent = balanceHidden ? 'visibility' : 'visibility_off';
            btn.querySelector('.fc-btn-label').textContent = balanceHidden ? 'Tampilkan' : 'Sembunyikan';
        });
    }

    /* ---------- Balik kartu ---------- */
    function setFlipped(scene, flipped) {
        scene.classList.toggle('is-flipped', flipped);
        scene.setAttribute('aria-pressed', String(flipped));
        var front = scene.querySelector('[data-face="front"]');
        var back = scene.querySelector('[data-face="back"]');
        if (front) front.setAttribute('aria-hidden', String(flipped));
        if (back) back.setAttribute('aria-hidden', String(!flipped));
    }
    function toggleFlip(scene) { setFlipped(scene, !scene.classList.contains('is-flipped')); }

    /* ---------- Salin nomor kartu ---------- */
    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text; ta.setAttribute('readonly', '');
            ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy') ? resolve() : reject(); } catch (err) { reject(err); }
            document.body.removeChild(ta);
        });
    }

    /* ---------- Tilt 3D mengikuti pointer ---------- */
    function initScene(scene) {
        var raf = null, last = null;
        var downX = 0, downY = 0, moved = false;

        function apply() {
            raf = null;
            if (!last) return;
            var r = scene.getBoundingClientRect();
            var px = Math.min(Math.max((last.x - r.left) / r.width, 0), 1);
            var py = Math.min(Math.max((last.y - r.top) / r.height, 0), 1);
            var ry = (px - .5) * 2 * MAX_Y;
            var rx = -(py - .5) * 2 * MAX_X;
            scene.style.setProperty('--rx', rx.toFixed(2) + 'deg');
            scene.style.setProperty('--ry', ry.toFixed(2) + 'deg');
            scene.style.setProperty('--gx', (px * 100).toFixed(1) + '%');
            scene.style.setProperty('--gy', (py * 100).toFixed(1) + '%');
            scene.style.setProperty('--go', '.9');
            scene.style.setProperty('--sheen', ((px - .5) * 70).toFixed(1) + '%');
            scene.style.setProperty('--sx', (-ry * 1.4).toFixed(1) + 'px');
        }

        function reset() {
            scene.classList.remove('is-active');
            last = null;
            ['--rx', '--ry', '--gx', '--gy', '--go', '--sheen', '--sx'].forEach(function (p) {
                scene.style.removeProperty(p);
            });
        }

        if (!reduceMotion) {
            scene.addEventListener('pointermove', function (e) {
                scene.classList.add('is-active');
                last = { x: e.clientX, y: e.clientY };
                if (Math.abs(e.clientX - downX) > 8 || Math.abs(e.clientY - downY) > 8) moved = true;
                if (!raf) raf = requestAnimationFrame(apply);
            });
            scene.addEventListener('pointerleave', reset);
            scene.addEventListener('pointercancel', reset);
            scene.addEventListener('pointerup', function (e) { if (e.pointerType !== 'mouse') reset(); });
        }

        scene.addEventListener('pointerdown', function (e) { downX = e.clientX; downY = e.clientY; moved = false; });

        scene.addEventListener('click', function () {
            if (moved) { moved = false; return; }
            toggleFlip(scene);
        });

        scene.addEventListener('keydown', function (e) {
            if (e.target !== scene) return;
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleFlip(scene); }
        });
    }

    document.querySelectorAll('[data-card3d]').forEach(initScene);

    /* ---------- Tombol aksi ---------- */
    document.querySelectorAll('[data-card-wrap]').forEach(function (wrap) {
        wrap.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-act]');
            if (!btn) return;
            var act = btn.getAttribute('data-act');
            var scene = wrap.querySelector('[data-card3d]');

            if (act === 'flip') {
                toggleFlip(scene);
            } else if (act === 'hide') {
                balanceHidden = !balanceHidden;
                try { localStorage.setItem(HIDE_KEY, balanceHidden ? '1' : '0'); } catch (err) {}
                applyBalanceVisibility();
            } else if (act === 'copy') {
                var label = btn.querySelector('.fc-btn-copy-label');
                var icon = btn.querySelector('.fc-btn-copy-icon');
                copyText(wrap.getAttribute('data-number') || '').then(function () {
                    label.textContent = 'Tersalin';
                    icon.textContent = 'check';
                }).catch(function () {
                    label.textContent = 'Gagal menyalin';
                }).then(function () {
                    setTimeout(function () {
                        label.textContent = 'Salin nomor';
                        icon.textContent = 'content_copy';
                    }, 1600);
                });
            }
        });
    });

    applyBalanceVisibility();
})();
</script>
@endsection