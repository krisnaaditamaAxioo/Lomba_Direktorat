@extends('layouts.app')

@section('title', 'Disiplin & Penghargaan')

@section('styles')
<style>
    .card-detail {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
        opacity: 0;
    }
    .card-detail.expanded {
        max-height: 300px;
        opacity: 1;
        transition: max-height 0.4s ease-in, opacity 0.3s ease-in;
    }
</style>
@endsection

@section('content')
<!-- Gradient Header Block (Fintech Style) - Full Width on Mobile, Rounded Card on Desktop -->
<div id="pageHero" class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-4 pt-8 pb-14 px-6 text-white overflow-hidden shadow-2xl shadow-orange-950/10 rounded-b-[2.5rem] lg:mx-0 lg:rounded-[2.5rem] lg:pt-6 lg:pb-8">
    <!-- Abstract Circular Glow Overlays -->
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute -bottom-8 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

    <div class="relative z-10 space-y-5">
        <!-- Branding & Stat Pill -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <span class="text-base font-black tracking-tight">SEH</span>
            </div>
            <!-- Total Merit Pill -->
            <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">military_tech</span>
                <span>{{ number_format($stats['total_merit']) }} Poin</span>
            </div>
        </div>

        <!-- Title Center -->
        <div class="text-center py-1 space-y-1">
            <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">Sistem Poin</p>
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight">Poin & Disiplin</h2>
            <p class="text-xs text-orange-100/70 font-medium">Pantau poin perilaku siswa dan penghargaan akademik.</p>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex gap-2 sm:gap-3 mt-5 mb-6 md:mb-8">
    <a href="{{ route('points.leaderboard') }}" class="flex-1 sm:flex-none justify-center bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-50 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">leaderboard</span>
        <span class="hidden xs:inline">Peringkat</span>
    </a>
    <a href="{{ route('points.create') }}" class="flex-1 sm:flex-none justify-center bg-primary text-white px-4 sm:px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-orange-100 hover:bg-orange-700 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Tambah Catatan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <!-- Ringkasan Poin -->
    <div class="lg:col-span-2 space-y-6 lg:space-y-8">
        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
            <div class="bg-orange-600 p-5 md:p-6 rounded-2xl text-white">
                <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-widest opacity-80 mb-2">Total Poin Penghargaan</p>
                <h3 class="text-2xl md:text-3xl font-black">{{ number_format($stats['total_merit']) }}</h3>
                <div class="mt-3 md:mt-4 flex items-center gap-2 text-[10px] md:text-[11px] font-bold bg-white/20 w-fit px-2 py-1 rounded-lg">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    Sistem poin aktif
                </div>
            </div>
            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100">
                <p class="text-[10px] md:text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Pelanggaran</p>
                <h3 class="text-2xl md:text-3xl font-black text-slate-900">{{ number_format($stats['total_demerit']) }}</h3>
                <div class="mt-3 md:mt-4 flex items-center gap-2 text-[10px] md:text-[11px] font-bold text-rose-600 bg-rose-50 w-fit px-2 py-1 rounded-lg">
                    <span class="material-symbols-outlined text-sm">trending_down</span>
                    Pemantauan perilaku
                </div>
            </div>
            <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100">
                <p class="text-[10px] md:text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sanksi Aktif</p>
                <h3 class="text-2xl md:text-3xl font-black text-slate-900">{{ number_format($stats['active_citations']) }}</h3>
                <div class="mt-3 md:mt-4 flex items-center gap-2 text-[10px] md:text-[11px] font-bold text-amber-600 bg-amber-50 w-fit px-2 py-1 rounded-lg">
                    <span class="material-symbols-outlined text-sm">info</span>
                    30 hari terakhir
                </div>
            </div>
        </div>

        <!-- ==================== MOBILE: TAMPILAN KARTU ==================== -->
        <div class="sm:hidden space-y-3">
            <div class="flex items-center justify-between px-1">
                <h4 class="font-bold text-slate-900">Log Poin Terbaru</h4>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $points->count() }} catatan</span>
            </div>

            @foreach($points as $point)
            @php
                $isPositive = in_array($point->type, ['merit', 'achievement']);
                $totalViolation = $point->student->points()->where('type', 'violation')->sum('score');
                $totalAchievement = $point->student->points()->where('type', 'achievement')->sum('score');
                $currentPoints = max(0, $totalViolation - $totalAchievement);
                $parentPhone = $point->student->parent_phone;
                $waUrl = "";
                if ($currentPoints >= 70 && $parentPhone) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone);
                    if (strpos($cleanPhone, '0') === 0) $cleanPhone = '62' . substr($cleanPhone, 1);
                    $message = "SURAT PANGGILAN ORANG TUA\n\nKepada Yth. Orang Tua/Wali dari " . $point->student->nama_lengkap . ",\n\nKami menginformasikan bahwa poin pelanggaran putra/putri Bapak/Ibu telah mencapai " . $currentPoints . " poin. Sehubungan dengan hal tersebut, kami mengharap kehadiran Bapak/Ibu di sekolah untuk koordinasi pembinaan.\n\nTerima kasih.\nSmart Axioo Class Program";
                    $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
                }
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                <!-- Header Kartu -->
                <button onclick="togglePointCard('pc-{{ $point->id }}')" class="w-full p-4 flex items-center gap-3 active:bg-slate-50 transition-colors text-left">
                    <div class="w-10 h-10 rounded-xl {{ $isPositive ? 'bg-emerald-50' : 'bg-rose-50' }} flex items-center justify-center font-bold text-sm {{ $isPositive ? 'text-emerald-600' : 'text-rose-600' }} flex-shrink-0">
                        {{ $isPositive ? '+' : '-' }}{{ $point->score }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $point->student->nama_lengkap }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="px-2 py-0.5 {{ $isPositive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-md text-[9px] font-bold uppercase">{{ $point->type }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($point->date)->format('d M Y') }}</span>
                        </div>
                    </div>
                    @if($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" onclick="event.stopPropagation()" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl transition-all flex-shrink-0 active:scale-90" title="Kirim Surat Panggilan via WA">
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                        </a>
                    @endif
                    <span class="material-symbols-outlined text-slate-300 flex-shrink-0 transition-transform duration-300" id="icon-pc-{{ $point->id }}">expand_more</span>
                </button>

                <!-- Pratinjau Alasan -->
                <div class="px-4 pb-3 pl-[68px]">
                    <p class="text-xs text-slate-500 truncate">{{ $point->reason }}</p>
                </div>

                <!-- Detail yang Dapat Diperluas -->
                <div id="pc-{{ $point->id }}" class="card-detail">
                    <div class="border-t border-slate-100 bg-slate-50/50 p-4 space-y-3">
                        <div class="flex items-start gap-3">
                            @if($point->proof_photo)
                                <a href="{{ asset('storage/' . $point->proof_photo) }}" target="_blank" class="block w-14 h-14 rounded-xl overflow-hidden border border-slate-200 hover:border-primary transition-colors flex-shrink-0">
                                    <img src="{{ asset('storage/' . $point->proof_photo) }}" alt="Bukti" class="w-full h-full object-cover">
                                </a>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Alasan Lengkap</p>
                                <p class="text-sm text-slate-700 leading-relaxed">{{ $point->reason }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('points.edit', $point) }}" class="flex items-center gap-1.5 px-3 py-2 text-slate-500 hover:text-primary hover:bg-orange-50 rounded-lg transition-all text-xs font-bold">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                Ubah
                            </a>
                            <form action="{{ route('points.destroy', $point) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-1.5 px-3 py-2 text-slate-500 hover:text-error hover:bg-rose-50 rounded-lg transition-all text-xs font-bold">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($points->count() === 0)
                <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">inbox</span>
                    <p class="text-sm font-bold text-slate-500 mt-3">Belum ada catatan</p>
                </div>
            @endif
        </div>

        <!-- ==================== DESKTOP: TAMPILAN TABEL ==================== -->
        <div class="hidden sm:block bg-white rounded-2xl border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h4 class="font-bold text-slate-900">Log Poin Terbaru</h4>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $points->count() }} catatan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Siswa</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Tipe</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Poin</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Alasan</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Bukti</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($points as $point)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($point->student->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700">{{ $point->student->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 {{ in_array($point->type, ['merit', 'achievement']) ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-lg text-[10px] font-bold uppercase">{{ $point->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @php
                                        $totalViolation = $point->student->points()->where('type', 'violation')->sum('score');
                                        $totalAchievement = $point->student->points()->where('type', 'achievement')->sum('score');
                                        $currentPoints = max(0, $totalViolation - $totalAchievement);
                                        $parentPhone = $point->student->parent_phone;
                                        $waUrl = "";
                                        if ($currentPoints >= 70 && $parentPhone) {
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone);
                                            if (strpos($cleanPhone, '0') === 0) $cleanPhone = '62' . substr($cleanPhone, 1);
                                            
                                            $message = "SURAT PANGGILAN ORANG TUA\n\nKepada Yth. Orang Tua/Wali dari " . $point->student->nama_lengkap . ",\n\nKami menginformasikan bahwa poin pelanggaran putra/putri Bapak/Ibu telah mencapai " . $currentPoints . " poin. Sehubungan dengan hal tersebut, kami mengharap kehadiran Bapak/Ibu di sekolah untuk koordinasi pembinaan.\n\nTerima kasih.\nSmart Axioo Class Program";
                                            $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
                                        }
                                    @endphp

                                    @if($waUrl)
                                    <a href="{{ $waUrl }}" target="_blank" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-all flex items-center gap-1" title="Kirim Surat Panggilan via WA">
                                        <span class="material-symbols-outlined text-sm">mail</span>
                                        <span class="text-[10px] font-bold">Surat</span>
                                    </a>
                                    @endif

                                    <span class="text-sm font-bold {{ in_array($point->type, ['merit', 'achievement']) ? 'text-emerald-600' : 'text-rose-600' }}">{{ in_array($point->type, ['merit', 'achievement']) ? '+' : '-' }}{{ $point->score }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 truncate max-w-[200px]">{{ $point->reason }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($point->proof_photo)
                                    <a href="{{ asset('storage/' . $point->proof_photo) }}" target="_blank" class="block w-10 h-10 rounded-lg overflow-hidden border border-slate-100 hover:border-primary transition-colors">
                                        <img src="{{ asset('storage/' . $point->proof_photo) }}" alt="Bukti" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-300 italic">Tidak ada bukti</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400">{{ \Carbon\Carbon::parse($point->date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('points.edit', $point) }}" class="p-2 text-slate-400 hover:text-primary rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="{{ route('points.destroy', $point) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-error rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Kanan -->
    <div class="space-y-6 lg:space-y-8">
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100">
            <h4 class="font-bold text-slate-900 mb-5">Distribusi Poin</h4>
            <div class="space-y-5">
                @foreach($distribution as $label => $data)
                <div>
                    <div class="flex justify-between text-xs font-bold mb-2">
                        <span class="text-slate-500">{{ $label }}</span>
                        <span class="text-{{ $data['color'] }}-600">{{ $data['percentage'] }}%</span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $data['color'] }}-600 transition-all duration-500" style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-900 p-5 md:p-6 rounded-2xl text-white">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px] text-amber-400">lightbulb</span>
                </div>
                <h4 class="font-bold">Tips Cepat</h4>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed mb-5">Poin merit yang konsisten tinggi akan otomatis memicu pertimbangan "Siswa Terbaik Bulan Ini" di dasbor utama.</p>
            <button class="w-full py-3 bg-white/10 hover:bg-white/20 active:bg-white/25 text-white rounded-xl text-sm font-bold transition-colors">Lihat Kriteria</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePointCard(id) {
    const detail = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    detail.classList.toggle('expanded');
    icon.style.transform = detail.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
}

// Mobile: sembunyikan navbar atas (Search) & tempelkan header ke ujung atas layar
(function () {
    const hero = document.getElementById('pageHero');
    if (!hero) return;
    const mq = window.matchMedia('(max-width: 767px)');

    const searchBox = document.querySelector('input[type="search"], input[placeholder*="earch" i]');
    let topbar = null;
    if (searchBox) {
        let el = searchBox;
        while (el && el !== document.body) {
            if (el.contains(hero)) break;
            const pos = getComputedStyle(el).position;
            if (el.tagName === 'HEADER' || el.tagName === 'NAV' || pos === 'sticky' || pos === 'fixed') topbar = el;
            el = el.parentElement;
        }
    }

    function snapHero() {
        if (topbar) topbar.style.display = mq.matches ? 'none' : '';
        hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
        if (!mq.matches) return;
        const r = hero.getBoundingClientRect();
        const cs = getComputedStyle(hero);
        const mt = parseFloat(cs.marginTop) || 0;
        const ml = parseFloat(cs.marginLeft) || 0;
        const mr = parseFloat(cs.marginRight) || 0;
        const top = r.top + window.scrollY;
        const right = document.documentElement.clientWidth - r.right;
        if (top > 0) hero.style.marginTop = (mt - top) + 'px';
        if (r.left !== 0) hero.style.marginLeft = (ml - r.left) + 'px';
        if (right !== 0) hero.style.marginRight = (mr - right) + 'px';
    }
    const run = () => requestAnimationFrame(snapHero);
    run();
    window.addEventListener('load', run);
    window.addEventListener('resize', run);
    if (mq.addEventListener) mq.addEventListener('change', run);
})();
</script>
@endsection