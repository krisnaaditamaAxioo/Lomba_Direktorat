<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Terminal - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-high": "#f0f4f8",
                        "secondary-container": "#e2e8f0",
                        "on-primary-fixed": "#1e293b",
                        "primary-fixed": "#f1f5f9",
                        "on-secondary-container": "#475569",
                        "surface-container": "#f8fafc",
                        "outline": "#cbd5e1",
                        "inverse-surface": "#0f172a",
                        "on-primary-container": "#334155",
                        "tertiary-container": "#fef3c7",
                        "surface-tint": "#6366f1",
                        "surface-bright": "#ffffff",
                        "inverse-primary": "#818cf8",
                        "outline-variant": "#e2e8f0",
                        "on-error": "#ffffff",
                        "tertiary": "#d97706",
                        "secondary-fixed-dim": "#94a3b8",
                        "error": "#ef4444",
                        "secondary": "#64748b",
                        "on-background": "#0f172a",
                        "surface-variant": "#f1f5f9",
                        "tertiary-fixed-dim": "#fcd34d",
                        "primary-fixed-dim": "#94a3b8",
                        "on-tertiary-container": "#92400e",
                        "on-primary-fixed-variant": "#475569",
                        "surface-dim": "#e2e8f0",
                        "secondary-fixed": "#cbd5e1",
                        "on-secondary-fixed-variant": "#334155",
                        "on-surface-variant": "#64748b",
                        "primary": "#4f46e5",
                        "on-tertiary": "#ffffff",
                        "background": "#f8fafc",
                        "tertiary-fixed": "#fde68a",
                        "surface-container-low": "#f1f5f9",
                        "on-surface": "#1e293b",
                        "on-tertiary-fixed-variant": "#b45309",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-fixed": "#451a03",
                        "on-error-container": "#7f1d1d",
                        "surface-container-highest": "#e2e8f0",
                        "surface": "#ffffff",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#f8fafc",
                        "primary-container": "#6366f1",
                        "error-container": "#fee2e2",
                        "on-secondary": "#ffffff",
                        "on-secondary-fixed": "#1e293b"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "2xl": "2rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-page": "40px",
                        "stack-md": "20px",
                        "container-max": "1440px",
                        "unit": "4px",
                        "stack-sm": "12px",
                        "stack-lg": "32px",
                        "gutter": "32px"
                    },
                    "fontFamily": {
                        "inter": ["Inter", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        .qr-glow {
            box-shadow: 0 0 30px rgba(79, 70, 229, 0.15);
            animation: qr-pulse 3s infinite ease-in-out;
        }
        @keyframes qr-pulse {
            0%, 100% { border-color: rgba(79, 70, 229, 0.2); }
            50% { border-color: rgba(79, 70, 229, 0.6); }
        }
        .scan-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #6366f1, transparent);
            animation: scan 3s linear infinite;
        }
        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
        .subtle-bg {
            background: radial-gradient(circle at top left, #f8fafc, #f1f5f9);
        }
        .scanner-input {
            position: absolute;
            opacity: 0.01;
            top: -100px;
            left: -100px;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }
    </style>
</head>
<body class="subtle-bg text-on-surface min-h-screen selection:bg-primary/10 antialiased">
    <!-- Refined Header -->
    <header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-10 h-20 bg-white/60 backdrop-blur-md border-b border-white/40">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-[20px]">account_balance</span>
                </div>
                <span class="text-xl font-semibold tracking-tight text-on-surface">Attendance Terminal</span>
            </div>
            <div class="h-6 w-[1px] bg-slate-200"></div>
            <div class="flex items-center gap-2 px-4 py-1.5 bg-green-50 border border-green-100 rounded-full">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[12px] font-medium text-green-700 uppercase tracking-wider">System Live</span>
            </div>
        </div>
        <div class="flex items-center gap-8">
            <div class="flex flex-col items-end">
                <span class="text-[13px] font-semibold text-on-surface">{{ date('l, M d') }}</span>
                <span class="text-[11px] font-medium text-slate-400 uppercase tracking-[0.1em]">Main Entrance</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="p-2.5 hover:bg-slate-100 rounded-full transition-all text-slate-500">
                    <span class="material-symbols-outlined">login</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Hidden Input for Hardware Scanner -->
    <input type="text" id="hardware-scanner-input" class="scanner-input" autofocus autocomplete="off">

    <main class="pt-28 pb-12 px-10 max-w-[1440px] mx-auto grid grid-cols-12 gap-gutter">
        <!-- Left: Recently Scanned -->
        <section class="col-span-12 lg:col-span-3">
            <div class="glass-panel rounded-2xl p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-semibold text-on-surface">Recent Logs</h2>
                        <p class="text-xs text-slate-400">Live activity feed</p>
                    </div>
                    <span class="w-2.5 h-2.5 bg-primary rounded-full animate-pulse"></span>
                </div>
                <div class="space-y-4 flex-1 overflow-y-auto max-h-[500px]" id="recent-logs">
                    @forelse($recentLogs as $log)
                    <div class="group flex items-center gap-4 p-4 rounded-xl hover:bg-white/80 border border-transparent hover:border-slate-100 transition-all cursor-default">
                        <div class="relative">
                            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold ring-2 ring-white shadow-sm">
                                {{ strtoupper(substr($log->student->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 {{ $log->status == 'present' ? 'bg-green-500' : 'bg-amber-400' }} border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $log->student->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Angkatan {{ $log->student->angkatan ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-primary">{{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i A') : '--:--' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-slate-400 text-sm py-8">No logs yet today</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Center: QR Scan Area -->
        <section class="col-span-12 lg:col-span-6">
            <div class="glass-panel rounded-2xl p-16 h-full flex flex-col items-center justify-center text-center">
                @if($isHoliday)
                    <div class="mb-12 p-8 bg-red-50 border border-red-200 rounded-2xl w-full max-w-md">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-red-500 text-[40px]">event_busy</span>
                        </div>
                        <span class="text-[11px] font-bold text-red-600 uppercase tracking-[0.2em] mb-3 block">Hari Libur</span>
                        <h1 class="text-2xl font-semibold text-red-800 mb-2">Tidak Bisa Absensi</h1>
                        <p class="text-red-600 font-light text-base leading-relaxed">Hari ini adalah hari libur: {{ $holidayName }}</p>
                    </div>
                @else
                    <div class="mb-12">
                        <span class="text-[11px] font-bold text-primary uppercase tracking-[0.2em] mb-3 block">Ready to Scan</span>
                        <h1 class="text-4xl font-light text-slate-900 mb-4 tracking-tight">Welcome, Student</h1>
                        <p class="text-slate-500 font-light text-lg max-w-sm mx-auto leading-relaxed">Silakan pindai kartu identitas digital, NIS, atau NISN Anda pada alat scanner.</p>
                    </div>
                @endif
                
                @if(!$isHoliday)
                    <!-- Scanner Status -->
                    <div id="scan-status" class="hidden mb-6 p-4 rounded-2xl w-full max-w-md transition-all">
                        <p id="status-text" class="text-sm font-semibold"></p>
                    </div>

                    <!-- Enhanced QR Frame -->
                    <div class="relative w-80 h-80 mb-16 group">
                        <div class="absolute -inset-4 border border-primary/10 rounded-[2.5rem] qr-glow"></div>
                        <div class="absolute top-0 left-0 w-14 h-14 border-t-2 border-l-2 border-primary/60 rounded-tl-3xl z-10"></div>
                        <div class="absolute top-0 right-0 w-14 h-14 border-t-2 border-r-2 border-primary/60 rounded-tr-3xl z-10"></div>
                        <div class="absolute bottom-0 left-0 w-14 h-14 border-b-2 border-l-2 border-primary/60 rounded-bl-3xl z-10"></div>
                        <div class="absolute bottom-0 right-0 w-14 h-14 border-b-2 border-r-2 border-primary/60 rounded-br-3xl z-10"></div>
                        
                        <div class="w-full h-full bg-slate-50/80 rounded-[2rem] flex items-center justify-center relative overflow-hidden shadow-inner">
                            <div id="placeholder-icon" class="absolute inset-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[140px] text-slate-200">barcode_scanner</span>
                            </div>
                            <div class="absolute inset-0 scan-line pointer-events-none"></div>
                        </div>
                    </div>

                    <div class="w-full max-w-sm space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="h-[1px] flex-1 bg-slate-100"></div>
                            <span class="text-[11px] font-bold text-slate-300 uppercase tracking-widest">Pendaftaran</span>
                            <div class="h-[1px] flex-1 bg-slate-100"></div>
                        </div>

                        <a href="{{ route('registrations.form') }}" 
                            class="w-full py-4 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-2xl font-medium text-sm shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined text-primary">person_add</span>
                            Daftar Siswa Baru
                        </a>
                        <p class="text-[11px] text-slate-400">Klik tombol di atas untuk menuju halaman pendaftaran</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Right: Clock & Stats -->
        <section class="col-span-12 lg:col-span-3 space-y-gutter">
            <div class="glass-panel rounded-2xl p-10 flex flex-col items-center justify-center">
                <div class="relative w-48 h-48 rounded-full border border-slate-100/50 bg-white/40 shadow-inner flex items-center justify-center">
                    <div class="absolute inset-4">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1px] h-3 bg-slate-200"></div>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-[1px] w-3 bg-slate-200"></div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[1px] h-3 bg-slate-200"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-[1px] w-3 bg-slate-200"></div>
                    </div>
                    <!-- Simplified Analog Clock with JS -->
                    <div id="hour-hand" class="absolute w-[3px] h-12 bg-slate-800 rounded-full bottom-1/2 origin-bottom transform z-10"></div>
                    <div id="min-hand" class="absolute w-[2px] h-18 bg-slate-400 rounded-full bottom-1/2 origin-bottom transform z-10"></div>
                    <div id="sec-hand" class="absolute w-[1px] h-20 bg-primary/40 rounded-full bottom-1/2 origin-bottom transform z-20"></div>
                    <div class="w-2.5 h-2.5 bg-slate-800 rounded-full z-30 ring-4 ring-white/50 shadow-sm"></div>
                </div>
                <div class="mt-10 text-center">
                    <div id="digital-clock" class="text-5xl font-light tracking-tight text-slate-900 leading-none mb-1">00:00</div>
                    <div id="time-period" class="text-xs font-bold text-primary tracking-[0.4em] uppercase">...</div>
                </div>
            </div>

            <div class="bg-primary p-7 rounded-2xl text-white shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4 opacity-80">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Quick Tip</span>
                    </div>
                    <p class="text-sm font-medium leading-relaxed">Gunakan kartu identitas digital Anda untuk proses absensi yang lebih cepat.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Floating Footer Stats -->
    <footer class="fixed bottom-8 left-1/2 -translate-x-1/2 w-full max-w-3xl px-10 z-40">
        <div class="bg-white/70 backdrop-blur-xl rounded-full border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] p-5 flex items-center justify-around">
            <div class="flex items-center gap-4 px-6 border-r border-slate-100">
                <div class="p-2 bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined text-primary">groups</span>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Scans</p>
                    <p class="text-lg font-semibold text-slate-800" id="stat-total">{{ $stats['total_scans'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 px-6 border-r border-slate-100">
                <div class="p-2 bg-green-50 rounded-lg">
                    <span class="material-symbols-outlined text-green-500">done_all</span>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">On Time</p>
                    <p class="text-lg font-semibold text-slate-800" id="stat-on-time">{{ $stats['on_time'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 px-6">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <span class="material-symbols-outlined text-amber-500">access_time_filled</span>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Late</p>
                    <p class="text-lg font-semibold text-slate-800" id="stat-late">{{ $stats['late'] }}</p>
                </div>
            </div>
        </div>
    </footer>

    <audio id="beep-success" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3"></audio>
    <audio id="beep-error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>

    <script>
        // Clock Logic
        function updateClock() {
            const now = new Date();
            const h = now.getHours();
            const m = now.getMinutes();
            const s = now.getSeconds();
            
            // Digital
            document.getElementById('digital-clock').innerText = 
                `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
            document.getElementById('time-period').innerText = h >= 12 ? 'Afternoon' : 'Morning';

            // Analog
            const hDeg = (h % 12) * 30 + m * 0.5;
            const mDeg = m * 6;
            const sDeg = s * 6;
            
            document.getElementById('hour-hand').style.transform = `rotate(${hDeg}deg)`;
            document.getElementById('min-hand').style.transform = `rotate(${mDeg}deg)`;
            document.getElementById('sec-hand').style.transform = `rotate(${sDeg}deg)`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Hardware Scanner Logic
        const scannerInput = document.getElementById('hardware-scanner-input');
        const statusDiv = document.getElementById('scan-status');
        const statusText = document.getElementById('status-text');
        const successSound = document.getElementById('beep-success');
        const errorSound = document.getElementById('beep-error');

        let isProcessing = false;

        // Keep input focused at all times
        function keepFocus() {
            if (!isProcessing && document.activeElement !== scannerInput) {
                scannerInput.focus();
            }
        }
        
        document.addEventListener('click', keepFocus);
        window.addEventListener('focus', keepFocus);
        setInterval(keepFocus, 500);
        keepFocus();

        scannerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const decodedText = this.value.trim();
                this.value = ''; // Clear for next scan
                
                if (decodedText && !isProcessing) {
                    processScan(decodedText);
                }
            }
        });

        function processScan(decodedText) {
            isProcessing = true;
            
            statusDiv.classList.remove('hidden', 'bg-green-50', 'bg-red-50');
            statusDiv.classList.add('bg-orange-50', 'block');
            statusText.className = 'text-orange-700 font-bold';
            statusText.innerText = "Processing ID: " + decodedText + "...";

            fetch("{{ route('attendance.process-scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id_siswa: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                statusDiv.classList.remove('bg-orange-50');
                
                if (data.success) {
                    successSound.play();
                    statusDiv.classList.add('bg-green-50');
                    statusText.className = 'text-green-700 font-bold';
                    statusText.innerText = data.message;
                    
                    // Refresh logs and stats if needed, or just reload page
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    errorSound.play();
                    statusDiv.classList.add('bg-red-50');
                    statusText.className = 'text-red-700 font-bold';
                    statusText.innerText = data.message;
                    
                    setTimeout(() => {
                        isProcessing = false;
                        statusDiv.classList.add('hidden');
                        statusDiv.classList.remove('block');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorSound.play();
                statusDiv.classList.remove('bg-orange-50');
                statusDiv.classList.add('bg-red-50');
                statusText.className = 'text-red-700 font-bold';
                statusText.innerText = "Connection error occurred.";
                
                setTimeout(() => {
                    isProcessing = false;
                    statusDiv.classList.add('hidden');
                    statusDiv.classList.remove('block');
                }, 3000);
            });
        }
    </script>
</body>
</html>
