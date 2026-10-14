@extends('layouts.app')

@section('title', 'Hari Libur')

@section('content')
@php
    $inputClass = 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 placeholder:font-normal focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 outline-none transition';
    $labelClass = 'block text-xs font-black uppercase tracking-wider text-slate-500 mb-2';
    $typeBadge = [
        'special' => ['Khusus',  'bg-orange-100 text-orange-700', 'event'],
        'monthly' => ['Bulanan', 'bg-purple-100 text-purple-700', 'event_repeat'],
        'range'   => ['Range',   'bg-teal-100 text-teal-700',     'date_range'],
    ];
    $fmt = fn ($d) => \Carbon\Carbon::parse($d)->translatedFormat('d F Y');
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ===== HEADER: menempel ke atas & samping di mobile, tanpa celah ===== --}}
    <header class="relative overflow-hidden -mx-4 -mt-4 sm:-mx-6 sm:-mt-6 md:mx-0 md:mt-0
                   rounded-b-[2rem] md:rounded-3xl
                   bg-gradient-to-br from-orange-500 via-orange-600 to-red-700
                   text-white px-5 pt-5 pb-8 md:px-10 md:py-9 shadow-xl shadow-orange-600/25">
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute bottom-0 right-1/4 w-32 h-32 rounded-full bg-black/10"></div>

        <div class="relative">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <span class="font-black text-lg tracking-tight">SEH</span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-slate-800 text-sm font-bold shadow-md">
                    <span class="material-symbols-outlined text-[18px] text-orange-500">beach_access</span>
                    {{ count($holidays) }} Libur
                </span>
            </div>

            <div class="text-center mt-6 md:mt-8">
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-orange-200">Kalender Akademik</p>
                <h2 class="mt-1 text-3xl md:text-4xl font-black tracking-tight leading-tight">Hari Libur</h2>
                <p class="mt-2 text-sm md:text-base text-orange-100">Kelola hari libur untuk absensi siswa.</p>
            </div>
        </div>
    </header>

    {{-- ===== AKSI ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:flex md:justify-end gap-3">
        <button onclick="openModal('createRangeModal')"
                class="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 hover:shadow-teal-500/45 hover:-translate-y-0.5 active:scale-95 transition-all">
            <span class="material-symbols-outlined">date_range</span>
            Tambah Range Hari Libur
        </button>
        <button onclick="openModal('createModal')"
                class="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold flex items-center justify-center gap-2 shadow-lg shadow-orange-500/25 hover:shadow-orange-500/45 hover:-translate-y-0.5 active:scale-95 transition-all">
            <span class="material-symbols-outlined">add</span>
            Tambah Hari Libur
        </button>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="flex gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl">
            <span class="material-symbols-outlined text-[20px] mt-0.5">error</span>
            <ul class="text-sm space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ===== MOBILE: kartu ===== --}}
    <div class="grid gap-3 md:hidden">
        @forelse($holidays as $holiday)
            @php
                $badge = $typeBadge[$holiday->type] ?? $typeBadge['range'];
                $when = $holiday->type == 'special' ? $fmt($holiday->date)
                      : ($holiday->type == 'monthly' ? 'Setiap tanggal ' . $holiday->day_of_month
                      : $fmt($holiday->start_date) . ' - ' . $fmt($holiday->end_date));
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center {{ $badge[1] }}">
                        <span class="material-symbols-outlined">{{ $badge[2] }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                        <h3 class="mt-1 font-bold text-slate-900 leading-snug break-words">{{ $holiday->name }}</h3>
                        <p class="mt-0.5 text-sm text-slate-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">calendar_month</span>{{ $when }}
                        </p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button onclick="openEditModal({{ $holiday }})" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 active:scale-95 transition">
                        <span class="material-symbols-outlined text-[18px]">edit</span>Edit
                    </button>
                    <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Hapus hari libur ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 text-rose-600 text-sm font-semibold hover:bg-rose-100 active:scale-95 transition">
                            <span class="material-symbols-outlined text-[18px]">delete</span>Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-200 px-6 py-10 text-center text-slate-500">
                <span class="material-symbols-outlined text-4xl text-slate-300">beach_access</span>
                <p class="mt-2">Belum ada hari libur.</p>
            </div>
        @endforelse
    </div>

    {{-- ===== DESKTOP/TABLET: tabel ===== --}}
    <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
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
                    @php $badge = $typeBadge[$holiday->type] ?? $typeBadge['range']; @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-900 font-bold">
                            @if($holiday->type == 'special')
                                {{ $fmt($holiday->date) }}
                            @elseif($holiday->type == 'monthly')
                                Setiap tanggal {{ $holiday->day_of_month }}
                            @else
                                {{ $fmt($holiday->start_date) }} - {{ $fmt($holiday->end_date) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $holiday->name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ $holiday }})" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Hapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
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

{{-- ===== MODAL: bottom-sheet di mobile, di tengah di desktop ===== --}}

<!-- Create Modal -->
<div id="createModal" data-modal class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center sm:p-4">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 max-w-md w-full max-h-[92vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Tambah Hari Libur</h3>
            <button type="button" onclick="closeModal('createModal')" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="{{ $labelClass }}">Tipe Hari Libur</label>
                <select name="type" id="createType" onchange="toggleCreateFields()" class="{{ $inputClass }}" required>
                    <option value="special">Khusus (Tanggal tertentu)</option>
                    <option value="monthly">Bulanan (Setiap tanggal tertentu)</option>
                </select>
            </div>
            <div id="createDateDiv">
                <label class="{{ $labelClass }}">Tanggal</label>
                <input type="date" name="date" id="createDate" class="{{ $inputClass }}">
            </div>
            <div id="createDayDiv" class="hidden">
                <label class="{{ $labelClass }}">Tanggal Bulanan</label>
                <input type="number" name="day_of_month" id="createDayOfMonth" min="1" max="31" inputmode="numeric" placeholder="Misal: 17" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Nama Hari Libur</label>
                <input type="text" name="name" id="createName" required placeholder="Misal: Hari Raya Idul Fitri" class="{{ $inputClass }}">
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-1">
                <button type="button" onclick="closeModal('createModal')" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 active:scale-95 transition">Batal</button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold shadow-lg shadow-orange-500/30 active:scale-95 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" data-modal class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center sm:p-4">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 max-w-md w-full max-h-[92vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Edit Hari Libur</h3>
            <button type="button" onclick="closeModal('editModal')" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="{{ $labelClass }}">Tipe Hari Libur</label>
                <select name="type" id="editType" onchange="toggleEditFields()" class="{{ $inputClass }}" required>
                    <option value="special">Khusus (Tanggal tertentu)</option>
                    <option value="monthly">Bulanan (Setiap tanggal tertentu)</option>
                    <option value="range">Range (Tanggal Awal - Akhir)</option>
                </select>
            </div>
            <div id="editDateDiv">
                <label class="{{ $labelClass }}">Tanggal</label>
                <input type="date" name="date" id="editDate" class="{{ $inputClass }}">
            </div>
            <div id="editDayDiv" class="hidden">
                <label class="{{ $labelClass }}">Tanggal Bulanan</label>
                <input type="number" name="day_of_month" id="editDayOfMonth" min="1" max="31" inputmode="numeric" placeholder="Misal: 17" class="{{ $inputClass }}">
            </div>
            <div id="editRangeDiv" class="hidden space-y-4">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Awal</label>
                    <input type="date" name="start_date" id="editStartDate" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="editEndDate" class="{{ $inputClass }}">
                </div>
            </div>
            <div>
                <label class="{{ $labelClass }}">Nama Hari Libur</label>
                <input type="text" name="name" id="editName" required placeholder="Misal: Hari Raya Idul Fitri" class="{{ $inputClass }}">
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-1">
                <button type="button" onclick="closeModal('editModal')" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 active:scale-95 transition">Batal</button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold shadow-lg shadow-orange-500/30 active:scale-95 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Range Modal -->
<div id="createRangeModal" data-modal class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center sm:p-4">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 max-w-md w-full max-h-[92vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900">Tambah Range Hari Libur</h3>
            <button type="button" onclick="closeModal('createRangeModal')" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form action="{{ route('holidays.range.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $labelClass }}">Tanggal Awal</label>
                    <input type="date" name="start_date" id="createStartDate" class="{{ $inputClass }}" required>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="createEndDate" class="{{ $inputClass }}" required>
                </div>
            </div>
            <div>
                <label class="{{ $labelClass }}">Nama Hari Libur</label>
                <input type="text" name="name" id="createRangeName" required placeholder="Misal: Libur Lebaran" class="{{ $inputClass }}">
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-1">
                <button type="button" onclick="closeModal('createRangeModal')" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 active:scale-95 transition">Batal</button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-bold shadow-lg shadow-teal-500/30 active:scale-95 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Tutup modal saat klik area gelap atau tekan Escape
document.querySelectorAll('[data-modal]').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal(modal.id);
    });
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-modal]').forEach(function (m) { closeModal(m.id); });
    }
});

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
    // substring(0, 10) agar tetap valid untuk input date meski nilai berformat datetime
    const d = function (v) { return v ? String(v).substring(0, 10) : ''; };

    document.getElementById('editType').value = holiday.type;
    document.getElementById('editDate').value = d(holiday.date);
    document.getElementById('editDayOfMonth').value = holiday.day_of_month || '';
    document.getElementById('editStartDate').value = d(holiday.start_date);
    document.getElementById('editEndDate').value = d(holiday.end_date);
    document.getElementById('editName').value = holiday.name;
    document.getElementById('editForm').action = '/holidays/' + holiday.id;

    toggleEditFields();
    openModal('editModal');
}
</script>
@endsection