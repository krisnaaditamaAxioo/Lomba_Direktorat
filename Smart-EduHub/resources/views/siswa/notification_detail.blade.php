@extends('layouts.siswa')

@section('title', 'Detail Notifikasi')
@section('page_title', 'Detail Notifikasi')

@section('content')
@php
    $type = $notification->type;
    if ($type === 'finance') {
        $icon = 'account_balance_wallet';
        $label = 'Keuangan';
    } elseif ($type === 'point') {
        $icon = 'grade';
        $label = 'Poin';
    } else {
        $icon = 'campaign';
        $label = 'Pengumuman';
    }

    $attachment = $notification->data['attachment'] ?? null;
    $attachmentUrl = $attachment
        ? (\Illuminate\Support\Str::startsWith($attachment, ['http://', 'https://'])
            ? $attachment
            : \Illuminate\Support\Facades\Storage::url($attachment))
        : null;
    $attachmentName = $attachment ? basename(parse_url($attachment, PHP_URL_PATH) ?: $attachment) : null;
@endphp

<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6 pb-28 px-2 sm:px-0">

    <!-- ===== Header ===== -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-5 pb-8 px-5 sm:px-8 text-white overflow-hidden shadow-xl shadow-orange-900/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-6 lg:pb-8">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>

        <div class="relative z-10">
            <!-- Tombol kembali -->
            <a href="{{ route('siswa.notifications') }}" class="inline-flex items-center gap-1.5 pl-2.5 pr-4 py-2 rounded-full bg-white/15 border border-white/20 backdrop-blur-md text-xs font-bold hover:bg-white/25 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Kembali
            </a>

            <div class="flex items-start gap-3.5 sm:gap-4 mt-5">
                <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/20 shadow-lg">
                    <span class="material-symbols-outlined text-2xl sm:text-3xl">{{ $icon }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/20 text-[10px] font-extrabold uppercase tracking-[0.15em]">{{ $label }}</span>
                        @if($notification->read)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/10 text-[10px] font-bold uppercase tracking-wider text-white/80">
                                <span class="material-symbols-outlined text-[13px]">done_all</span> Terbaca
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white text-[#EB4325] text-[10px] font-extrabold uppercase tracking-wider">Baru</span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight leading-snug break-words">{{ $notification->title }}</h1>
                    <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-white/80 font-medium mt-2.5">
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">calendar_today</span>
                            {{ $notification->created_at->format('d M Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            {{ $notification->created_at->format('H:i') }}
                        </span>
                        <span class="text-white/60">· {{ $notification->created_at->diffForHumans() }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Isi Pesan ===== -->
    <article class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-sm p-5 sm:p-8">
        <div class="flex items-center gap-2 mb-4 sm:mb-5">
            <span class="w-1 h-5 rounded-full bg-gradient-to-b from-[#F6863B] to-[#EB4325]"></span>
            <h2 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Isi Pesan</h2>
        </div>
        <div class="text-sm sm:text-base text-slate-700 leading-relaxed sm:leading-loose whitespace-pre-line break-words">{{ $notification->message }}</div>
    </article>

    <!-- ===== Lampiran ===== -->
    @if($attachmentUrl)
        <div class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1 h-5 rounded-full bg-gradient-to-b from-[#F6863B] to-[#EB4325]"></span>
                <h2 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Lampiran</h2>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-orange-50/80 to-slate-50 border border-orange-100/70">
                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                    <div class="w-12 h-12 shrink-0 rounded-2xl bg-red-100 text-red-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">picture_as_pdf</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $attachmentName ?: 'PDF notifikasi' }}</p>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Dokumen PDF</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:flex gap-2 sm:shrink-0">
                    <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#F6863B] to-[#EB4325] text-white text-xs sm:text-sm font-bold shadow-lg shadow-orange-500/25 hover:-translate-y-0.5 hover:shadow-xl active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-lg">open_in_new</span>
                        Buka
                    </a>
                    <a href="{{ $attachmentUrl }}" download
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs sm:text-sm font-bold hover:border-orange-300 hover:text-[#EB4325] active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Unduh
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- ===== Aksi Bawah ===== -->
    <div class="flex">
        <a href="{{ route('siswa.notifications') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 hover:-translate-y-0.5 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-lg">notifications</span>
            Semua Notifikasi
        </a>
    </div>
</div>
@endsection