
@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Detail Aduan',
        'eyebrow' => 'Helpdesk',
        'subtitle' => 'Kelola aduan dari ' . ($complaint->complainant->nama_lengkap ?? $complaint->complainant->name ?? 'Tidak diketahui'),
        'icon' => 'report',
    ])

    <div class="flex items-center justify-center gap-4">
        <a href="{{ route('admin.complaints.index') }}" class="p-3 bg-slate-100 text-slate-700 rounded-2xl hover:bg-slate-200 transition-all flex items-center gap-2 font-bold text-sm">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
        <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider {{
            $complaint->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
            ($complaint->status === 'in_progress' ? 'bg-orange-100 text-orange-700' :
            ($complaint->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'))
        }}">
            {{ $complaint->status_text }}
        </span>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Complaint Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 p-8">
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-6">{{ $complaint->title }}</h3>
                
                <div class="space-y-4 mb-8">
                    <p class="text-sm text-slate-700 font-medium leading-relaxed">{{ $complaint->description }}</p>
                    
                    @if($complaint->proof_photo)
                    <div class="mt-6">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Bukti Foto</p>
                        <img src="{{ asset('storage/' . $complaint->proof_photo) }}" alt="Bukti Aduan" class="rounded-2xl border border-slate-100 max-w-md">
                    </div>
                    @endif
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Informasi Aduan</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500 font-medium">Pengirim</p>
                            <p class="text-slate-900 font-bold">{{ $complaint->complainant->nama_lengkap ?? $complaint->complainant->name ?? 'Tidak diketahui' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium">Tanggal Dikirim</p>
                            <p class="text-slate-900 font-bold">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        @if($complaint->handled_by)
                        <div>
                            <p class="text-slate-500 font-medium">Ditangani Oleh</p>
                            <p class="text-slate-900 font-bold">{{ $complaint->handledBy->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium">Tanggal Ditangani</p>
                            <p class="text-slate-900 font-bold">{{ $complaint->handled_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($complaint->response)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-[32px] p-8">
                <h4 class="text-lg font-black text-emerald-800 dark:text-emerald-200 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    Tanggapan
                </h4>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">{{ $complaint->response }}</p>
            </div>
            @endif
        </div>

        <!-- Action Sidebar -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 p-6">
                <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-4">Ubah Status & Tanggapan</h4>
                <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Status</label>
                        <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                            <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ $complaint->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tanggapan (Opsional)</label>
                        <textarea name="response" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Berikan tanggapan kepada pengirim aduan...">{{ $complaint->response }}</textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-orange-600 text-white rounded-2xl font-black shadow-lg shadow-orange-100 hover:bg-orange-700 transition-all">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
