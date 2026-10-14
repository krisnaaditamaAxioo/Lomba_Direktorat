<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proses Pendaftaran - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9;
        }
        .step-transition {
            transition: all 0.5s ease-in-out;
        }
        .progress-line-active {
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media print {
            .no-print { display: none; }
            .print-only { display: block !important; }
            body { background: white; p: 0; }
            .main-card { box-shadow: none; border: none; }
        }
        .print-only { display: none; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="max-w-4xl w-full">
        @if(!$registration)
            <!-- Search Form if no registration found -->
            <div class="bg-white rounded-[2rem] shadow-xl p-8 md:p-12 text-center border border-slate-100 no-print">
                <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="search" class="w-10 h-10"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-4">Lacak Pendaftaran</h2>
                <p class="text-slate-500 mb-8">Masukkan ID Pendaftaran Anda untuk melihat status seleksi terbaru.</p>
                
                <form action="{{ route('registrations.track') }}" method="GET" class="max-w-md mx-auto">
                    <div class="relative mb-4">
                        <input type="text" name="id" value="{{ $id }}" placeholder="Contoh: REG-6638..." 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-center font-bold text-slate-700">
                    </div>
                    <button type="submit" class="w-full py-4 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-100">
                        Cek Status Sekarang
                    </button>
                </form>

                @if($id)
                    <p class="mt-6 text-rose-500 text-sm font-medium">ID Pendaftaran "{{ $id }}" tidak ditemukan. Silakan periksa kembali.</p>
                @endif
            </div>
        @else
            <!-- Main Card -->
            <div class="main-card bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100">
                <!-- Header Section -->
                <div class="bg-orange-600 p-6 md:p-10 text-white relative no-print">
                    <div class="absolute top-0 right-0 p-8 opacity-10 hidden sm:block">
                        <i data-lucide="activity" class="w-24 h-24 md:w-32 md:h-32"></i>
                    </div>
                    <div class="relative z-10 text-center md:text-left">
                        <h1 class="text-xl md:text-3xl font-bold mb-2">Pelacakan Alur Seleksi</h1>
                        <p class="text-orange-100 opacity-90 text-xs md:text-base">Lihat perkembangan pendaftaran Anda dari tahap awal hingga hasil akhir.</p>
                    </div>
                </div>

                <!-- Print Header (Only visible when printing) -->
                <div class="print-only p-10 text-center border-b-2 border-slate-900 mb-8">
                    <h1 class="text-2xl font-black uppercase">Bukti Pendaftaran Siswa Baru</h1>
                    <h2 class="text-xl font-bold text-orange-600">SMART AXIOO SCHOOL</h2>
                    <p class="text-sm text-slate-500 mt-2">Tahun Akademik {{ $registration->academic_year }}</p>
                </div>

                <!-- Content Section -->
                <div class="p-6 md:p-12">
                    <!-- User Info Bar -->
                    <div class="flex flex-col md:flex-row justify-between items-center bg-slate-50 rounded-2xl p-5 mb-10 md:mb-12 border border-slate-100 gap-4">
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-white rounded-full border border-slate-200 flex items-center justify-center text-orange-600 shadow-sm shrink-0">
                                <i data-lucide="user" class="w-5 h-5 md:w-6 md:h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm md:text-base">{{ $registration->name }}</h3>
                                <p class="text-[10px] md:text-xs text-slate-500">ID Daftar: #{{ $registration->application_id }} • Angkatan {{ $registration->angkatan ?? '-' }}</p>
                            </div>
                        </div>
                        <div id="globalStatusBadge" class="w-full md:w-auto px-4 py-2 
                            {{ $registration->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($registration->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }} 
                            rounded-full text-[10px] md:text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2">
                            @if($registration->status !== 'approved' && $registration->status !== 'rejected')
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                            @switch($registration->status)
                                @case('pending') Sedang Antri @break
                                @case('verified') Sedang Diproses @break
                                @case('approved') Diterima @break
                                @case('rejected') Ditolak @break
                                @default {{ ucfirst($registration->status) }}
                            @endswitch
                        </div>
                    </div>

                    <!-- Progress Tracker (Hidden in Print) -->
                    <div class="relative px-2 md:px-4 no-print">
                        <!-- Progress Line Desktop -->
                        <div class="absolute top-5 md:top-6 left-0 w-full h-1 bg-slate-100 rounded-full hidden md:block"></div>
                        @php
                            $progressWidth = '0%';
                            if($registration->status === 'pending') $progressWidth = '0%';
                            if($registration->status === 'verified') $progressWidth = '50%';
                            if($registration->status === 'approved') $progressWidth = '100%';
                        @endphp
                        <div id="activeProgressLine" class="absolute top-5 md:top-6 left-0 w-{{ $registration->status === 'pending' ? '0' : ($registration->status === 'verified' ? '1/2' : 'full') }} h-1 bg-orange-600 rounded-full hidden md:block progress-line-active" style="width: {{ $progressWidth }}"></div>

                        <!-- Steps Wrapper -->
                        <div class="relative z-10 flex flex-col md:flex-row justify-between gap-8 md:gap-4">
                            
                            <!-- Step 1: PENDING -->
                            <div class="flex flex-row md:flex-col items-start md:items-center text-left md:text-center flex-1 gap-4 md:gap-0 group">
                                <div class="relative flex flex-col items-center shrink-0">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full {{ in_array($registration->status, ['pending', 'verified', 'approved']) ? 'bg-emerald-500 text-white shadow-emerald-100' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shadow-lg transition-all z-10">
                                        <i data-lucide="{{ in_array($registration->status, ['verified', 'approved']) ? 'check' : 'clock' }}" class="w-5 h-5 md:w-6 md:h-6"></i>
                                    </div>
                                    <!-- Vertical Line Mobile -->
                                    <div class="absolute top-10 w-0.5 h-10 {{ in_array($registration->status, ['verified', 'approved']) ? 'bg-orange-600' : 'bg-slate-100' }} md:hidden"></div>
                                </div>
                                <div class="md:mt-4">
                                    <h4 class="font-bold {{ in_array($registration->status, ['pending', 'verified', 'approved']) ? 'text-slate-800' : 'text-slate-400' }} text-sm md:text-base">Pending</h4>
                                    <p class="text-[10px] md:text-[11px] text-slate-500 mt-1">Berkas dalam antrian.</p>
                                    <span class="text-[9px] md:text-[10px] text-emerald-600 font-bold mt-2 px-2 py-0.5 bg-emerald-50 rounded-full inline-block">Diterima</span>
                                </div>
                            </div>

                            <!-- Step 2: PROSES (Verified) -->
                            <div class="flex flex-row md:flex-col items-start md:items-center text-left md:text-center flex-1 gap-4 md:gap-0 group">
                                <div class="relative flex flex-col items-center shrink-0">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full 
                                        {{ in_array($registration->status, ['verified', 'approved']) ? 'bg-orange-600 text-white shadow-orange-200 ring-4 ring-orange-50' : 'bg-slate-100 text-slate-400' }} 
                                        flex items-center justify-center shadow-lg transition-all z-10">
                                        <i data-lucide="{{ $registration->status === 'verified' ? 'loader-2' : ($registration->status === 'approved' ? 'check' : 'activity') }}" class="w-5 h-5 md:w-6 md:h-6 {{ $registration->status === 'verified' ? 'animate-spin' : '' }}"></i>
                                    </div>
                                    <!-- Vertical Line Mobile -->
                                    <div class="absolute top-10 w-0.5 h-10 {{ $registration->status === 'approved' ? 'bg-orange-600' : 'bg-slate-100' }} md:hidden"></div>
                                </div>
                                <div class="md:mt-4">
                                    <h4 class="font-bold {{ in_array($registration->status, ['verified', 'approved']) ? 'text-orange-600' : 'text-slate-400' }} text-sm md:text-base">Proses</h4>
                                    <p class="text-[10px] md:text-[11px] text-slate-500 mt-1">Validasi dokumen.</p>
                                    <span class="text-[9px] md:text-[10px] {{ in_array($registration->status, ['verified', 'approved']) ? 'text-orange-600 bg-orange-50' : 'text-slate-300 bg-slate-50' }} font-bold mt-2 px-2 py-0.5 rounded-full inline-block">
                                        {{ in_array($registration->status, ['verified', 'approved']) ? 'Reviewing' : 'Menunggu' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Step 3: APPROVE (Diterima) -->
                            <div class="flex flex-row md:flex-col items-start md:items-center text-left md:text-center flex-1 gap-4 md:gap-0 group">
                                <div class="relative flex flex-col items-center shrink-0">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full {{ $registration->status === 'approved' ? 'bg-emerald-600 text-white shadow-emerald-200' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center transition-all z-10">
                                        <i data-lucide="award" class="w-5 h-5 md:w-6 md:h-6"></i>
                                    </div>
                                </div>
                                <div class="md:mt-4">
                                    <h4 class="font-bold {{ $registration->status === 'approved' ? 'text-emerald-600' : 'text-slate-400' }} text-sm md:text-base">Approve</h4>
                                    <p class="text-[10px] md:text-[11px] text-slate-400 mt-1">Hasil akhir seleksi.</p>
                                    <span class="text-[9px] md:text-[10px] {{ $registration->status === 'approved' ? 'text-emerald-600 bg-emerald-50' : 'text-slate-300 bg-slate-50' }} font-bold mt-2 px-2 py-0.5 rounded-full inline-block italic">
                                        {{ $registration->status === 'approved' ? 'Lulus Seleksi' : 'Menunggu' }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Print Details (Visible in Print & Approved) -->
                    <div class="print-only mt-10 space-y-6">
                        <div class="grid grid-cols-2 gap-8 border-t border-b border-slate-100 py-8">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap</p>
                                    <p class="font-bold text-slate-900">{{ $registration->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">NISN</p>
                                    <p class="font-bold text-slate-900">{{ $registration->nisn ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Angkatan</p>
                                    <p class="font-bold text-orange-600">Angkatan {{ $registration->angkatan ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</p>
                                    <p class="font-bold text-slate-900">{{ $registration->email }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">No. HP</p>
                                    <p class="font-bold text-slate-900">{{ $registration->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Daftar</p>
                                    <p class="font-bold text-slate-900">{{ $registration->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-12 text-center text-sm text-slate-500 italic">
                            *Simpan bukti ini sebagai tanda bukti pendaftaran Anda.
                        </div>
                    </div>

                    <!-- Detail Status Alert (Hidden in Print) -->
                    <div class="mt-12 md:mt-16 p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] 
                        {{ $registration->status === 'approved' ? 'bg-emerald-50 border-emerald-100' : ($registration->status === 'rejected' ? 'bg-rose-50 border-rose-100' : 'bg-orange-50 border-orange-100') }} 
                        border flex items-start gap-4 no-print">
                        <div class="p-2 md:p-3 bg-white rounded-xl md:rounded-2xl 
                            {{ $registration->status === 'approved' ? 'text-emerald-600' : ($registration->status === 'rejected' ? 'text-rose-600' : 'text-orange-600') }} 
                            shadow-sm shrink-0">
                            <i data-lucide="{{ $registration->status === 'approved' ? 'award' : ($registration->status === 'rejected' ? 'x-circle' : 'info') }}" class="w-5 h-5 md:w-6 md:h-6"></i>
                        </div>
                        <div>
                            <h5 class="font-bold 
                                {{ $registration->status === 'approved' ? 'text-emerald-900' : ($registration->status === 'rejected' ? 'text-rose-900' : 'text-orange-900') }} 
                                text-xs md:text-sm mb-1">
                                Status Saat Ini: 
                                @switch($registration->status)
                                    @case('pending') Tahap Antrian @break
                                    @case('verified') Tahap Verifikasi @break
                                    @case('approved') Berhasil Diterima! @break
                                    @case('rejected') Pendaftaran Ditolak @break
                                @endswitch
                            </h5>
                            <p class="text-[10px] md:text-xs 
                                {{ $registration->status === 'approved' ? 'text-emerald-700' : ($registration->status === 'rejected' ? 'text-rose-700' : 'text-orange-700') }} 
                                leading-relaxed">
                                @switch($registration->status)
                                    @case('pending')
                                        Pendaftaran Anda telah kami terima dan berada dalam antrian verifikasi. Mohon tunggu kabar selanjutnya.
                                        @break
                                    @case('verified')
                                        Kami sedang melakukan pengecekan data Anda. Jika valid, status akan menjadi <b>Approve</b> dan Anda bisa mencetak kartu pendaftaran.
                                        @break
                                    @case('approved')
                                        Selamat! Anda telah dinyatakan <b>Lulus Seleksi</b>. Silakan unduh dan cetak surat pendaftaran Anda melalui tombol di bawah.
                                        @break
                                    @case('rejected')
                                        Mohon maaf, pendaftaran Anda belum bisa kami proses lebih lanjut. Silakan hubungi admin untuk informasi lebih detail.
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    <!-- Interactive Buttons (Hidden in Print) -->
                    <div class="mt-6 md:mt-8 flex flex-col sm:flex-row gap-3 md:gap-4 no-print">
                        @if($whatsappGroupLink)
                            <a href="{{ $whatsappGroupLink }}" target="_blank" class="flex-1 py-3 md:py-4 bg-green-600 text-white text-sm md:text-base font-bold rounded-xl md:rounded-2xl hover:bg-green-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100">
                                <i data-lucide="message-circle" class="w-4 h-4 md:w-5 md:h-5"></i> Grup WhatsApp Casis
                            </a>
                        @else
                            <a href="https://wa.me/6281234567890" target="_blank" class="flex-1 py-3 md:py-4 bg-white border border-slate-200 text-slate-700 text-sm md:text-base font-bold rounded-xl md:rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4 md:w-5 md:h-5"></i> Hubungi Admin
                            </a>
                        @endif

                        @if(in_array($registration->status, ['pending', 'verified', 'approved', 'rejected']))
                            <a href="{{ route('registrations.download', ['id' => $registration->application_id]) }}" class="flex-1 py-3 md:py-4 bg-white border border-slate-200 text-slate-700 text-sm md:text-base font-bold rounded-xl md:rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center gap-2" title="Download PDF">
                                <i data-lucide="download" class="w-4 h-4 md:w-5 md:h-5"></i>
                                {{ in_array($registration->status, ['approved', 'rejected']) ? 'Download Hasil (PDF)' : 'Download Bukti (PDF)' }}
                            </a>
                        @endif
                        
                        @if($registration->status === 'verified' || ($registration->status === 'pending' && $registration->examResult))
                            @if(!$registration->examResult)
                                <a href="{{ route('exams.start', ['id' => $registration->application_id]) }}" class="flex-1 py-3 md:py-4 bg-orange-600 text-white hover:bg-orange-700 text-sm md:text-base font-bold rounded-xl md:rounded-2xl shadow-lg shadow-orange-100 transition-all flex items-center justify-center gap-2 animate-bounce">
                                    <i data-lucide="edit-3" class="w-4 h-4 md:w-5 md:h-5"></i> Kerjakan Soal Seleksi
                                </a>
                            @else
                                <button disabled class="flex-1 py-3 md:py-4 bg-emerald-50 text-emerald-600 border border-emerald-100 text-sm md:text-base font-bold rounded-xl md:rounded-2xl flex items-center justify-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 md:w-5 md:h-5"></i> Ujian Selesai
                                </button>
                            @endif
                        @elseif($registration->status === 'approved')
                            <button onclick="window.print()" class="flex-1 py-3 md:py-4 bg-orange-600 text-white hover:bg-orange-700 text-sm md:text-base font-bold rounded-xl md:rounded-2xl transition-all flex items-center justify-center gap-2">
                                <i data-lucide="printer" class="w-4 h-4 md:w-5 md:h-5"></i> Cetak
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Back Link (Hidden in Print) -->
        <div class="text-center mt-6 md:mt-8 flex flex-wrap justify-center gap-4 md:gap-6 no-print">
            <a href="{{ route('registrations.form') }}" class="text-slate-500 hover:text-orange-600 font-medium text-xs md:text-sm transition-all inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Form
            </a>
            <span class="text-slate-300 hidden sm:block">|</span>
            <a href="/" class="text-slate-500 hover:text-orange-600 font-medium text-xs md:text-sm transition-all inline-flex items-center gap-2">
                Halaman Utama <i data-lucide="home" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };
    </script>
</body>
</html>
