@extends('layouts.parent')

@section('title', 'Layanan Pengaduan')
@section('page_title', 'Form Pengaduan & Layanan Wali Murid')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Form Pengaduan -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
            <div>
                <h2 class="text-xl font-black text-slate-900">Kirim Pengaduan</h2>
                <p class="text-xs text-slate-400 mt-1">Sampaikan keluhan, masukan, atau kendala sekolah secara resmi.</p>
            </div>

            <form action="{{ route('parent.complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700">Judul Pengaduan <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Kendala Pembayaran / Kehadiran Siswa" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs py-2.5 px-3">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700">Detail Pengaduan <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="Tuliskan keluhan atau masukan Anda secara rinci..." class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs p-3"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700">Lampiran Foto Bukti (Opsional)</label>
                    <input type="file" name="proof_photo" accept="image/*" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs py-2 px-3">
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-xs shadow-md transition-all">
                    Kirim Pengaduan
                </button>
            </form>
        </div>

        <!-- Tabel Riwayat Pengaduan -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
            <h2 class="text-lg font-black text-slate-900">Riwayat Pengaduan Anda</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-4 py-3 font-bold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 font-bold text-slate-500 uppercase">Judul</th>
                            <th class="px-4 py-3 font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-3 font-bold text-slate-500 uppercase">Tanggapan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($complaints as $c)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 text-slate-600 font-mono">
                                {{ $c->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $c->title }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $c->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($c->status === 'in_progress' ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $c->status_text }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $c->response ?: 'Belum ada tanggapan' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400 italic">
                                Belum ada pengaduan yang dikirim.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
