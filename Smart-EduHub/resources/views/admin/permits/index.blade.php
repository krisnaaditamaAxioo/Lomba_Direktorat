@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Persetujuan Izin Siswa',
        'eyebrow' => 'Perizinan',
        'subtitle' => 'Tinjau dan proses pengajuan izin/sakit dari siswa binaan.',
        'icon' => 'assignment_ind',
    ])

    <div class="flex justify-center">
        <button onclick="openCreateModal()" class="px-5 py-3 bg-orange-600 text-white rounded-2xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-orange-100 hover:bg-orange-700 transition-all">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Izin
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Izin</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Alasan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Bukti</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($permits as $permit)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($permit->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $permit->student->nama_lengkap }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 uppercase">Angkatan {{ $permit->student->angkatan ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-black uppercase tracking-wider">
                                {{ $permit->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $permit->start_date }}</p>
                            @if($permit->end_date)
                            <p class="text-[10px] text-slate-400 font-medium">s/d {{ $permit->end_date }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-500 font-medium line-clamp-2 max-w-xs">{{ $permit->reason }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($permit->proof_photo)
                            <a href="{{ asset('storage/'.$permit->proof_photo) }}" target="_blank" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors inline-block">
                                <span class="material-symbols-outlined text-[20px]">image</span>
                            </a>
                            @else
                            <span class="text-xs text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($permit->status === 'pending')
                                <form action="{{ route('admin.permits.status', $permit->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Setujui">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.permits.status', $permit->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Tolak">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                </form>
                                @else
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $permit->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $permit->status }}
                                </span>
                                @endif

                                <button onclick="openEditModal({{ json_encode($permit) }})" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                
                                <form action="{{ route('admin.permits.destroy', $permit->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data izin ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">assignment_late</span>
                                <p class="text-slate-400 font-medium">Belum ada pengajuan izin masuk.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-[2.5rem] w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Tambah Izin Baru</h3>
                    <button onclick="closeCreateModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('admin.permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Siswa</label>
                        <select name="student_id" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="">Pilih Siswa</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->nama_lengkap }} (Angkatan {{ $student->angkatan ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jenis Izin</label>
                        <select name="type" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="dispen">Dispensasi</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                            <input type="date" name="start_date" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alasan</label>
                        <textarea name="reason" rows="3" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Masukkan alasan..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Bukti (Opsional)</label>
                        <input type="file" name="proof_photo" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    </div>

                    <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-[2.5rem] w-full max-w-lg shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Edit Data Izin</h3>
                    <button onclick="closeEditModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Siswa</label>
                        <select name="student_id" id="edit_student_id" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jenis Izin</label>
                        <select name="type" id="edit_type" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="dispen">Dispensasi</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                            <input type="date" name="start_date" id="edit_start_date" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="edit_end_date" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alasan</label>
                        <textarea name="reason" id="edit_reason" rows="3" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none" placeholder="Masukkan alasan..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="edit_status" required class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ganti Bukti (Opsional)</label>
                        <input type="file" name="proof_photo" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    </div>

                    <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-100 hover:bg-orange-700 active:scale-[0.98] transition-all uppercase tracking-widest text-sm">Update Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(permit) {
        const form = document.getElementById('editForm');
        form.action = `{{ url('student-permits') }}/${permit.id}`;
        
        document.getElementById('edit_student_id').value = permit.student_id;
        document.getElementById('edit_type').value = permit.type;
        document.getElementById('edit_start_date').value = permit.start_date;
        document.getElementById('edit_end_date').value = permit.end_date || '';
        document.getElementById('edit_reason').value = permit.reason;
        document.getElementById('edit_status').value = permit.status;
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
