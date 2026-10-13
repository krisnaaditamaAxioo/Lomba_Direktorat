<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Pendaftaran - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9;
        }
        .search-focus:focus-within {
            ring: 2px;
            ring-color: #4f46e5;
            border-color: #4f46e5;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="max-w-md w-full">
        <!-- Logo/Header Area -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-600 rounded-2xl shadow-lg shadow-orange-200 mb-4">
                <i data-lucide="search" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Cek Status Seleksi</h1>
            <p class="text-slate-500 text-sm mt-2">Masukkan NISN Anda untuk melihat perkembangan pendaftaran</p>
        </div>

        <!-- Search Card -->
        <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/60 p-8 border border-slate-100">
            <form action="{{ route('registrations.track') }}" method="GET" id="searchForm" class="space-y-6">
                <div>
                    <label for="nisn" class="block text-sm font-semibold text-slate-700 mb-2 ml-1">Nomor Induk Siswa Nasional (NISN)</label>
                    <div class="relative flex items-center group">
                        <div class="absolute left-4 text-slate-400 group-focus-within:text-orange-600 transition-colors">
                            <i data-lucide="hash" class="w-5 h-5"></i>
                        </div>
                        <input 
                            type="text" 
                            id="nisn" 
                            name="nisn" 
                            placeholder="Contoh: 0012345678" 
                            required
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                        >
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 ml-1 italic">*Pastikan NISN sesuai dengan yang terdaftar.</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2 group"
                >
                    <span>Cari Data</span>
                    <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Loading State (Hidden by default) -->
            <div id="loadingState" class="hidden py-8 text-center">
                <div class="inline-block w-8 h-8 border-4 border-orange-600/20 border-t-orange-600 rounded-full animate-spin mb-4"></div>
                <p class="text-slate-500 text-sm">Mencari data di server...</p>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 flex flex-col items-center gap-4">
            <a href="{{ route('registrations.form') }}" class="text-orange-600 hover:text-orange-700 font-bold text-sm transition-all flex items-center gap-2">
                Belum mendaftar? Klik di sini <i data-lucide="external-link" class="w-4 h-4"></i>
            </a>
            <p class="text-slate-400 text-[11px]">© {{ date('Y') }} Smart Axioo Class Program. All rights reserved.</p>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const form = e.target;
            const loading = document.getElementById('loadingState');
            
            form.classList.add('opacity-50', 'pointer-events-none');
            loading.classList.remove('hidden');
        });

        window.onload = function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };
    </script>
</body>
</html>
