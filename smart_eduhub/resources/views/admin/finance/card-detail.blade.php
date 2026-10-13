@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Detail Kartu',
        'eyebrow' => 'Smart EduHub Wallet',
        'subtitle' => 'Kelola kartu dan transaksi siswa ini.',
        'icon' => 'credit_card',
    ])

    {{-- Aksi: kembali + export --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('admin.finance.cards') }}" class="p-2.5 text-slate-500 hover:bg-slate-100 rounded-xl transition-all flex items-center gap-2 font-bold text-sm">
            <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            Kembali
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-1">Export:</span>
            <a href="{{ route('admin.finance.cards.export.excel', $card->id) }}"
               title="Download Excel semua transaksi siswa ini"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                <span class="material-symbols-outlined text-base">table_view</span>
                Excel
            </a>
            <a href="{{ route('admin.finance.cards.export.pdf', $card->id) }}"
               title="Download PDF semua transaksi siswa ini"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black transition-all shadow-sm hover:shadow-md active:scale-95 uppercase tracking-wider">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                PDF
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Card Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-[#5D3EBC] via-[#4C2EAB] to-[#3B1E9A] rounded-[2rem] p-6 text-white shadow-lg shadow-orange-950/20">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">credit_card</span>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        {{ $card->status }}
                    </span>
                </div>

                <div class="mb-6">
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-1">Nomor Kartu</p>
                    <p class="text-lg font-mono font-bold tracking-wider">{{ $card->card_number }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-1">Saldo</p>
                    <p class="text-3xl font-black">Rp {{ number_format($card->balance, 0, ',', '.') }}</p>
                </div>

                <div class="border-t border-white/20 pt-4">
                    <p class="text-xs text-orange-200 uppercase tracking-widest mb-2">Pemilik Kartu</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr($card->student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold">{{ $card->student->nama_lengkap }}</p>
                            <p class="text-[10px] text-orange-200">Angkatan {{ $card->student->angkatan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->canTopupFinance())
            <!-- Top Up Card -->
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4">Top Up Saldo</h3>
                <form action="{{ route('admin.finance.cards.topup', $card->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah</label>
                        <input type="number" name="amount" min="1000" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Masukkan jumlah...">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Catatan..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Top Up</button>
                </form>
            </div>
            @endif

            @if(auth()->user()->canTopupFinance())
            <!-- Withdraw Card -->
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4">Tarik Tunai</h3>
                <form action="{{ route('admin.finance.cards.withdraw', $card->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah</label>
                        <input type="number" name="amount" min="1000" required max="{{ $card->balance }}" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Masukkan jumlah...">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Catatan..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Tarik Tunai</button>
                </form>
            </div>
            @endif

            @if(auth()->user()->canProcessFinancePayment())
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4">Pembayaran</h3>
                <form id="cardDetailPaymentForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="card_id" value="{{ $card->id }}">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Merchant / Keperluan</label>
                        <input type="text" name="merchant_name" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none" placeholder="Contoh: Kantin / SPP">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah</label>
                        <input type="number" name="amount" min="1000" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none" placeholder="Masukkan jumlah...">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-rose-600 text-white rounded-2xl font-black shadow-xl shadow-rose-100 hover:bg-rose-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Proses Pembayaran</button>
                </form>
            </div>
            @endif

            @if(auth()->user()->canUpdateFinanceCardStatus())
            <!-- Update Status Card -->
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4">Update Status Kartu</h3>
                <form action="{{ route('admin.finance.cards.status', $card->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="active" {{ $card->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="frozen" {{ $card->status === 'frozen' ? 'selected' : '' }}>Frozen</option>
                            <option value="inactive" {{ $card->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Update Status</button>
                </form>
            </div>
            @endif
        </div>

        <!-- Transaction History -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ auth()->user()->isBums() ? 'Riwayat Pembayaran' : 'Riwayat Transaksi' }}</h3>
                        @if(auth()->user()->isBums())
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Khusus Pembayaran</span>
                        @endif
                    </div>
                    @if(auth()->user()->canManageFinanceTransactions())
                    <a href="{{ route('admin.finance.transactions.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Kelola Transaksi
                    </a>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse($transactions as $transaction)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 
                                {{ $transaction->type === 'topup' ? 'bg-emerald-50 text-emerald-600' : 
                                   ($transaction->type === 'transfer_receive' ? 'bg-cyan-50 text-cyan-600' :
                                   ($transaction->type === 'transfer' ? 'bg-orange-50 text-orange-600' :
                                   ($transaction->type === 'withdraw' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600'))) }}">
                                <span class="material-symbols-outlined text-lg">
                                    {{ $transaction->type === 'topup' ? 'add_card' : 
                                       ($transaction->type === 'transfer_receive' ? 'call_received' :
                                       ($transaction->type === 'transfer' ? 'send' :
                                       ($transaction->type === 'withdraw' ? 'point_of_sale' : 'shopping_cart'))) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">
                                    @if($transaction->type === 'transfer_receive')
                                        Transfer Masuk
                                    @elseif($transaction->type === 'transfer')
                                        Transfer Keluar
                                    @elseif($transaction->type === 'withdraw')
                                        Tarik Tunai
                                    @elseif($transaction->type === 'topup')
                                        Topup
                                    @elseif($transaction->type === 'payment')
                                        Pembayaran
                                    @else
                                        {{ ucfirst($transaction->type) }}
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $transaction->created_at->format('d M Y H:i') }} • {{ Str::limit($transaction->description, 30) }}</p>
                                <p class="text-[10px] text-slate-400">Ref: {{ $transaction->reference_number ?: '-' }} • Petugas: {{ $transaction->performer_name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
                            @endphp
                            <p class="text-sm font-black {{ $isCredit ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $isCredit ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-slate-400">Saldo: Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</p>
                            <div class="mt-3 flex justify-end gap-2">
                                <a href="{{ route('admin.finance.transactions.receipt', $transaction) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 bg-orange-100 text-orange-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-200 transition-all" title="Lihat struk">
                                    <span class="material-symbols-outlined text-xs">receipt</span>
                                    Struk
                                </a>
                                @if(auth()->user()->canManageFinanceTransactions())
                                <a href="{{ route('admin.finance.transactions.edit', $transaction) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-100 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" title="Edit transaksi">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    Edit
                                </a>
                                @if($transaction->id === optional($transaction->card->transactions()->latest('id')->first())->id)
                                <form action="{{ route('admin.finance.transactions.destroy', $transaction) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    @php
                                        if ($transaction->type === 'topup') {
                                            $confirmMsg = "Hapus topup Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan berkurang Rp " . number_format($transaction->amount, 0, ',', '.') . ".";
                                        } elseif ($transaction->type === 'withdraw') {
                                            $confirmMsg = "Hapus tarik tunai Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan bertambah Rp " . number_format($transaction->amount, 0, ',', '.') . ".";
                                        } elseif ($transaction->type === 'transfer') {
                                            $confirmMsg = "Hapus transfer keluar Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo kartu ini akan bertambah dan transaksi pasangan di kartu tujuan juga akan dihapus.";
                                        } elseif ($transaction->type === 'transfer_receive') {
                                            $confirmMsg = "Hapus transfer masuk Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo kartu ini akan berkurang dan transaksi pasangan di kartu sumber juga akan dihapus.";
                                        } else {
                                            $confirmMsg = "Hapus transaksi Rp " . number_format($transaction->amount, 0, ',', '.') . "? Saldo akan disesuaikan kembali.";
                                        }
                                    @endphp
                                    <button type="submit" onclick="return confirm('{{ $confirmMsg }}')" class="inline-flex items-center gap-1 px-3 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all
                                        {{ $transaction->type === 'topup' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 
                                           ($transaction->type === 'withdraw' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 
                                           ($transaction->type === 'transfer_receive' ? 'bg-cyan-100 text-cyan-700 hover:bg-cyan-200' : 
                                           ($transaction->type === 'transfer' ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200'))) }}" title="Hapus transaksi terakhir ini">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                        Hapus
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="material-symbols-outlined text-2xl">receipt_long</span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-bold tracking-tight">Belum ada transaksi.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="cardDetailCustomAlertModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-rose-50 dark:from-rose-950/20 to-pink-50 dark:to-pink-950/20 border-b border-rose-100 dark:border-rose-900 p-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">error</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Perhatian</h3>
            </div>
            <button type="button" onclick="closeCardDetailCustomAlert()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p id="cardDetailCustomAlertMessage" class="text-sm text-slate-700 dark:text-slate-300 font-medium"></p>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 pt-0">
            <button type="button" onclick="closeCardDetailCustomAlert()" class="w-full py-4 bg-gradient-to-r from-rose-600 to-pink-700 text-white rounded-2xl font-black shadow-xl hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Modal PIN Verification -->
<div id="cardDetailPinModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-orange-50 dark:from-orange-950/20 to-purple-50 dark:to-purple-950/20 border-b border-orange-100 dark:border-orange-900 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Verifikasi PIN</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">Masukkan PIN kartu siswa</p>
            </div>
            <button type="button" onclick="closeCardDetailPinModal()" class="p-2 text-slate-500 hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all">
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
                    id="cardDetailPinInput"
                    maxlength="6"
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none text-center tracking-widest" 
                    placeholder="000000"
                >
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="submitCardDetailPaymentWithPin()" class="flex-1 py-4 bg-gradient-to-r from-orange-600 to-purple-700 text-white rounded-2xl font-black shadow-xl hover:shadow-2xl active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                    <span class="inline-flex items-center justify-center gap-2 w-full">
                        <span class="material-symbols-outlined">check_circle</span>
                        Verifikasi & Proses
                    </span>
                </button>
                <button type="button" onclick="closeCardDetailPinModal()" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-black hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let pendingCardDetailPaymentData = null;

// Custom Alert Functions
function showCardDetailCustomAlert(message) {
    document.getElementById('cardDetailCustomAlertMessage').textContent = message;
    document.getElementById('cardDetailCustomAlertModal').classList.remove('hidden');
}

function closeCardDetailCustomAlert() {
    document.getElementById('cardDetailCustomAlertModal').classList.add('hidden');
}

document.getElementById('cardDetailPaymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    pendingCardDetailPaymentData = {
        cardId: document.querySelector('#cardDetailPaymentForm input[name="card_id"]').value,
        amount: document.querySelector('#cardDetailPaymentForm input[name="amount"]').value,
        description: document.querySelector('#cardDetailPaymentForm textarea[name="description"]').value,
        merchantName: document.querySelector('#cardDetailPaymentForm input[name="merchant_name"]').value
    };
    openCardDetailPinModal();
});

// PIN Modal Functions
function openCardDetailPinModal() {
    document.getElementById('cardDetailPinModal').classList.remove('hidden');
    document.getElementById('cardDetailPinInput').value = '';
    document.getElementById('cardDetailPinInput').focus();
}

function closeCardDetailPinModal() {
    document.getElementById('cardDetailPinModal').classList.add('hidden');
    pendingCardDetailPaymentData = null;
}

async function submitCardDetailPaymentWithPin() {
    const pin = document.getElementById('cardDetailPinInput').value;
    if (!pin || pin.length < 4) {
        showCardDetailCustomAlert('Masukkan PIN yang valid');
        return;
    }

    try {
        const response = await fetch(`/finance/cards/${pendingCardDetailPaymentData.cardId}/payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                amount: pendingCardDetailPaymentData.amount,
                description: pendingCardDetailPaymentData.description,
                merchant_name: pendingCardDetailPaymentData.merchantName,
                pin: pin
            })
        });

        const data = await response.json();
        
        if (data.success) {
            closeCardDetailPinModal();
            window.location.href = `/finance/transactions/${data.transaction_id}/receipt`;
        } else {
            showCardDetailCustomAlert('Error: ' + (data.message || 'Gagal melakukan pembayaran'));
        }
    } catch (error) {
        showCardDetailCustomAlert('Terjadi kesalahan: ' + error.message);
        console.error(error);
    }
}

// Close modals when clicking outside
document.getElementById('cardDetailPinModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('cardDetailPinModal')) closeCardDetailPinModal();
});

document.getElementById('cardDetailCustomAlertModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('cardDetailCustomAlertModal')) closeCardDetailCustomAlert();
});
</script>
@endsection
