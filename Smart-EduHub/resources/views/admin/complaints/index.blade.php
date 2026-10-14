@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Aduan & Laporan',
        'eyebrow' => 'Helpdesk',
        'subtitle' => 'Kelola semua aduan dan laporan dari siswa dan guru.',
        'icon' => 'report',
    ])

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-6">
        <form action="{{ route('admin.complaints.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau deskripsi..." class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
            </div>
            <div class="w-full md:w-48">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Status</label>
                <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="w-full md:w-auto px-6 py-3 bg-orange-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-100 hover:bg-orange-700 transition-all">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Complaints List -->
    <div class="space-y-4">
        @forelse($complaints as $complaint)
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">report</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $complaint->title }}</h3>
                            <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ 
                                $complaint->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                ($complaint->status === 'in_progress' ? 'bg-orange-100 text-orange-700' : 
                                ($complaint->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'))
                            }}">
                                {{ $complaint->status_text }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">
                            Dari: {{ $complaint->complainant->nama_lengkap ?? $complaint->complainant->name ?? 'Tidak diketahui' }} • 
                            {{ $complaint->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="px-4 py-2 bg-orange-50 text-orange-600 rounded-xl text-sm font-bold hover:bg-orange-100 transition-all">
                        Lihat Detail
                    </a>
                    <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Hapus aduan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-sm font-bold hover:bg-rose-100 transition-all">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">inbox</span>
            </div>
            <h3 class="text-lg font-black text-slate-700 mb-2">Belum ada aduan</h3>
            <p class="text-sm text-slate-400 font-medium">Tidak ada aduan yang masuk saat ini.</p>
        </div>
        @endforelse

        {{ $complaints->links() }}
    </div>
</div>
@endsection
