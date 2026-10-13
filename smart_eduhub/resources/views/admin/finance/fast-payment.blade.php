@extends('layouts.app')

@section('content')
<style>
    /* Styling khusus untuk memantapkan video scanner */
    #qrVideoContainer video {
        width: 100% !important;
        height: 340px !important;
        object-fit: cover !important;
        border-radius: 1.5rem !important;
    }
    #qrVideoContainer {
        position: relative !important;
    }
    #qrVideoContainer div[role="button"] {
        display: none !important;
    }
    
    /* Animasi garis laser scan */
    @keyframes scan {
        0% { transform: translateY(0); }
        50% { transform: translateY(220px); }
        100% { transform: translateY(0); }
    }
    .scanner-laser {
        position: absolute;
        inset-x: 0;
        height: 3px;
        background: linear-gradient(to right, transparent, #ef4444, transparent);
        animation: scan 2.5s linear infinite;
        z-index: 10;
    }

    /* Sembunyikan navbar atas (logo + search) di mobile (< 768px) */
    @media (max-width: 767px) {
        .page-hide-topbar-mobile,
        body > header,
        #app > header,
        header.sticky,
        header.fixed,
        #topNavbar,
        .top-navbar,
        [data-topbar] {
            display: none !important;
        }
    }

    /* Header menempel ke ujung atas layar di mobile */
    @media (max-width: 767px) {
        main, #app, .main-content, body > div {
            padding-top: 0 !important;
        }
    }
</style>

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pt-0 pb-4 sm:py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6 sm:space-y-8">
        
        {{-- ===== HEADER (hero) ===== --}}
        <div class="relative overflow-hidden -mx-4 mt-0 sm:mx-0 rounded-b-[2.5rem] sm:rounded-3xl bg-gradient-to-br from-[#f98a3c] via-[#ea4a2a] to-[#8f1d1d] px-5 pt-5 pb-8 sm:px-8 sm:py-8 shadow-lg shadow-orange-500/20">
            {{-- Lingkaran dekoratif --}}
            <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -left-14 bottom-0 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-10 right-16 h-32 w-32 rounded-full bg-white/5"></div>

            <div class="relative">
                {{-- Baris atas: kembali + tarif --}}
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.finance.dashboard') }}"
                       aria-label="Kembali"
                       class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white/20 text-white ring-1 ring-white/25 backdrop-blur transition hover:bg-white/30 active:scale-95">
                        <span class="material-symbols-outlined text-[22px]">arrow_back</span>
                    </a>

                    <div class="flex items-center gap-2 rounded-full bg-white px-3.5 py-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[18px] text-orange-500">payments</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tarif</span>
                        <span class="text-xs font-extrabold text-slate-900 sm:text-sm">Rp {{ number_format($fastPaymentAmount, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Judul tengah --}}
                <div class="mt-5 text-center sm:mt-6">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-orange-100/90 sm:text-xs">Kasir Cepat</p>
                    <h1 class="mt-1 text-3xl font-black tracking-tight text-white sm:text-4xl">Pembayaran Cepat</h1>
                    <p class="mx-auto mt-2 max-w-md text-xs font-medium text-orange-100/90 sm:text-sm">
                        Scan QR atau masukkan NIS untuk penarikan instan (Parkir, Kantin, dll)
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
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

            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-600">search</span>
                        Pencarian Manual
                    </h3>
                    
                    <form id="searchForm" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">NIS / ID No. Kartu</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    placeholder="Contoh: 202410023" 
                                    autocomplete="off"
                                    class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white"
                                >
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
                        Jika QR Code pada kartu rusak/kotor, silakan ketikkan Nomor Induk Siswa secara manual pada form di atas.
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

        <div id="cardsSection" class="hidden space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kartu Terkait Ditemukan</div>
                <div class="text-xs font-medium text-slate-500">Pilih kartu aktif untuk eksekusi</div>
            </div>
            <div id="cardsList" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
        </div>
    </div>
</div>

<div id="pinModal" class="hidden fixed inset-0 bg-slate-950/60 dark:bg-black/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-fade-in">
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100 dark:border-slate-800">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Autentikasi PIN</h3>
                <p id="modalCardInfoPin" class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Kartu: -</p>
            </div>
            <button type="button" onclick="closePinModal()" class="p-2 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-full transition-all">
                <span class="material-symbols-outlined block">close</span>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Tagihan</p>
                <p id="paymentAmountPin" class="text-3xl font-black text-slate-900 dark:text-white">Rp {{ number_format($fastPaymentAmount, 0, ',', '.') }}</p>
            </div>

            <form id="pinSubmitForm" class="space-y-4">
                @csrf
                <input type="hidden" id="paymentCardId" name="card_id">
                
                <div class="space-y-2 text-center">
                    <input 
                        type="password" 
                        name="pin" 
                        id="pinInputModal"
                        maxlength="6"
                        readonly
                        class="w-full px-4 py-4 bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl text-xl font-black text-center tracking-[1.5em] outline-none focus:border-orange-500 transition-all dark:text-white" 
                        placeholder="••••••"
                    >
                </div>

                <div class="grid grid-cols-3 gap-2 pt-2">
                    @for ($i = 1; $i <= 9; $i++)
                        <button type="button" onclick="pressKey('{{ $i }}')" class="py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-lg rounded-xl transition-all">{{ $i }}</button>
                    @endfor
                    <button type="button" onclick="pressKey('clear')" class="py-3 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/30 dark:text-rose-400 font-bold text-sm rounded-xl transition-all">C</button>
                    <button type="button" onclick="pressKey('0')" class="py-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-lg rounded-xl transition-all">0</button>
                    <button type="button" onclick="pressKey('back')" class="py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-white font-bold flex items-center justify-center rounded-xl transition-all">
                        <span class="material-symbols-outlined text-sm">backspace</span>
                    </button>
                </div>

                <button type="submit" class="w-full mt-2 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold shadow-lg shadow-orange-100 dark:shadow-none transition-all uppercase tracking-wider text-xs flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Proses Transaksi
                </button>
            </form>
        </div>
    </div>
</div>

<div id="customAlertModal" class="hidden fixed inset-0 bg-slate-950/60 dark:bg-black/80 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-xs w-full overflow-hidden border border-slate-100 dark:border-slate-800 p-6 text-center">
        <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl">warning</span>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Terjadi Kendala</h3>
        <p id="customAlertMessage" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6"></p>
        <button type="button" onclick="closeCustomAlert()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl font-bold text-xs transition-all uppercase tracking-wider">
            Mengerti
        </button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const FAST_PAYMENT_AMOUNT = {{ $fastPaymentAmount }};
let html5QrCode = null;
let scannerActive = false;

// Fungsi Virtual Keypad PIN
function pressKey(key) {
    const pinInput = document.getElementById('pinInputModal');
    if (key === 'clear') {
        pinInput.value = '';
    } else if (key === 'back') {
        pinInput.value = pinInput.value.slice(0, -1);
    } else {
        if (pinInput.value.length < 6) {
            pinInput.value += key;
        }
    }
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
    if (scannerActive) {
        stopScanner();
    } else {
        startScanner();
    }
}

async function startScanner() {
    try {
        document.getElementById('qrStatus').textContent = 'Meminta akses kamera...';
        document.getElementById('toggleScannerBtn').innerHTML = `
            <span class="inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">videocam_off</span>
                Matikan Kamera
            </span>
        `;
        
        document.getElementById('qrPlaceholder').classList.add('hidden');
        document.getElementById('qrVideoContainer').classList.remove('hidden');
        document.getElementById('scannerOverlay').classList.remove('hidden');
        
        html5QrCode = new Html5Qrcode("qrVideoContainer");
        
        const config = {
            fps: 15,
            qrbox: { width: 220, height: 220 },
            aspectRatio: 1.0
        };
        
        await html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        );
        
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
        html5QrCode.stop().then(() => {
            console.log("Scanner stopped");
        }).catch((error) => {
            console.error("Error stopping scanner:", error);
        });
    }
    
    scannerActive = false;
    document.getElementById('toggleScannerBtn').innerHTML = `
        <span class="inline-flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">photo_camera</span>
            Mulai Ambil Scan
        </span>
    `;
    document.getElementById('qrStatus').textContent = 'Status: Kamera Nonaktif';
    
    document.getElementById('qrPlaceholder').classList.remove('hidden');
    document.getElementById('qrVideoContainer').classList.add('hidden');
    document.getElementById('scannerOverlay').classList.add('hidden');
    document.getElementById('qrVideoContainer').innerHTML = '';
}

function onScanSuccess(decodedText, decodedResult) {
    stopScanner();
    searchStudent(decodedText.trim());
}

function onScanFailure(error) {
    // Dipanggil berkala saat scanning gagal membaca QR, dibiarkan kosong agar tidak spam log.
}

