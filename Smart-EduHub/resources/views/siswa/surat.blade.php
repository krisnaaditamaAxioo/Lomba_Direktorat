@extends('layouts.siswa')

@section('title', 'Surat Resmi')
@section('page_title', 'Surat & Pemberitahuan')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <div class="px-2">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Pusat Pemberitahuan</h1>
        <p class="text-slate-500 font-medium mt-1">Pantau status kedisiplinan dan surat resmi yang diterbitkan sekolah.</p>
    </div>

    <div class="space-y-6">
        @forelse($letters as $letter)
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden hover:border-brand-200 transition-all">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-4xl">mark_email_unread</span>
                            </div>
                            <div>
                                @php
                                    $typeLabel = match($letter->type) {
                                        'binaan' => 'Surat Binaan Kedisiplinan',
                                        'sp1' => 'Surat Peringatan 1 (SP1)',
                                        'sp2' => 'Surat Peringatan 2 (SP2)',
                                        'sp3' => 'Surat Peringatan 3 (SP3)',
                                        default => 'Surat Resmi'
                                    };
                                @endphp
                                <h3 class="text-xl font-black text-slate-900 leading-tight">{{ $typeLabel }}</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $letter->reference_number }}</span>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $letter->issued_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @php
                                $statusClass = match($letter->status) {
                                    'processed' => 'bg-emerald-500 text-white',
                                    'received' => 'bg-brand-500 text-white',
                                    'sent' => 'bg-brand-500 text-white',
                                    default => 'bg-slate-200 text-slate-600'
                                };
                                $statusLabel = match($letter->status) {
                                    'processed' => 'Selesai Diproses',
                                    'received' => 'Diterima Wali',
                                    'sent' => 'Dalam Pengiriman',
                                    default => 'Sedang Disiapkan'
                                };
                            @endphp
                            <span class="px-4 py-2 rounded-xl {{ $statusClass }} text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-8 border-t border-slate-50">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poin Pelanggaran</p>
                            <p class="text-lg font-black text-rose-600">{{ $letter->points }} Poin</p>
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan Hasil / Keterangan</p>
                            <p class="text-sm text-slate-600 font-medium italic">
                                {{ $letter->result_note ?: 'Belum ada catatan tindak lanjut dari sekolah.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button onclick="viewLetter('{{ route('siswa.letters.view', ['student' => $letter->student_id, 'type' => $letter->type, 'view_only' => 1]) }}')" class="w-full py-4 bg-slate-50 text-slate-900 rounded-2xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-2 hover:bg-slate-900 hover:text-white transition-all group">
                            <span class="material-symbols-outlined text-lg group-hover:scale-110 transition-transform">visibility</span>
                            Lihat Isi Surat
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-20 text-center">
                <div class="w-24 h-24 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <span class="material-symbols-outlined text-5xl">notifications_off</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Tidak Ada Surat</h3>
                <p class="text-slate-500 mt-2 max-w-sm mx-auto font-medium">Anda belum memiliki riwayat surat resmi atau pemberitahuan kedisiplinan dari sekolah.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Letter View Modal -->
<div id="letterModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] w-full max-w-4xl h-[90vh] flex flex-col shadow-2xl animate-fade-in-down">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900">Detail Surat Resmi</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pratinjau Dokumen (Hanya Lihat)</p>
            </div>
            <button onclick="closeLetterModal()" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all flex items-center justify-center">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-hidden p-2 bg-slate-100 relative">
            <div id="iframeLoader" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-brand-600/20 border-t-brand-600 rounded-full animate-spin"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Memuat Dokumen...</p>
                </div>
            </div>
            <iframe id="letterIframe" class="w-full h-full rounded-2xl bg-white shadow-inner opacity-0 transition-opacity duration-300" src="" onload="hideLoader()"></iframe>
        </div>
        <div class="p-6 bg-white rounded-b-[32px] text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Surat ini bersifat rahasia dan diterbitkan secara otomatis oleh sistem.</p>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function viewLetter(url) {
        // alert('Membuka surat...'); // Temporary debug
        console.log('Viewing letter:', url);
        const modal = document.getElementById('letterModal');
        const iframe = document.getElementById('letterIframe');
        const loader = document.getElementById('iframeLoader');
        
        if (!modal || !iframe || !loader) {
            console.error('Modal elements not found');
            // Fallback: open in new tab
            window.open(url, '_blank');
            return;
        }

        // Reset state
        loader.classList.remove('hidden');
        iframe.classList.add('opacity-0');
        iframe.src = url;
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideLoader() {
        console.log('Iframe loaded');
        const iframe = document.getElementById('letterIframe');
        const loader = document.getElementById('iframeLoader');
        
        if (loader) loader.classList.add('hidden');
        if (iframe) {
            iframe.classList.remove('opacity-0');
            iframe.classList.add('opacity-100');
        }
    }

    function closeLetterModal() {
        const modal = document.getElementById('letterModal');
        const iframe = document.getElementById('letterIframe');
        
        if (modal) modal.classList.add('hidden');
        if (iframe) iframe.src = '';
        document.body.style.overflow = 'auto';
    }

    // Close on click outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('letterModal');
        if (e.target === modal) closeLetterModal();
    });
</script>
@endsection
@endsection
