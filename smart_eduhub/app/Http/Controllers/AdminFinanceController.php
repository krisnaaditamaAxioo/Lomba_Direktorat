<?php

namespace App\Http\Controllers;

use App\Exports\FinanceReportExport;
use App\Exports\FinanceStudentExport;
use App\Models\Student;
use App\Models\StudentCard;
use App\Models\StudentCardRequest;
use App\Models\Setting;
use App\Models\StudentCardTransaction;
use App\Models\Angkatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AdminFinanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->canAccessFinanceDashboard()) {
            return redirect()->route('admin.finance.dashboard');
        }
        if ($user->canViewFinancePaymentHistory() && $user->isBums()) {
            return redirect()->route('admin.finance.payments.history');
        }
        return redirect()->route('admin.finance.cards');
    }

    public function transactionSearch()
    {
        $this->authorizeFinance('canTopupFinance');
        return view('admin.finance.transaction-search');
    }

    public function searchCardByNis(Request $request)
    {
        try {
            $this->authorizeFinance('canTopupFinance');
            $nis = $request->input('nis');
            if (!$nis) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIS harus diisi'
                ]);
            }
            $student = Student::with('angkatanModel')->where('nis_siswa', $nis)->orWhere('nisn', $nis)->first();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa dengan NIS ' . $nis . ' tidak ditemukan'
                ]);
            }
            $cards = StudentCard::where('student_id', $student->id)->get();
            if ($cards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu siswa belum dibuat. Silakan hubungi admin'
                ]);
            }
            $cardsData = $cards->map(function($card) {
                return [
                    'id' => $card->id,
                    'card_number' => $card->card_number,
                    'status' => $card->status,
                    'balance' => $card->balance
                ];
            });
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'nama_lengkap' => $student->nama_lengkap,
                    'nis_siswa' => $student->nis_siswa,
                    'nisn' => $student->nisn,
                    'angkatan' => $student->angkatanModel ? $student->angkatanModel->code : '-'
                ],
                'cards' => $cardsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard()
    {
        $this->authorizeFinance('canAccessFinanceDashboard');
        
        // Get date range from request or use default (last 30 days)
        $startDate = request()->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request()->input('end_date', now()->format('Y-m-d'));
        
        // Query for transactions in date range
        $transactionsQuery = StudentCardTransaction::whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        
        // Calculate totals
        $totalTopup = $transactionsQuery->clone()->where('type', 'topup')->sum('amount');
        $totalPayment = $transactionsQuery->clone()->where('type', 'payment')->sum('amount');
        $totalWithdraw = $transactionsQuery->clone()->where('type', 'withdraw')->sum('amount');
        $transactionCount = $transactionsQuery->count();
        
        // Get active cards and pending requests
        $activeCards = StudentCard::where('status', 'active')->count();
        $pendingRequests = StudentCardRequest::where('status', 'pending')->count();
        
        // Get recent transactions
        $recentTransactions = StudentCardTransaction::with(['card.student', 'performedBy'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->latest()
            ->limit(10)
            ->get();
        
        // Get daily recap
        $dailyRecap = StudentCardTransaction::select(
            DB::raw('DATE(created_at) as transaction_date'),
            DB::raw('SUM(CASE WHEN type = "topup" THEN amount ELSE 0 END) as total_topup'),
            DB::raw('SUM(CASE WHEN type = "payment" THEN amount ELSE 0 END) as total_payment'),
            DB::raw('COUNT(*) as total_transactions')
        )
        ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('transaction_date', 'desc')
        ->get();
        
        return view('admin.finance.dashboard', compact(
            'startDate', 'endDate', 'totalTopup', 'totalPayment', 'totalWithdraw', 'transactionCount',
            'activeCards', 'pendingRequests', 'recentTransactions', 'dailyRecap'
        ));
    }

    public function fastPayment()
    {
        $this->authorizeFinance('canProcessFinancePayment');
        $fastPaymentAmount = (int) Setting::get('fast_payment_amount', 5000);
        return view('admin.finance.fast-payment', compact('fastPaymentAmount'));
    }

    public function fastPaymentSearch(Request $request)
    {
        $this->authorizeFinance('canProcessFinancePayment');
        $query = $request->input('query');
        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Query pencarian harus diisi']);
        }
        $student = Student::where('nis_siswa', $query)->orWhere('nisn', $query)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        }
        $cards = StudentCard::where('student_id', $student->id)->get();
        return response()->json([
            'success' => true,
            'student' => $student,
            'cards' => $cards
        ]);
    }

    public function fastPaymentProcess(Request $request)
    {
        $this->authorizeFinance('canProcessFinancePayment');
        $validated = $request->validate([
            'card_id' => 'required|exists:student_cards,id',
            'pin' => 'required|string'
        ]);
        
        $card = StudentCard::findOrFail($validated['card_id']);
        $fastPaymentAmount = (int) Setting::get('fast_payment_amount', 5000);
        
        // Validate PIN (check if card has pin or use student's pin)
        $student = $card->student;
        if ($student->pin !== $validated['pin']) {
            return response()->json(['success' => false, 'message' => 'PIN salah']);
        }
        
        if ($card->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Kartu tidak aktif']);
        }
        if ($card->balance < $fastPaymentAmount) {
            return response()->json(['success' => false, 'message' => 'Saldo tidak mencukupi']);
        }
        DB::beginTransaction();
        try {
            $balanceBefore = $card->balance;
            $card->balance -= $fastPaymentAmount;
            $card->save();
            $transaction = StudentCardTransaction::create([
                'card_id' => $card->id,
                'type' => 'payment',
                'amount' => $fastPaymentAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $card->balance,
                'description' => 'Pembayaran cepat',
                'performed_by' => auth()->id()
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function rekap()
    {
        $this->authorizeFinance('canViewFinanceRekap');
        $from = request()->input('from', now()->subMonth()->format('Y-m-d'));
        $to = request()->input('to', now()->format('Y-m-d'));
        $angkatan = request()->input('angkatan');
        $studentId = request()->input('student_id');
        
        $query = StudentCardTransaction::with(['card.student.angkatanModel', 'performedBy'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        if ($angkatan) {
            $query->whereHas('card.student.angkatanModel', function($q) use ($angkatan) {
                $q->where('code', $angkatan);
            });
        }
        if ($studentId) {
            $query->whereHas('card', function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            });
        }
        
        $transactions = $query->latest()->paginate(50);
        
        $totalTopup = $query->clone()->where('type', 'topup')->sum('amount');
        $totalPayment = $query->clone()->where('type', 'payment')->sum('amount');
        $totalWithdraw = $query->clone()->where('type', 'withdraw')->sum('amount');
        $totalTransfer = $query->clone()->where('type', 'transfer')->sum('amount');
        $totalTransferReceive = $query->clone()->where('type', 'transfer_receive')->sum('amount');
        
        $grandTotal = [
            'topup' => $totalTopup,
            'payment' => $totalPayment,
            'transfer' => $totalTransfer,
            'transfer_receive' => $totalTransferReceive,
            'withdraw' => $totalWithdraw,
        ];
        
        $summary = collect([
            'topup' => (object)['count' => $query->clone()->where('type', 'topup')->count()],
            'payment' => (object)['count' => $query->clone()->where('type', 'payment')->count()],
            'transfer' => (object)['count' => $query->clone()->where('type', 'transfer')->count()],
            'transfer_receive' => (object)['count' => $query->clone()->where('type', 'transfer_receive')->count()],
            'withdraw' => (object)['count' => $query->clone()->where('type', 'withdraw')->count()],
        ]);
        
        $dailySummary = StudentCardTransaction::select(
            DB::raw('DATE(created_at) as transaction_date'),
            DB::raw('SUM(CASE WHEN type = "topup" THEN amount ELSE 0 END) as total_topup'),
            DB::raw('SUM(CASE WHEN type = "payment" THEN amount ELSE 0 END) as total_payment'),
            DB::raw('SUM(CASE WHEN type = "transfer" THEN amount ELSE 0 END) as total_transfer'),
            DB::raw('SUM(CASE WHEN type = "transfer_receive" THEN amount ELSE 0 END) as total_transfer_receive'),
            DB::raw('SUM(CASE WHEN type = "withdraw" THEN amount ELSE 0 END) as total_withdraw'),
            DB::raw('COUNT(*) as total_transactions')
        )
        ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
        ->when($angkatan, function($q) use ($angkatan) {
            $q->whereHas('card.student.angkatanModel', function($qq) use ($angkatan) {
                $qq->where('code', $angkatan);
            });
        })
        ->when($studentId, function($q) use ($studentId) {
            $q->whereHas('card', function($qq) use ($studentId) {
                $qq->where('student_id', $studentId);
            });
        })
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('transaction_date', 'desc')
        ->get();
        
        $paymentTransactions = $query->clone()->where('type', 'payment')->latest()->limit(10)->get();
        
        $students = Student::with('angkatanModel')->get();
        $angkatans = Angkatan::all();
        
        return view('admin.finance.rekap', compact(
            'transactions', 'from', 'to', 'angkatan', 'totalTopup', 'totalPayment', 'totalWithdraw', 'angkatans',
            'students', 'studentId', 'grandTotal', 'summary', 'dailySummary', 'paymentTransactions'
        ));
    }

    public function requests()
    {
        $this->authorizeFinance('canApproveFinanceRequests');
        $requests = StudentCardRequest::with('student')
            ->latest()
            ->paginate(20);
        return view('admin.finance.requests', compact('requests'));
    }

    public function createRequest()
    {
        $this->authorizeFinance('canApproveFinanceRequests');
        $students = Student::orderBy('nama_lengkap')->get();

        return view('admin.finance.request-create', compact('students'));
    }

    public function storeRequest(Request $request)
    {
        $this->authorizeFinance('canApproveFinanceRequests');

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'statement_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['statement_file'] = $request->hasFile('statement_file')
            ? $request->file('statement_file')->store('card-requests', 'public')
            : 'admin-submitted';

        StudentCardRequest::create($validated);

        return redirect()->route('admin.finance.requests')
            ->with('success', 'Pengajuan kartu untuk siswa berhasil dibuat.');
    }

    public function approveRequest($cardRequestId)
    {
        $this->authorizeFinance('canApproveFinanceRequests');
        $request = StudentCardRequest::findOrFail($cardRequestId);
        DB::beginTransaction();
        try {
            $card = StudentCard::create([
                'student_id' => $request->student_id,
                'card_number' => 'SC-' . strtoupper(uniqid()),
                'status' => 'active',
                'balance' => 0,
                'pin' => '123456' // Default PIN, should be changed by student
            ]);
            $request->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            DB::commit();
            return back()->with('success', 'Permintaan kartu disetujui dan kartu telah dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectRequest($cardRequestId)
    {
        $this->authorizeFinance('canApproveFinanceRequests');
        $request = StudentCardRequest::findOrFail($cardRequestId);
        $request->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now()
        ]);
        return back()->with('success', 'Permintaan kartu ditolak');
    }

    public function cards()
    {
        $this->authorizeFinance('canViewFinanceCards');
        $query = StudentCard::with('student.angkatanModel')->latest();
        $nama = request()->input('nama');
        $angkatan = request()->input('angkatan');
        $status = request()->input('status');
        if ($nama) {
            $query->whereHas('student', function($q) use ($nama) {
                $q->where('nama_lengkap', 'LIKE', '%' . $nama . '%');
            });
        }
        if ($angkatan) {
            $query->whereHas('student.angkatanModel', function($q) use ($angkatan) {
                $q->where('code', $angkatan);
            });
        }
        if ($status) {
            $query->where('status', $status);
        }
        $cards = $query->paginate(20);
        $angkatans = Angkatan::all();
        $angkatanList = $angkatans->pluck('code')->toArray();
        return view('admin.finance.cards', compact('cards', 'nama', 'angkatan', 'status', 'angkatans', 'angkatanList'));
    }

    public function showCard(StudentCard $card)
    {
        $this->authorizeFinance('canViewFinanceCards');
        $card->load(['student', 'transactions.performedBy']);
        $transactions = $card->transactions()->latest()->paginate(20);
        return view('admin.finance.card-detail', compact('card', 'transactions'));
    }

    public function topupCard(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canTopupFinance');
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255'
        ]);
        DB::beginTransaction();
        try {
            $balanceBefore = $card->balance;
            $card->balance += $validated['amount'];
            $card->save();
            $transaction = StudentCardTransaction::create([
                'card_id' => $card->id,
                'type' => 'topup',
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $card->balance,
                'description' => $validated['description'] ?? 'Topup oleh admin',
                'performed_by' => auth()->id()
            ]);
            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transaction->id
                ]);
            }
            return back()->with('success', 'Topup berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function withdrawCard(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canWithdrawFinance');
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000'
        ]);
        if ($card->balance < $validated['amount']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Saldo tidak mencukupi']);
            }
            return back()->with('error', 'Saldo tidak mencukupi');
        }
        DB::beginTransaction();
        try {
            $balanceBefore = $card->balance;
            $card->balance -= $validated['amount'];
            $card->save();
            $transaction = StudentCardTransaction::create([
                'card_id' => $card->id,
                'type' => 'withdraw',
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $card->balance,
                'description' => 'Penarikan oleh admin',
                'performed_by' => auth()->id()
            ]);
            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transaction->id
                ]);
            }
            return back()->with('success', 'Penarikan berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function payment(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canProcessFinancePayment');
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
            'merchant_name' => 'nullable|string|max:255',
            'pin' => 'required|string'
        ]);
        
        $student = $card->student;
        if ($student->pin !== $validated['pin']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'PIN salah']);
            }
            return back()->with('error', 'PIN salah');
        }
        
        if ($card->status !== 'active') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Kartu tidak aktif']);
            }
            return back()->with('error', 'Kartu tidak aktif');
        }
        if ($card->balance < $validated['amount']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Saldo tidak mencukupi']);
            }
            return back()->with('error', 'Saldo tidak mencukupi');
        }
        DB::beginTransaction();
        try {
            $balanceBefore = $card->balance;
            $card->balance -= $validated['amount'];
            $card->save();
            $transaction = StudentCardTransaction::create([
                'card_id' => $card->id,
                'type' => 'payment',
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $card->balance,
                'description' => $validated['merchant_name'] ?? $validated['description'] ?? 'Pembayaran',
                'performed_by' => auth()->id()
            ]);
            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'transaction_id' => $transaction->id
                ]);
            }
            return redirect()->route('admin.finance.transactions.receipt', $transaction->id)
                ->with('success', 'Pembayaran berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateCardStatus(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canManageFinanceCards');
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,frozen'
        ]);
        $card->update($validated);
        return back()->with('success', 'Status kartu diperbarui');
    }

    public function transactions()
    {
        $this->authorizeFinance('canViewFinanceTransactions');
        $query = StudentCardTransaction::with(['card.student', 'performedBy'])->latest();
        $type = request()->input('type');
        $studentName = request()->input('student_name');
        $cardNumber = request()->input('card_number');
        $date = request()->input('date');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($studentName) {
            $query->whereHas('card.student', function($q) use ($studentName) {
                $q->where('nama_lengkap', 'LIKE', '%' . $studentName . '%');
            });
        }
        
        if ($cardNumber) {
            $query->whereHas('card', function($q) use ($cardNumber) {
                $q->where('card_number', 'LIKE', '%' . $cardNumber . '%');
            });
        }
        
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        $transactions = $query->paginate(20);
        return view('admin.finance.transactions.index', compact('transactions', 'type', 'studentName', 'cardNumber', 'date'));
    }

    public function showReceipt(StudentCardTransaction $transaction)
    {
        $this->authorizeFinance('canViewFinanceTransactions');
        $transaction->load(['card.student', 'performedBy']);
        return view('admin.finance.receipt', compact('transaction'));
    }

    public function editTransaction(StudentCardTransaction $transaction)
    {
        $this->authorizeFinance('canEditFinanceTransactions');
        $transaction->load(['card.student']);
        return view('admin.finance.transactions.edit', compact('transaction'));
    }

    public function updateTransaction(Request $request, StudentCardTransaction $transaction)
    {
        $this->authorizeFinance('canEditFinanceTransactions');
        $validated = $request->validate([
            'description' => 'required|string|max:255'
        ]);
        $transaction->update($validated);
        return redirect()->route('admin.finance.transactions.index')
            ->with('success', 'Transaksi diperbarui');
    }

    public function destroyTransaction(StudentCardTransaction $transaction)
    {
        $this->authorizeFinance('canDeleteFinanceTransactions');
        
        DB::beginTransaction();
        try {
            $card = $transaction->card;
            
            if ($transaction->type === 'topup') {
                $card->balance -= $transaction->amount;
            } elseif (in_array($transaction->type, ['payment', 'withdraw', 'transfer'])) {
                $card->balance += $transaction->amount;
            } elseif ($transaction->type === 'transfer_receive') {
                $card->balance -= $transaction->amount;
            }
            
            $card->save();
            
            if (in_array($transaction->type, ['transfer', 'transfer_receive']) && $transaction->reference_number) {
                $pairedType = $transaction->type === 'transfer' ? 'transfer_receive' : 'transfer';
                $pairedTransaction = StudentCardTransaction::where('reference_number', $transaction->reference_number)
                    ->where('type', $pairedType)
                    ->where('id', '!=', $transaction->id)
                    ->first();
                
                if ($pairedTransaction) {
                    $pairedCard = $pairedTransaction->card;
                    if ($pairedType === 'transfer_receive') {
                        $pairedCard->balance -= $pairedTransaction->amount;
                    } else {
                        $pairedCard->balance += $pairedTransaction->amount;
                    }
                    $pairedCard->save();
                    $pairedTransaction->delete();
                }
            }
            
            $transaction->delete();
            
            DB::commit();
            return back()->with('success', 'Transaksi dihapus dan saldo diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function paymentHistory()
    {
        $this->authorizeFinance('canViewFinancePaymentHistory');
        $query = StudentCardTransaction::with(['card.student', 'performedBy'])
            ->where('type', 'payment')
            ->latest();
        $from = request()->input('from', now()->subMonth()->format('Y-m-d'));
        $to = request()->input('to', now()->format('Y-m-d'));
        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        $transactions = $query->paginate(20);
        return view('admin.finance.payment-history', compact('transactions', 'from', 'to'));
    }

    public function exportRekapExcel()
    {
        $this->authorizeFinance('canViewFinanceRekap');
        $from = request()->input('from');
        $to = request()->input('to');
        $angkatan = request()->input('angkatan');
        $studentId = request()->input('student_id');
        return Excel::download(new FinanceReportExport($from, $to, $angkatan, $studentId), 'rekap-keuangan.xlsx');
    }

    public function exportRekapPdf()
    {
        $this->authorizeFinance('canViewFinanceRekap');
        $from = request()->input('from');
        $to = request()->input('to');
        $angkatan = request()->input('angkatan');
        $studentId = request()->input('student_id');
        
        $filteredStudent = $studentId ? \App\Models\Student::find($studentId) : null;
        
        $query = StudentCardTransaction::with(['card.student.angkatanModel', 'performedBy'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        if ($angkatan) {
            $query->whereHas('card.student.angkatanModel', function($q) use ($angkatan) {
                $q->where('code', $angkatan);
            });
        }
        if ($studentId) {
            $query->whereHas('card', function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            });
        }
        $transactions = $query->latest()->get();
        $totalTopup = $query->clone()->where('type', 'topup')->sum('amount');
        $totalPayment = $query->clone()->where('type', 'payment')->sum('amount');
        $totalWithdraw = $query->clone()->where('type', 'withdraw')->sum('amount');
        $totalTransfer = $query->clone()->where('type', 'transfer')->sum('amount');
        
        $grandTotal = [
            'topup' => $totalTopup,
            'payment' => $totalPayment,
            'transfer' => $totalTransfer,
            'withdraw' => $totalWithdraw,
        ];
        
        $totalTransferReceive = $query->clone()->where('type', 'transfer_receive')->sum('amount');
        
        $summary = collect([
            'topup' => (object)['count' => $query->clone()->where('type', 'topup')->count()],
            'payment' => (object)['count' => $query->clone()->where('type', 'payment')->count()],
            'transfer' => (object)['count' => $query->clone()->where('type', 'transfer')->count()],
            'transfer_receive' => (object)['count' => $query->clone()->where('type', 'transfer_receive')->count()],
            'withdraw' => (object)['count' => $query->clone()->where('type', 'withdraw')->count()],
        ]);
        
        $grandTotal = [
            'topup' => $totalTopup,
            'payment' => $totalPayment,
            'transfer' => $totalTransfer,
            'transfer_receive' => $totalTransferReceive,
            'withdraw' => $totalWithdraw,
        ];
        
        $dailySummary = StudentCardTransaction::select(
            DB::raw('DATE(created_at) as transaction_date'),
            DB::raw('SUM(CASE WHEN type = "topup" THEN amount ELSE 0 END) as total_topup'),
            DB::raw('SUM(CASE WHEN type = "payment" THEN amount ELSE 0 END) as total_payment'),
            DB::raw('SUM(CASE WHEN type = "transfer" THEN amount ELSE 0 END) as total_transfer'),
            DB::raw('SUM(CASE WHEN type = "transfer_receive" THEN amount ELSE 0 END) as total_transfer_receive'),
            DB::raw('SUM(CASE WHEN type = "withdraw" THEN amount ELSE 0 END) as total_withdraw'),
            DB::raw('COUNT(*) as total_transactions')
        )
        ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
        ->when($angkatan, function($q) use ($angkatan) {
            $q->whereHas('card.student.angkatanModel', function($qq) use ($angkatan) {
                $qq->where('code', $angkatan);
            });
        })
        ->when($studentId, function($q) use ($studentId) {
            $q->whereHas('card', function($qq) use ($studentId) {
                $qq->where('student_id', $studentId);
            });
        })
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('transaction_date', 'desc')
        ->get();
        
        $perStudentSummary = StudentCardTransaction::with(['card.student'])
            ->whereBetween(DB::raw('DATE(student_card_transactions.created_at)'), [$from, $to])
            ->join('student_cards', 'student_card_transactions.card_id', '=', 'student_cards.id')
            ->join('students', 'student_cards.student_id', '=', 'students.id')
            ->when($angkatan, function($q) use ($angkatan) {
                $q->join('angkatans', 'students.angkatan_id', '=', 'angkatans.id')
                  ->where('angkatans.code', $angkatan);
            })
            ->when($studentId, function($q) use ($studentId) {
                $q->where('students.id', $studentId);
            })
            ->selectRaw('students.nama_lengkap, students.nis_siswa, student_cards.card_number, student_cards.balance as current_balance')
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'topup' THEN student_card_transactions.amount ELSE 0 END) as total_topup")
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'payment' THEN student_card_transactions.amount ELSE 0 END) as total_payment")
            ->selectRaw("SUM(CASE WHEN student_card_transactions.type = 'transfer' THEN student_card_transactions.amount ELSE 0 END) as total_transfer")
            ->selectRaw('COUNT(student_card_transactions.id) as total_trx')
            ->groupBy('students.nama_lengkap', 'students.nis_siswa', 'student_cards.card_number', 'student_cards.balance')
            ->orderBy('students.nama_lengkap')
            ->get();
            
        $pdf = Pdf::loadView('admin.finance.pdf.report-all', compact(
            'transactions', 'from', 'to', 'angkatan', 'grandTotal', 'summary', 'dailySummary', 'perStudentSummary', 'filteredStudent'
        ))->setPaper('a4', 'landscape');
        return $pdf->download('rekap-keuangan.pdf');
    }

    public function exportStudentExcel(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canViewFinanceCards');
        $from = $request->input('from');
        $to   = $request->input('to');
        $studentName = $card->load('student')->student->nama_lengkap ?? 'siswa';
        $safeName    = str_replace(' ', '-', strtolower($studentName));
        $filename    = 'keuangan-' . $safeName . ($from ? '-' . $from . '-sd-' . $to : '') . '.xlsx';
        return Excel::download(new FinanceStudentExport($card->id, $from, $to), $filename);
    }

    public function exportStudentPdf(Request $request, StudentCard $card)
    {
        $this->authorizeFinance('canViewFinanceCards');
        $from = $request->input('from');
        $to   = $request->input('to');
        $card->load(['student', 'transactions.performedBy']);
        $transQuery = $card->transactions()->with('performedBy');
        if ($from && $to) {
            $transQuery->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        }
        $transactions = $transQuery->latest()->get();
        $studentName = $card->student->nama_lengkap ?? 'siswa';
        $safeName    = str_replace(' ', '-', strtolower($studentName));
        $filename    = 'keuangan-' . $safeName . ($from ? '-' . $from . '-sd-' . $to : '') . '.pdf';
        $pdf = Pdf::loadView('admin.finance.pdf.report-student', compact('card', 'transactions', 'from', 'to'))
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }

    public function exportCardsPdf(Request $request)
    {
        $this->authorizeFinance('canViewFinanceCards');
        $query = StudentCard::with('student.angkatanModel')->latest();
        $nama = $request->input('nama');
        $angkatan = $request->input('angkatan');
        if ($nama) {
            $query->whereHas('student', function($q) use ($nama) {
                $q->where('nama_lengkap', 'LIKE', '%' . $nama . '%');
            });
        }
        if ($angkatan) {
            $query->whereHas('student.angkatanModel', function($q) use ($angkatan) {
                $q->where('code', $angkatan);
            });
        }
        $cards = $query->get();
        $filename = 'daftar-kartu-' . now()->format('Y-m-d') . '.pdf';
        $pdf = Pdf::loadView('admin.finance.pdf.report-cards', compact('cards', 'nama', 'angkatan'))
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }

    private function authorizeFinance(string $ability)
    {
        $user = auth()->user();
        if (!$user) {
            if (request()->expectsJson() || request()->ajax()) {
                abort(response()->json(['success' => false, 'message' => 'Anda harus login terlebih dahulu'], 401));
            }
            abort(401, 'Anda harus login terlebih dahulu');
        }
        if (!method_exists($user, $ability)) {
            if (request()->expectsJson() || request()->ajax()) {
                abort(response()->json(['success' => false, 'message' => 'Ability tidak ditemukan: ' . $ability], 403));
            }
            abort(403, 'Ability tidak ditemukan: ' . $ability);
        }
        if (!$user->{$ability}()) {
            if (request()->expectsJson() || request()->ajax()) {
                abort(response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk ' . $ability . '. Role Anda: ' . $user->role], 403));
            }
            abort(403, 'Anda tidak memiliki izin untuk ' . $ability . '. Role Anda: ' . $user->role);
        }
        return $user;
    }
}
