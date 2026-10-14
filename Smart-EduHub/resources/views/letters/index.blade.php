@extends('layouts.app')

@section('title', 'Surat Resmi & Peringatan')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-display-xl text-display-xl text-on-surface">Surat Resmi & Peringatan</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar surat binaan dan peringatan yang diterbitkan otomatis berdasarkan poin pelanggaran.</p>
    </div>
    <a href="{{ route('letters.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-orange-600 text-white rounded-2xl text-sm font-bold hover:bg-orange-700 shadow-lg shadow-orange-100">
        <span class="material-symbols-outlined">add</span>
        Buat Surat Baru
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-50">
        <h4 class="font-bold text-slate-900">Riwayat Penerbitan & Ekspedisi Surat</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Siswa</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Jenis Surat</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Ekspedisi (Status)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Poin</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">No. Referensi</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($letters as $letter)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr($letter->student->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $letter->student->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Angkatan {{ $letter->student->angkatan ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $typeColor = match($letter->type) {
                                'binaan' => 'bg-orange-50 text-orange-600',
                                'sp1' => 'bg-amber-50 text-amber-600',
                                'sp2' => 'bg-orange-50 text-orange-600',
                                'sp3' => 'bg-rose-50 text-rose-600',
                                default => 'bg-slate-50 text-slate-600'
                            };
                            $typeLabel = match($letter->type) {
                                'binaan' => 'Binaan',
                                'sp1' => 'SP 1',
                                'sp2' => 'SP 2',
                                'sp3' => 'SP 3',
                                default => $letter->type
                            };
                        @endphp
                        <span class="px-2.5 py-1 {{ $typeColor }} rounded-lg text-[10px] font-bold uppercase">{{ $typeLabel }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-2">
                            <form action="{{ route('letters.status', $letter) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-[10px] font-bold uppercase py-1 px-2 rounded-lg border-none {{ $letter->status == 'processed' ? 'bg-emerald-50 text-emerald-600' : ($letter->status == 'sent' ? 'bg-orange-50 text-orange-600' : ($letter->status == 'received' ? 'bg-orange-50 text-orange-600' : 'bg-slate-50 text-slate-400')) }} focus:ring-0 cursor-pointer">
                                    <option value="draft" {{ $letter->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="sent" {{ $letter->status == 'sent' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="received" {{ $letter->status == 'received' ? 'selected' : '' }}>Diterima Wali</option>
                                    <option value="processed" {{ $letter->status == 'processed' ? 'selected' : '' }}>Diproses</option>
                                </select>
                            </form>
                            @if($letter->result_note)
                                <p class="text-[10px] text-slate-500 italic max-w-[150px] truncate" title="{{ $letter->result_note }}">{{ $letter->result_note }}</p>
                            @endif
                            <button onclick="openNoteModal('{{ $letter->id }}', '{{ addslashes($letter->result_note) }}')" class="text-[9px] font-bold text-orange-600 hover:underline text-left">
                                {{ $letter->result_note ? 'Ubah Catatan' : '+ Tambah Catatan' }}
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-700">
                        {{ $letter->points }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 font-mono">
                        {{ $letter->reference_number }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-400">
                        {{ $letter->issued_at->format('d/m/y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-1">
                            @php
                                $waUrl = "";
                                if ($letter->student->parent_phone) {
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $letter->student->parent_phone);
                                    if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                                    
                                    $msgType = match($letter->type) {
                                        'binaan' => 'Surat Binaan Kedisiplinan',
                                        'sp1' => 'Surat Peringatan 1 (SP1)',
                                        'sp2' => 'Surat Peringatan 2 (SP2)',
                                        'sp3' => 'Surat Peringatan 3 (SP3)',
                                        default => 'Surat Resmi'
                                    };
                                    
                                    $message = "NOTIFIKASI SURAT RESMI\n\nKepada Yth. Orang Tua/Wali dari " . $letter->student->nama_lengkap . ",\n\nKami menginformasikan bahwa sekolah telah menerbitkan *" . $msgType . "* sehubungan dengan akumulasi poin pelanggaran putra/putri Bapak/Ibu yang telah mencapai " . $letter->points . " poin.\n\nSurat fisik akan segera dikirimkan. Bapak/Ibu juga dapat mengecek detailnya melalui portal siswa.\n\nTerima kasih.\n*Smart Axioo Class Program*";
                                    $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
                                }
                            @endphp
                            
                            @if($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Kirim Notifikasi WA ke Wali">
                                <span class="material-symbols-outlined text-[18px]">chat</span>
                            </a>
                            @endif

                            <a href="{{ route('letters.print', ['student' => $letter->student_id, 'type' => $letter->type]) }}" target="_blank" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all" title="Cetak Surat">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                            </a>
                            <form action="{{ route('letters.destroy', $letter) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Surat">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-400 italic">
                        Belum ada surat yang diterbitkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($letters->hasPages())
    <div class="p-6 border-t border-slate-50">
        {{ $letters->links() }}
    </div>
    @endif
</div>

<!-- Modal Catatan -->
<div id="noteModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl animate-fade-in-down">
        <form id="noteForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Catatan Hasil Proses</h3>
                <p class="text-xs text-slate-500 mt-1">Tambahkan catatan hasil pertemuan atau proses surat ini.</p>
            </div>
            <div class="p-6">
                <textarea name="result_note" id="result_note_input" rows="4" class="w-full rounded-xl border-slate-200 focus:ring-primary focus:border-primary text-sm" placeholder="Contoh: Orang tua sudah datang, siswa berjanji tidak mengulangi..."></textarea>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeNoteModal()" class="px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-lg transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-primary hover:bg-orange-700 rounded-lg transition-all">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    function openNoteModal(id, note) {
        const modal = document.getElementById('noteModal');
        const form = document.getElementById('noteForm');
        const input = document.getElementById('result_note_input');
        
        form.action = `/letters/${id}/status`;
        input.value = note || '';
        
        modal.classList.remove('hidden');
    }

    function closeNoteModal() {
        document.getElementById('noteModal').classList.add('hidden');
    }

    // Close modal on click outside
    document.getElementById('noteModal').addEventListener('click', function(e) {
        if (e.target === this) closeNoteModal();
    });
</script>
@endsection
@endsection
