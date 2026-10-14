@extends('layouts.app')

@section('content')
<style>
    #qrVideoContainer video {
        width: 100% !important;
        height: 340px !important;
        object-fit: cover !important;
        border-radius: 1.5rem !important;
    }
    #qrVideoContainer { position: relative !important; }
    #qrVideoContainer div[role="button"] { display: none !important; }

    @keyframes scan {
        0% { transform: translateY(0); }
        50% { transform: translateY(220px); }
        100% { transform: translateY(0); }
    }
    .scanner-laser {
        position: absolute; inset-x: 0; height: 3px;
        background: linear-gradient(to right, transparent, #f97316, transparent);
        animation: scan 2.5s linear infinite; z-index: 10;
    }

    @media (max-width: 767px) {
        .page-hide-topbar-mobile, body > header, #app > header,
        header.sticky, header.fixed, #topNavbar, .top-navbar, [data-topbar] {
            display: none !important;
        }
        main, #app, .main-content, body > div { padding-top: 0 !important; }
    }
</style>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pt-0 pb-4 sm:py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6 sm:space-y-8">

        {{-- ===== HEADER (hero) ===== --}}
        <div class="relative overflow-hidden -mx-4 mt-0 sm:mx-0 rounded-b-[2.5rem] sm:rounded-3xl bg-gradient-to-br from-[#f98a3c] via-[#ea4a2a] to-[#8f1d1d] px-5 pt-5 pb-8 sm:px-8 sm:py-8 shadow-lg shadow-orange-500/20">
            <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -left-14 bottom-0 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-10 right-16 h-32 w-32 rounded-full bg-white/5"></div>

            <div class="relative">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.finance.dashboard') }}" aria-label="Kembali"
                       class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white/20 text-white ring-1 ring-white/25 backdrop-blur transition hover:bg-white/30 active:scale-95">
                        <span class="material-symbols-outlined text-[22px]">arrow_back</span>
                    </a>
                    <div class="flex items-center gap-2 rounded-full bg-white px-3.5 py-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[18px] text-orange-500">bolt</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cepat</span>
                        <span class="text-xs font-extrabold text-slate-900 sm:text-sm">Transaksi Instan</span>
                    </div>
                </div>

                <div class="mt-5 text-center sm:mt-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-orange-100/90 sm:text-xs">Transaksi Cepat</p>
                    <h1 class="mt-1 text-3xl font-black tracking-tight text-white sm:text-4xl">Topup &middot; Tarik Tunai &middot; Kantin</h1>
                    <p class="mx-auto mt-2 max-w-md text-xs font-medium text-orange-100/90 sm:text-sm">
                        Scan kartu siswa, pilih jenis transaksi, lalu minta siswa memasukkan PIN untuk otorisasi.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Scanner --}}
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-600">qr_code_scanner</span>
                        Kamera Scanner QR
                    </h3>
                    <div id="qrReaderContainer" class="relative bg-slate-900 rounded-2xl overflow-hidden shadow-inner h-84 flex items-center justify-center">
                        <div id="scannerOverlay" class="hidden absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
                            <div class="w-60 h-60 border-2 border-dashed border-white/50 rounded-2xl relative">
                                <div class="scanner-laser"></div>
                            </div>
                        </div>
                        <div id="qrPlaceholder" class="text-center text-slate-500 p-8">
                            <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-700">
                                <span class="material-symbols-outlined text-3xl text-orange-400">videocam</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-300">Kamera Belum Aktif</p>
                            <p class="text-xs text-slate-500 mt-1">Klik tombol di bawah untuk meminta akses kamera</p>
                        </div>
                        <div id="qrVideoContainer" class="w-full hidden"></div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <button type="button" id="toggleScannerBtn" onclick="toggleScanner()" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 rounded-2xl font-bold shadow-md transition-all uppercase tracking-wider text-xs flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                        <span>Mulai Ambil Scan</span>
                    </button>
                    <p id="qrStatus" class="text-center text-xs text-slate-400 dark:text-slate-500">Status: Siap digunakan</p>
                </div>
            </div>

            {{-- Manual Search --}}
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-600">search</span>
                        Pencarian Manual
                    </h3>
                    <form id="searchForm" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">NIS / Nomor Kartu</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                                <input type="text" id="searchInput" placeholder="Contoh: 202410023" autocomplete="off"
                                    class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold shadow-lg shadow-orange-200 dark:shadow-none transition-all uppercase tracking-wider text-xs flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">search</span>
                            Cari Data Siswa
                        </button>
                    </form>
                </div>
                <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 flex gap-3 items-start">
                    <span class="material-symbols-outlined text-slate-400">info</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Jika QR Code pada kartu rusak/kotor, silakan ketikkan NIS atau nomor kartu secara manual.
                    </p>
                </div>
            </div>
        </div>

        <div id="loadingState" class="hidden animate-pulse">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 text-center border border-slate-100 dark:border-slate-800">
                <div class="w-8 h-8 border-3 border-orange-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Memvalidasi ID & mengambil data kartu...</p>
            </div>
        </div>

        <div id="errorMessage" class="hidden bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/50 text-rose-700 dark:text-rose-400 px-5 py-4 rounded-2xl flex items-center gap-3">
            <span class="material-symbols-outlined flex-shrink-0">error</span>
            <span class="text-xs sm:text-sm font-bold" id="errorText"></span>
        </div>

        <div id="successMessage" class="hidden bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl flex items-center gap-3">
            <span class="material-symbols-outlined flex-shrink-0">check_circle</span>
            <span class="text-xs sm:text-sm font-bold" id="successText"></span>
        </div>

        {{-- Cards Section --}}
        <div id="cardsSection" class="hidden space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kartu Siswa Ditemukan</div>
            </div>
            <div id="cardsList" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
        </div>
    </div>
