@extends('layouts.app')

@section('title', 'Hari Libur')

@section('content')
<div class="space-y-6">
    @include('admin.partials.page-header', [
        'title' => 'Hari Libur',
        'eyebrow' => 'Kalender Akademik',
        'subtitle' => 'Kelola hari libur untuk absensi siswa.',
        'icon' => 'beach_access',
    ])

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button onclick="document.getElementById('createRangeModal').classList.remove('hidden')" class="px-5 py-3 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center gap-2 hover:bg-teal-700 transition-all">
            <span class="material-symbols-outlined">date_range</span>
            Tambah Range Hari Libur
        </button>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-5 py-3 rounded-xl bg-orange-600 text-white font-bold flex items-center justify-center gap-2 hover:bg-orange-700 transition-all">
            <span class="material-symbols-outlined">add</span>
            Tambah Hari Libur
        </button>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Tanggal/Hari</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-400">Nama</th>
                        <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-widest text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($holidays as $holiday)
                    <tr>
                        <td class="px-6 py-4">
                            @if($holiday->type == 'special')
                                <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">Khusus</span>
                            @elseif($holiday->type == 'monthly')
                                <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">Bulanan</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-teal-100 text-teal-700 text-xs font-bold">Range</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-900 font-bold">
                            @if($holiday->type == 'special')
                                {{ \Carbon\Carbon::parse($holiday->date)->translatedFormat('d F Y') }}
                            @elseif($holiday->type == 'monthly')
                                Setiap tanggal {{ $holiday->day_of_month }}
                            @else
                                {{ \Carbon\Carbon::parse($holiday->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($holiday->end_date)->translatedFormat('d F Y') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $holiday->name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ $holiday }})" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Hapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada hari libur.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Tambah Hari Libur</h3>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Hari Libur</label>
                <select name="type" id="createType" onchange="toggleCreateFields()" class="w-full rounded-xl border border-slate-200 p-3" required>
                    <option value="special">Khusus (Tanggal tertentu)</option>
                    <option value="monthly">Bulanan (Setiap tanggal tertentu)</option>
                </select>
            </div>
            <div id="createDateDiv">
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="date" id="createDate" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div id="createDayDiv" class="hidden">
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Bulanan</label>
                <input type="number" name="day_of_month" id="createDayOfMonth" min="1" max="31" placeholder="Misal: 17" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Hari Libur</label>
                <input type="text" name="name" id="createName" required placeholder="Misal: Hari Raya Idul Fitri" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 text-white font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Edit Hari Libur</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Hari Libur</label>
                <select name="type" id="editType" onchange="toggleEditFields()" class="w-full rounded-xl border border-slate-200 p-3" required>
                    <option value="special">Khusus (Tanggal tertentu)</option>
                    <option value="monthly">Bulanan (Setiap tanggal tertentu)</option>
                    <option value="range">Range (Tanggal Awal - Akhir)</option>
                </select>
            </div>
            <div id="editDateDiv">
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="date" id="editDate" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div id="editDayDiv" class="hidden">
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Bulanan</label>
                <input type="number" name="day_of_month" id="editDayOfMonth" min="1" max="31" placeholder="Misal: 17" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div id="editRangeDiv" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Awal</label>
                    <input type="date" name="start_date" id="editStartDate" class="w-full rounded-xl border border-slate-200 p-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="editEndDate" class="w-full rounded-xl border border-slate-200 p-3">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Hari Libur</label>
                <input type="text" name="name" id="editName" required placeholder="Misal: Hari Raya Idul Fitri" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 text-white font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Range Modal -->
<div id="createRangeModal" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Tambah Range Hari Libur</h3>
            <button onclick="document.getElementById('createRangeModal').classList.add('hidden')" class="text-slate-400">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('holidays.range.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Awal</label>
                <input type="date" name="start_date" id="createStartDate" class="w-full rounded-xl border border-slate-200 p-3" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" id="createEndDate" class="w-full rounded-xl border border-slate-200 p-3" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Hari Libur</label>
                <input type="text" name="name" id="createRangeName" required placeholder="Misal: Libur Lebaran" class="w-full rounded-xl border border-slate-200 p-3">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createRangeModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 text-white font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCreateFields() {
    const type = document.getElementById('createType').value;
    const dateDiv = document.getElementById('createDateDiv');
    const dayDiv = document.getElementById('createDayDiv');
    
    if (type === 'special') {
        dateDiv.classList.remove('hidden');
        dayDiv.classList.add('hidden');
        document.getElementById('createDate').required = true;
        document.getElementById('createDayOfMonth').required = false;
    } else {
        dateDiv.classList.add('hidden');
        dayDiv.classList.remove('hidden');
        document.getElementById('createDate').required = false;
        document.getElementById('createDayOfMonth').required = true;
    }
}

function toggleEditFields() {
    const type = document.getElementById('editType').value;
    const dateDiv = document.getElementById('editDateDiv');
    const dayDiv = document.getElementById('editDayDiv');
    const rangeDiv = document.getElementById('editRangeDiv');
    
    // Hide all first
    dateDiv.classList.add('hidden');
    dayDiv.classList.add('hidden');
    rangeDiv.classList.add('hidden');
    
    // Show appropriate fields
    if (type === 'special') {
        dateDiv.classList.remove('hidden');
        document.getElementById('editDate').required = true;
        document.getElementById('editDayOfMonth').required = false;
        document.getElementById('editStartDate').required = false;
        document.getElementById('editEndDate').required = false;
    } else if (type === 'monthly') {
        dayDiv.classList.remove('hidden');
        document.getElementById('editDate').required = false;
        document.getElementById('editDayOfMonth').required = true;
        document.getElementById('editStartDate').required = false;
        document.getElementById('editEndDate').required = false;
    } else {
        rangeDiv.classList.remove('hidden');
        document.getElementById('editDate').required = false;
        document.getElementById('editDayOfMonth').required = false;
        document.getElementById('editStartDate').required = true;
        document.getElementById('editEndDate').required = true;
    }
}

function openEditModal(holiday) {
    document.getElementById('editType').value = holiday.type;
    document.getElementById('editDate').value = holiday.date;
    document.getElementById('editDayOfMonth').value = holiday.day_of_month;
    document.getElementById('editStartDate').value = holiday.start_date;
    document.getElementById('editEndDate').value = holiday.end_date;
    document.getElementById('editName').value = holiday.name;
    document.getElementById('editForm').action = '/holidays/' + holiday.id;
    
    toggleEditFields();
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection
