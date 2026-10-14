@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('admin.finance.dashboard') }}" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all w-fit">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Transaksi Keuangan</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">Cari NIS siswa untuk melakukan topup atau pembayaran kartu</p>
            </div>
        </div>
        <!-- Search Form -->
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-lg">
            <form id="nisSearchForm" class="space-y-6">
                @csrf
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NIS Siswa</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="nisInput" 
                            placeholder="Contoh: 12345" 
                            autocomplete="off"
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none dark:text-white"
                        >
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">search</span>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Masukkan NIS siswa yang akan melakukan transaksi</p>
                </div>
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-2xl font-black shadow-xl shadow-orange-200 dark:shadow-orange-950/50 hover:shadow-2xl hover:shadow-orange-300 dark:hover:shadow-orange-900/50 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                    <span class="inline-flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">search</span>
                        Cari Kartu Siswa
                    </span>
                </button>
            </form>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden">
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-8 text-center">
                <div class="inline-block">
                    <div class="w-8 h-8 border-3 border-orange-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Mencari kartu siswa...</p>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-6 py-4 rounded-2xl flex items-start gap-3">
            <span class="material-symbols-outlined flex-shrink-0 mt-0.5">error</span>
            <span class="text-sm font-bold" id="errorText"></span>
        </div>

        <!-- Cards List -->
        <div id="cardsSection" class="hidden space-y-4">
            <div class="text-sm font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">Pilih Kartu</div>
            <div id="cardsList" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
        </div>
    </div>
</div>

<!-- Modal Topup -->
<div id="topupModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-emerald-50 dark:from-emerald-950/20 to-teal-50 dark:to-teal-950/20 border-b border-emerald-100 dark:border-emerald-900 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Top Up Saldo</h3>
                <p id="modalCardInfoTopup" class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">Kartu: -</p>
            </div>
            <button type="button" onclick="closeTopupModal()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Current Balance -->
            <div class="bg-gradient-to-br from-emerald-50 dark:from-emerald-950/30 to-teal-50 dark:to-teal-950/30 rounded-2xl p-6 border border-emerald-100 dark:border-emerald-900">
                <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest mb-2">Saldo Saat Ini</p>
                <p id="currentBalanceTopup" class="text-3xl font-black text-emerald-900 dark:text-emerald-100">Rp 0</p>
            </div>

            <!-- Form -->
            <form id="topupSubmitForm" class="space-y-6">
                @csrf
                <input type="hidden" id="topupCardId" name="card_id">
                
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah Top Up</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 dark:text-slate-400 font-black text-sm">Rp</span>
                        <input 
                            type="number" 
                            name="amount" 
                            min="1000" 
                            step="1000"
                            required 
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none dark:text-white" 
                            placeholder="0"
                        >
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Minimal Rp 1.000</p>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan (Opsional)</label>
                    <textarea 
                        name="description" 
                        rows="3" 
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none resize-none dark:text-white" 
                        placeholder="Catatan topup..."
                    ></textarea>
                </div>

                <!-- New Balance Preview -->
                <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Saldo Baru</span>
                        <span id="newBalanceTopup" class="font-black text-emerald-600 dark:text-emerald-400">Rp 0</span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-2xl font-black shadow-xl shadow-emerald-200 dark:shadow-emerald-950/50 hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                        <span class="inline-flex items-center justify-center gap-2 w-full">
                            <span class="material-symbols-outlined">check_circle</span>
                            Proses Top Up
                        </span>
                    </button>
                    <button type="button" onclick="closeTopupModal()" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Payment -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-rose-50 dark:from-rose-950/20 to-pink-50 dark:to-pink-950/20 border-b border-rose-100 dark:border-rose-900 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Pembayaran</h3>
                <p id="modalCardInfoPayment" class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">Kartu: -</p>
            </div>
            <button type="button" onclick="closePaymentModal()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Current Balance -->
            <div class="bg-gradient-to-br from-rose-50 dark:from-rose-950/30 to-pink-50 dark:to-pink-950/30 rounded-2xl p-6 border border-rose-100 dark:border-rose-900">
                <p class="text-[10px] font-black text-rose-700 dark:text-rose-400 uppercase tracking-widest mb-2">Saldo Saat Ini</p>
                <p id="currentBalancePayment" class="text-3xl font-black text-rose-900 dark:text-rose-100">Rp 0</p>
            </div>

            <!-- Form -->
            <form id="paymentSubmitForm" class="space-y-6">
                @csrf
                <input type="hidden" id="paymentCardId" name="card_id">
                
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Merchant / Keperluan</label>
                    <input 
                        type="text" 
                        name="merchant_name" 
                        required
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none dark:text-white" 
                        placeholder="Contoh: Kantin / SPP / Seragam"
                    >
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Siapa atau apa yang menerima pembayaran ini</p>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 dark:text-slate-400 font-black text-sm">Rp</span>
                        <input 
                            type="number" 
                            name="amount" 
                            min="1000" 
                            step="1000"
                            required 
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none dark:text-white" 
                            placeholder="0"
                        >
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Minimal Rp 1.000</p>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan (Opsional)</label>
                    <textarea 
                        name="description" 
                        rows="3" 
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none resize-none dark:text-white" 
                        placeholder="Catatan pembayaran..."
                    ></textarea>
                </div>

                <!-- New Balance Preview -->
                <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Saldo Setelah Pembayaran</span>
                        <span id="newBalancePayment" class="font-black text-rose-600 dark:text-rose-400">Rp 0</span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 py-4 bg-gradient-to-r from-rose-600 to-rose-700 text-white rounded-2xl font-black shadow-xl shadow-rose-200 dark:shadow-rose-950/50 hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                        <span class="inline-flex items-center justify-center gap-2 w-full">
                            <span class="material-symbols-outlined">done_all</span>
                            Proses Pembayaran
                        </span>
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="customAlertModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-rose-50 dark:from-rose-950/20 to-pink-50 dark:to-pink-950/20 border-b border-rose-100 dark:border-rose-900 p-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">error</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Perhatian</h3>
            </div>
            <button type="button" onclick="closeCustomAlert()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p id="customAlertMessage" class="text-sm text-slate-700 dark:text-slate-300 font-medium"></p>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 pt-0">
            <button type="button" onclick="closeCustomAlert()" class="w-full py-4 bg-gradient-to-r from-rose-600 to-pink-700 text-white rounded-2xl font-black shadow-xl hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Modal PIN Verification -->
<div id="pinModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-orange-50 dark:from-orange-950/20 to-purple-50 dark:to-purple-950/20 border-b border-orange-100 dark:border-orange-900 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Verifikasi PIN</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">Masukkan PIN kartu siswa</p>
            </div>
            <button type="button" onclick="closePinModal()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- PIN Input -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">PIN Kartu</label>
                <input 
                    type="password" 
                    id="pinInput"
                    maxlength="6"
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none text-center tracking-widest" 
                    placeholder="000000"
                >
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="submitPaymentWithPin()" class="flex-1 py-4 bg-gradient-to-r from-orange-600 to-purple-700 text-white rounded-2xl font-black shadow-xl hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                    <span class="inline-flex items-center justify-center gap-2 w-full">
                        <span class="material-symbols-outlined">check_circle</span>
                        Verifikasi & Proses
                    </span>
                </button>
                <button type="button" onclick="closePinModal()" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let allCards = [];
let pendingPaymentData = null;
let topupSubmitting = false;

// Custom Alert Functions
function showCustomAlert(message) {
    document.getElementById('customAlertMessage').textContent = message;
    document.getElementById('customAlertModal').classList.remove('hidden');
}

function closeCustomAlert() {
    document.getElementById('customAlertModal').classList.add('hidden');
}

document.getElementById('nisSearchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const nis = document.getElementById('nisInput').value.trim();
    
    if (!nis) {
                showCustomAlert('Masukkan NIS siswa');
                return;
            }

    await searchCard(nis);
});

document.getElementById('nisInput').addEventListener('keypress', async (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('nisSearchForm').dispatchEvent(new Event('submit'));
    }
});