</div>

{{-- ===== Modal Aksi Transaksi (jumlah + PIN) ===== --}}
<div id="actionModal" class="hidden fixed inset-0 bg-slate-950/60 dark:bg-black/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-fade-in">
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100 dark:border-slate-800">
        <div id="actionModalHeader" class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 id="actionModalTitle" class="text-base font-bold text-slate-900 dark:text-white">Transaksi</h3>
                <p id="actionModalCardInfo" class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Kartu: -</p>
            </div>
            <button type="button" onclick="closeActionModal()" class="p-2 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-full transition-all">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>

        <div class="p-6 space-y-5">
            {{-- Saldo Saat Ini --}}
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-800">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                <p id="currentBalanceDisplay" class="text-2xl font-black text-slate-900 dark:text-white">Rp 0</p>
            </div>

            <form id="actionForm" class="space-y-4">
                @csrf
                <input type="hidden" id="actionCardId" name="card_id">
                <input type="hidden" id="actionType" name="action_type">

                {{-- Merchant (hanya untuk kantin/pembayaran) --}}
                <div id="merchantField" class="space-y-2 hidden">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-1">Nama Merchant / Keperluan</label>
                    <input type="text" id="merchantInput" name="merchant_name" placeholder="Contoh: Kantin, SPP, Seragam"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
                </div>

                {{-- Jumlah --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-1">Jumlah (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 font-black text-sm">Rp</span>
                        <input type="number" id="amountInput" name="amount" min="1000" step="1000" required placeholder="0"
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white">
                    </div>
                    <p id="amountHint" class="text-[10px] text-slate-500 dark:text-slate-400">Minimal Rp 1.000</p>
                </div>

                {{-- Preview Saldo --}}
                <div id="balancePreview" class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-3 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between text-sm">
                        <span id="previewLabel" class="text-slate-600 dark:text-slate-400 font-medium">Saldo Baru</span>
                        <span id="previewValue" class="font-black text-slate-900 dark:text-white">Rp 0</span>
                    </div>
                </div>

                {{-- PIN --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-1">PIN Siswa (6 digit)</label>
                    <input type="password" id="pinInputModal" name="pin" maxlength="6" readonly placeholder="••••••"
                        class="w-full px-4 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl text-xl font-black text-center tracking-[1.5em] outline-none focus:border-orange-500 transition-all dark:text-white">
                </div>

                {{-- Keypad PIN --}}
                <div class="grid grid-cols-3 gap-2">
                    @for ($i = 1; $i <= 9; $i++)
                        <button type="button" onclick="pressKey('{{ $i }}')" class="py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-lg rounded-xl transition-all">{{ $i }}</button>
                    @endfor
                    <button type="button" onclick="pressKey('clear')" class="py-3 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/30 dark:text-rose-400 font-bold text-sm rounded-xl transition-all">C</button>
                    <button type="button" onclick="pressKey('0')" class="py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-lg rounded-xl transition-all">0</button>
                    <button type="button" onclick="pressKey('back')" class="py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-white font-bold flex items-center justify-center rounded-xl transition-all">
                        <span class="material-symbols-outlined text-sm">backspace</span>
                    </button>
                </div>

                <button type="submit" id="actionSubmitBtn" class="w-full mt-2 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold shadow-lg shadow-orange-100 dark:shadow-none transition-all uppercase tracking-wider text-xs flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span id="actionSubmitLabel">Proses Transaksi</span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Custom Alert --}}
<div id="customAlertModal" class="hidden fixed inset-0 bg-slate-950/60 dark:bg-black/80 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-xs w-full overflow-hidden border border-slate-100 dark:border-slate-800 p-6 text-center">
        <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl">warning</span>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Perhatian</h3>
        <p id="customAlertMessage" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6"></p>
        <button type="button" onclick="closeCustomAlert()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl font-bold text-xs transition-all uppercase tracking-wider">Mengerti</button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let scannerActive = false;
let currentCard = null;

// Abilities dari server
const canTopup = @json(auth()->user()->canTopupFinance());
const canWithdraw = @json(auth()->user()->canWithdrawFinance());
const canPayment = @json(auth()->user()->canProcessFinancePayment());

const ACTION_CONFIG = {
    topup: {
        title: 'Top Up Saldo',
        label: 'Top Up',
        icon: 'add_circle',
        headerClass: 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900',
        btnClass: 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-100 dark:shadow-none',
        tint: 'text-emerald-600',
        previewLabel: 'Saldo Setelah Top Up',
        endpoint: (id) => `/finance/cards/${id}/topup`,
        merchant: false,
        balanceOp: (bal, amt) => bal + amt
    },
    withdraw: {
        title: 'Tarik Tunai',
        label: 'Tarik Tunai',
        icon: 'point_of_sale',
        headerClass: 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900',
        btnClass: 'bg-amber-600 hover:bg-amber-700 shadow-amber-100 dark:shadow-none',
        tint: 'text-amber-600',
        previewLabel: 'Saldo Setelah Penarikan',
        endpoint: (id) => `/finance/cards/${id}/withdraw`,
        merchant: false,
        balanceOp: (bal, amt) => bal - amt
    },
    kantin: {
        title: 'Kantin / Pembayaran',
        label: 'Kantin',
        icon: 'restaurant',
        headerClass: 'bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900',
        btnClass: 'bg-rose-600 hover:bg-rose-700 shadow-rose-100 dark:shadow-none',
        tint: 'text-rose-600',
        previewLabel: 'Saldo Setelah Pembayaran',
        endpoint: (id) => `/finance/cards/${id}/payment`,
        merchant: true,
        balanceOp: (bal, amt) => bal - amt
    }
};

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

function pressKey(key) {
    const input = document.getElementById('pinInputModal');
    if (key === 'clear') input.value = '';
    else if (key === 'back') input.value = input.value.slice(0, -1);
    else if (input.value.length < 6) input.value += key;
}

function showCustomAlert(message) {
    document.getElementById('customAlertMessage').textContent = message;
    document.getElementById('customAlertModal').classList.remove('hidden');
}
function closeCustomAlert() {
    document.getElementById('customAlertModal').classList.add('hidden');
}

function showError(message) {
    document.getElementById('errorText').textContent = message;
    document.getElementById('errorMessage').classList.remove('hidden');
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('cardsSection').classList.add('hidden');
}
function showSuccess(message) {
    document.getElementById('successText').textContent = message;
    document.getElementById('successMessage').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('cardsSection').classList.add('hidden');
}

function toggleScanner() {
    scannerActive ? stopScanner() : startScanner();
}

async function startScanner() {
    try {
        document.getElementById('qrStatus').textContent = 'Meminta akses kamera...';
        document.getElementById('toggleScannerBtn').innerHTML = `<span class="inline-flex items-center justify-center gap-2"><span class="material-symbols-outlined text-lg">videocam_off</span> Matikan Kamera</span>`;
        document.getElementById('qrPlaceholder').classList.add('hidden');
        document.getElementById('qrVideoContainer').classList.remove('hidden');
        document.getElementById('scannerOverlay').classList.remove('hidden');

        html5QrCode = new Html5Qrcode("qrVideoContainer");
        const config = { fps: 15, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 };
        await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure);

        scannerActive = true;
        document.getElementById('qrStatus').textContent = 'Kamera Aktif: Tempatkan QR di dalam kotak';
    } catch (error) {
        console.error("Camera error:", error);
        showCustomAlert("Gagal memuat kamera. Pastikan izin kamera diizinkan oleh browser.");
        stopScanner();
    }
}

function stopScanner() {
    if (html5QrCode && scannerActive) {
        html5QrCode.stop().then(() => {}).catch(() => {});
    }
    scannerActive = false;
    document.getElementById('toggleScannerBtn').innerHTML = `<span class="inline-flex items-center justify-center gap-2"><span class="material-symbols-outlined text-lg">photo_camera</span> Mulai Ambil Scan</span>`;
    document.getElementById('qrStatus').textContent = 'Status: Kamera Nonaktif';
    document.getElementById('qrPlaceholder').classList.remove('hidden');
    document.getElementById('qrVideoContainer').classList.add('hidden');
    document.getElementById('scannerOverlay').classList.add('hidden');
    document.getElementById('qrVideoContainer').innerHTML = '';
}

function onScanSuccess(decodedText) {
    stopScanner();
    searchStudent(decodedText.trim());
}
function onScanFailure() {}

async function searchStudent(query) {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('cardsSection').classList.add('hidden');

    try {
        const response = await fetch(`{{ route('admin.finance.fast-transaction.search') }}?query=${encodeURIComponent(query)}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            displayCards(data.student, data.cards);
        } else {
            showError(data.message || 'Data siswa tidak terdaftar');
        }
    } catch (error) {
        showCustomAlert('Koneksi terputus: ' + error.message);
        console.error(error);
    } finally {
        document.getElementById('loadingState').classList.add('hidden');
    }
}

function displayCards(student, cards) {
    const cardsList = document.getElementById('cardsList');
    cardsList.innerHTML = '';

    cards.forEach(card => {
        const isActive = card.status === 'active';
        const el = document.createElement('div');
        el.className = `group relative rounded-3xl overflow-hidden border transition-all ${
            isActive
                ? 'bg-gradient-to-br from-slate-900 to-orange-950 text-white border-slate-800 shadow-xl'
                : 'bg-slate-100 dark:bg-slate-900 text-slate-400 border-slate-200 dark:border-slate-800 opacity-60'
        }`;

        const actionBtns = isActive ? `
            <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/10">
                ${canTopup ? `<button type="button" onclick="openActionModal('topup', ${card.id}, '${card.card_number}', ${card.balance}, '${student.nama_lengkap}')" class="py-2.5 bg-emerald-500/25 hover:bg-emerald-500/45 text-white rounded-xl font-bold text-xs flex flex-col items-center gap-1 transition-all border border-emerald-400/30"><span class="material-symbols-outlined text-base">add_circle</span><span>Topup</span></button>` : ''}
                ${canWithdraw ? `<button type="button" onclick="openActionModal('withdraw', ${card.id}, '${card.card_number}', ${card.balance}, '${student.nama_lengkap}')" class="py-2.5 bg-amber-500/25 hover:bg-amber-500/45 text-white rounded-xl font-bold text-xs flex flex-col items-center gap-1 transition-all border border-amber-400/30"><span class="material-symbols-outlined text-base">point_of_sale</span><span>Tarik</span></button>` : ''}
                ${canPayment ? `<button type="button" onclick="openActionModal('kantin', ${card.id}, '${card.card_number}', ${card.balance}, '${student.nama_lengkap}')" class="py-2.5 bg-rose-500/25 hover:bg-rose-500/45 text-white rounded-xl font-bold text-xs flex flex-col items-center gap-1 transition-all border border-rose-400/30"><span class="material-symbols-outlined text-base">restaurant</span><span>Kantin</span></button>` : ''}
            </div>
        ` : '';

        el.innerHTML = `
            <div class="p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Identitas Siswa</span>
                        <h4 class="text-base font-black tracking-wide ${isActive ? 'text-white' : 'text-slate-700 dark:text-slate-300'}">${student.nama_lengkap}</h4>
                        <p class="text-xs opacity-70">NIS: ${student.nis_siswa} &middot; ${student.angkatan || '-'}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider ${isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400'}">${isActive ? 'Aktif' : 'Nonaktif'}</span>
                </div>
                <div>
                    <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Nomor Kartu</span>
                    <p class="text-sm font-mono tracking-wider">${card.card_number}</p>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Sisa Saldo</span>
                        <p class="text-xl font-black ${isActive ? 'text-emerald-400' : 'text-slate-500'}">Rp ${formatNumber(card.balance)}</p>
                    </div>
                </div>
                ${actionBtns}
            </div>
        `;
        cardsList.appendChild(el);
    });

    document.getElementById('cardsSection').classList.remove('hidden');
}

function openActionModal(type, cardId, cardNumber, balance, studentName) {
    const cfg = ACTION_CONFIG[type];
    currentCard = { id: cardId, balance };

    document.getElementById('actionType').value = type;
    document.getElementById('actionCardId').value = cardId;
    document.getElementById('actionModalTitle').textContent = cfg.title;
    document.getElementById('actionModalCardInfo').textContent = `No: ${cardNumber} (${studentName})`;
    document.getElementById('currentBalanceDisplay').textContent = 'Rp ' + formatNumber(balance);
    document.getElementById('previewLabel').textContent = cfg.previewLabel;

    const header = document.getElementById('actionModalHeader');
    header.className = `p-6 border-b flex items-center justify-between ${cfg.headerClass}`;

    const submitBtn = document.getElementById('actionSubmitBtn');
    submitBtn.className = `w-full mt-2 py-4 text-white rounded-2xl font-bold shadow-lg transition-all uppercase tracking-wider text-xs flex items-center justify-center gap-2 ${cfg.btnClass}`;
    document.getElementById('actionSubmitLabel').textContent = 'Proses ' + cfg.label;

    document.getElementById('merchantField').classList.toggle('hidden', !cfg.merchant);
    document.getElementById('merchantInput').value = '';
    document.getElementById('amountInput').value = '';
    document.getElementById('pinInputModal').value = '';
    updatePreview();

    document.getElementById('actionModal').classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    currentCard = null;
}

function updatePreview() {
    if (!currentCard) return;
    const type = document.getElementById('actionType').value;
    const cfg = ACTION_CONFIG[type];
    const amount = parseInt(document.getElementById('amountInput').value) || 0;
    const newBalance = cfg.balanceOp(currentCard.balance, amount);
    const previewEl = document.getElementById('previewValue');
    previewEl.textContent = 'Rp ' + formatNumber(newBalance);
    previewEl.className = `font-black ${newBalance < 0 ? 'text-rose-600' : 'text-emerald-600 dark:text-emerald-400'}`;
}

document.getElementById('amountInput').addEventListener('input', updatePreview);

// Search form
document.getElementById('searchForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const query = document.getElementById('searchInput').value.trim();
    if (query) searchStudent(query);
});

// Submit action
document.getElementById('actionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const type = document.getElementById('actionType').value;
    const cfg = ACTION_CONFIG[type];
    const cardId = document.getElementById('actionCardId').value;
    const amount = document.getElementById('amountInput').value;
    const pin = document.getElementById('pinInputModal').value;
    const merchant = document.getElementById('merchantInput').value;

    if (pin.length < 6) {
        showCustomAlert('Digit PIN belum lengkap. Silakan masukkan 6 digit PIN siswa.');
        return;
    }

    const body = { amount, pin };
    if (cfg.merchant) {
        if (!merchant.trim()) {
            showCustomAlert('Nama merchant / keperluan harus diisi.');
            return;
        }
        body.merchant_name = merchant;
    }

    const submitBtn = document.getElementById('actionSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');

    try {
        const response = await fetch(cfg.endpoint(cardId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });
        const data = await response.json();
        if (data.success) {
            closeActionModal();
            showSuccess(`${cfg.label} berhasil diproses!`);
            document.getElementById('searchInput').value = '';
            document.getElementById('cardsSection').classList.add('hidden');
            setTimeout(() => {
                window.location.href = `/finance/transactions/${data.transaction_id}/receipt`;
            }, 1200);
        } else {
            showCustomAlert(data.message || 'Gagal memproses transaksi.');
        }
    } catch (error) {
        showCustomAlert('Terjadi masalah koneksi: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
});

document.getElementById('actionModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('actionModal')) closeActionModal();
});
document.getElementById('customAlertModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('customAlertModal')) closeCustomAlert();
});
</script>
<script>
(function () {
    const searchBox = document.querySelector('input[placeholder^="Search"], input[type="search"]');
    if (!searchBox) return;
    const bar = searchBox.closest('header, nav, [class*="sticky"], [class*="fixed"]');
    if (bar) bar.classList.add('page-hide-topbar-mobile');
})();
</script>
@endsection