async function searchStudent(query) {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('cardsSection').classList.add('hidden');

    try {
        const response = await fetch(`{{ route('admin.finance.fast-payment.search') }}?query=${encodeURIComponent(query)}`, {
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
    
    const activeCards = cards.filter(card => card.status === 'active');
    
    cards.forEach(card => {
        const isActive = card.status === 'active';
        
        const cardElement = document.createElement('div');
        cardElement.className = `group relative rounded-3xl overflow-hidden border transition-all ${
            isActive 
            ? 'bg-gradient-to-br from-slate-900 to-orange-950 text-white border-slate-800 shadow-xl cursor-pointer hover:-translate-y-1' 
            : 'bg-slate-100 dark:bg-slate-900 text-slate-400 border-slate-200 dark:border-slate-800 opacity-60'
        }`;
        
        cardElement.innerHTML = `
            <div class="p-6 space-y-6">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Identitas Siswa</span>
                        <h4 class="text-base font-black tracking-wide ${isActive ? 'text-white' : 'text-slate-700 dark:text-slate-300'}">${student.nama_lengkap}</h4>
                        <p class="text-xs opacity-70">${student.nis_siswa}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider ${
                        isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                    }">
                        ${isActive ? 'Aktif' : 'Nonaktif'}
                    </span>
                </div>

                <div>
                    <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Nomor RFID / Kartu</span>
                    <p class="text-sm font-mono tracking-wider">${card.card_number}</p>
                </div>

                <div class="pt-4 border-t ${isActive ? 'border-white/10' : 'border-slate-200 dark:border-slate-800'} flex items-end justify-between">
                    <div>
                        <span class="text-[9px] font-bold tracking-widest uppercase opacity-60 block">Sisa Saldo</span>
                        <p class="text-xl font-black ${isActive ? 'text-emerald-400' : 'text-slate-500'}">Rp ${new Intl.NumberFormat('id-ID').format(card.balance)}</p>
                    </div>
                    ${isActive ? `
                        <div class="p-2 bg-white/10 group-hover:bg-orange-600 text-white rounded-xl transition-all">
                            <span class="material-symbols-outlined block text-base">arrow_forward</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        if (isActive) {
            cardElement.addEventListener('click', () => checkBalanceAndOpenModal(card.id, card.card_number, student.nama_lengkap, card.balance));
        }
        cardsList.appendChild(cardElement);
    });

    document.getElementById('cardsSection').classList.remove('hidden');
    
    // Auto open modal if exactly one active card
    if (activeCards.length === 1) {
        const card = activeCards[0];
        checkBalanceAndOpenModal(card.id, card.card_number, student.nama_lengkap, card.balance);
    }
}

function checkBalanceAndOpenModal(cardId, cardNumber, studentName, balance) {
    if (balance < FAST_PAYMENT_AMOUNT) {
        showCustomAlert(`Saldo tidak cukup! Saldo saat ini: Rp ${new Intl.NumberFormat('id-ID').format(balance)}, diperlukan: Rp ${new Intl.NumberFormat('id-ID').format(FAST_PAYMENT_AMOUNT)}`);
        return;
    }
    
    // Open PIN modal if balance is sufficient
    openPinModal(cardId, cardNumber, studentName);
}

function openPinModal(cardId, cardNumber, studentName) {
    document.getElementById('paymentCardId').value = cardId;
    document.getElementById('modalCardInfoPin').textContent = `No: ${cardNumber} (${studentName})`;
    document.getElementById('pinModal').classList.remove('hidden');
    document.getElementById('pinInputModal').value = '';
}

function closePinModal() {
    document.getElementById('pinModal').classList.add('hidden');
}

document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const query = document.getElementById('searchInput').value.trim();
    if (query) {
        searchStudent(query);
    }
});

document.getElementById('pinSubmitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const cardId = document.getElementById('paymentCardId').value;
    const pin = document.getElementById('pinInputModal').value;

    if(pin.length < 6) {
        showCustomAlert('Digit PIN belum lengkap. Silakan masukkan 6 digit PIN kartu.');
        return;
    }

    try {
        const response = await fetch('{{ route('admin.finance.fast-payment.process') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ card_id: cardId, pin: pin })
        });

        const data = await response.json();
        
        if (data.success) {
            closePinModal();
            showSuccess('Pembayaran Berhasil diproses!');
            document.getElementById('searchInput').value = '';
            document.getElementById('cardsSection').classList.add('hidden');
            setTimeout(() => {
                window.location.href = `/finance/transactions/${data.transaction_id}/receipt`;
            }, 1200);
        } else {
            showCustomAlert(data.message || 'Otorisasi PIN salah atau gagal transaksi.');
        }
    } catch (error) {
        showCustomAlert('Terjadi masalah koneksi server: ' + error.message);
    }
});

document.getElementById('pinModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('pinModal')) closePinModal();
});

document.getElementById('customAlertModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('customAlertModal')) closeCustomAlert();
});
</script>
<script>
// Tandai navbar atas (yang berisi kolom Search) supaya disembunyikan oleh CSS di mobile
(function () {
    const searchBox = document.querySelector('input[placeholder^="Search"], input[type="search"]');
    if (!searchBox) return;
    const bar = searchBox.closest('header, nav, [class*="sticky"], [class*="fixed"]');
    if (bar) bar.classList.add('page-hide-topbar-mobile');
})();
</script>
@endsection