async function searchCard(nis) {
    const loadingState = document.getElementById('loadingState');
    const errorMessage = document.getElementById('errorMessage');
    const cardsSection = document.getElementById('cardsSection');

    loadingState.classList.remove('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
        cardsSection.classList.add('hidden');

        try {
            const response = await fetch(`{{ route('admin.finance.search-card') }}?nis=${encodeURIComponent(nis)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            loadingState.classList.add('hidden');

            if (!data.success) {
                showCustomAlert(data.message || 'Kartu tidak ditemukan');
                return;
            }

            if (!data.cards || !Array.isArray(data.cards) || data.cards.length === 0) {
                showCustomAlert('Tidak ada kartu yang ditemukan');
                return;
            }

            if (!data.student) {
                showCustomAlert('Data siswa tidak lengkap');
                return;
            }

            allCards = data.cards;
            displayCards(allCards, data.student);
            cardsSection.classList.remove('hidden');
        } catch (error) {
            loadingState.classList.add('hidden');
            showCustomAlert('Terjadi kesalahan saat mencari kartu: ' + error.message);
            console.error('Search card error:', error);
        }
}

const canTopup = @json(auth()->user()->canTopupFinance());
const canPayment = @json(auth()->user()->canProcessFinancePayment());

function displayCards(cards, student) {
    const cardsList = document.getElementById('cardsList');
    cardsList.innerHTML = '';

    cards.forEach((card, index) => {
        const cardEl = document.createElement('div');
        cardEl.className = 'bg-gradient-to-br from-[#5D3EBC] via-[#4C2EAB] to-[#3B1E9A] rounded-[2rem] p-6 text-white shadow-lg shadow-orange-950/20 hover:shadow-2xl hover:shadow-orange-950/40 transition-all';
        
        cardEl.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">credit_card</span>
                    </div>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        ${card.status}
                    </span>
                </div>

                <div>
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-1">Nomor Kartu</p>
                    <p class="text-lg font-mono font-bold tracking-wider">${card.card_number}</p>
                </div>

                <div>
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-1">Saldo</p>
                    <p class="text-2xl font-black">Rp ${formatNumber(card.balance)}</p>
                </div>

                <div class="border-t border-white/20 pt-4">
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-2">Pemilik Kartu</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs uppercase">
                            ${student.nama_lengkap.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <p class="text-sm font-bold">${student.nama_lengkap}</p>
                            <p class="text-[10px] text-orange-200">Angkatan ${student.angkatan || '-'}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    ${canTopup ? `
                        <button type="button" onclick="openTopupModal(${card.id}, '${card.card_number}', ${card.balance})" class="flex-1 py-3 px-4 bg-emerald-500/30 hover:bg-emerald-500/50 text-white rounded-xl font-bold active:scale-[0.98] transition-all text-sm flex items-center justify-center gap-2 border border-emerald-400/30">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                            <span class="hidden sm:inline">Top Up</span>
                        </button>
                    ` : ''}
                    ${canPayment ? `
                        <button type="button" onclick="openPaymentModal(${card.id}, '${card.card_number}', ${card.balance})" class="flex-1 py-3 px-4 bg-rose-500/30 hover:bg-rose-500/50 text-white rounded-xl font-bold active:scale-[0.98] transition-all text-sm flex items-center justify-center gap-2 border border-rose-400/30">
                            <span class="material-symbols-outlined text-lg">shopping_cart</span>
                            <span class="hidden sm:inline">Bayar</span>
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
        
        cardsList.appendChild(cardEl);
    });
}

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Topup Modal Functions
function openTopupModal(cardId, cardNumber, balance) {
    document.getElementById('topupCardId').value = cardId;
    document.getElementById('modalCardInfoTopup').textContent = `Kartu: ${cardNumber}`;
    document.getElementById('currentBalanceTopup').textContent = 'Rp ' + formatNumber(balance);
    document.getElementById('topupModal').classList.remove('hidden');
    document.getElementById('topupSubmitForm').reset();
    
    // Update preview saat input berubah
    document.querySelector('#topupSubmitForm input[name="amount"]').addEventListener('input', () => {
        updateTopupPreview(balance);
    });
}

function closeTopupModal() {
    document.getElementById('topupModal').classList.add('hidden');
}

function updateTopupPreview(currentBalance) {
    const amount = parseInt(document.querySelector('#topupSubmitForm input[name="amount"]').value) || 0;
    const newBalance = currentBalance + amount;
    document.getElementById('newBalanceTopup').textContent = 'Rp ' + formatNumber(newBalance);
}

// Payment Modal Functions
function openPaymentModal(cardId, cardNumber, balance) {
    document.getElementById('paymentCardId').value = cardId;
    document.getElementById('modalCardInfoPayment').textContent = `Kartu: ${cardNumber}`;
    document.getElementById('currentBalancePayment').textContent = 'Rp ' + formatNumber(balance);
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentSubmitForm').reset();
    
    // Update preview saat input berubah
    document.querySelector('#paymentSubmitForm input[name="amount"]').addEventListener('input', () => {
        updatePaymentPreview(balance);
    });
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function updatePaymentPreview(currentBalance) {
    const amount = parseInt(document.querySelector('#paymentSubmitForm input[name="amount"]').value) || 0;
    const newBalance = currentBalance - amount;
    document.getElementById('newBalancePayment').textContent = 'Rp ' + formatNumber(newBalance);
}

// Topup Submit
document.getElementById('topupSubmitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (topupSubmitting) {
        return;
    }

    topupSubmitting = true;
    const submitButton = e.currentTarget.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.classList.add('opacity-60', 'cursor-not-allowed');

    const cardId = document.getElementById('topupCardId').value;
    const amount = document.querySelector('#topupSubmitForm input[name="amount"]').value;
    const description = document.querySelector('#topupSubmitForm textarea[name="description"]').value;

    try {
            const response = await fetch(`/finance/cards/${cardId}/topup`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ amount, description })
            });

            const data = await response.json();
            
            if (data.success) {
                closeTopupModal();
                window.location.href = `/finance/transactions/${data.transaction_id}/receipt`;
            } else {
                topupSubmitting = false;
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
                showCustomAlert('Error: ' + (data.message || 'Gagal melakukan topup'));
            }
        } catch (error) {
            topupSubmitting = false;
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
            showCustomAlert('Terjadi kesalahan: ' + error.message);
            console.error(error);
        }
});

// Payment Submit
document.getElementById('paymentSubmitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    pendingPaymentData = {
        cardId: document.getElementById('paymentCardId').value,
        amount: document.querySelector('#paymentSubmitForm input[name="amount"]').value,
        description: document.querySelector('#paymentSubmitForm textarea[name="description"]').value,
        merchantName: document.querySelector('#paymentSubmitForm input[name="merchant_name"]').value
    };
    openPinModal();
});

// PIN Modal Functions
function openPinModal() {
    document.getElementById('pinModal').classList.remove('hidden');
    document.getElementById('pinInput').value = '';
    document.getElementById('pinInput').focus();
}

function closePinModal() {
    document.getElementById('pinModal').classList.add('hidden');
    pendingPaymentData = null;
}

async function submitPaymentWithPin() {
    const pin = document.getElementById('pinInput').value;
    if (!pin || pin.length < 4) {
        showCustomAlert('Masukkan PIN yang valid');
        return;
    }

    try {
        const response = await fetch(`/finance/cards/${pendingPaymentData.cardId}/payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                amount: pendingPaymentData.amount,
                description: pendingPaymentData.description,
                merchant_name: pendingPaymentData.merchantName,
                pin: pin
            })
        });

        const data = await response.json();
        
        if (data.success) {
            closePinModal();
            closePaymentModal();
            window.location.href = `/finance/transactions/${data.transaction_id}/receipt`;
        } else {
            showCustomAlert('Error: ' + (data.message || 'Gagal melakukan pembayaran'));
        }
    } catch (error) {
        showCustomAlert('Terjadi kesalahan: ' + error.message);
        console.error(error);
    }
}

// Close modals when clicking outside
document.getElementById('topupModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('topupModal')) closeTopupModal();
});

document.getElementById('paymentModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('paymentModal')) closePaymentModal();
});

document.getElementById('pinModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('pinModal')) closePinModal();
});

document.getElementById('customAlertModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('customAlertModal')) closeCustomAlert();
});
</script>
@endsection
