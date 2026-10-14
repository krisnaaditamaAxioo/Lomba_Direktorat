@extends('layouts.app')

@section('title', 'Manual Attendance Entry')

@section('content')
<div class="mb-8">
    <nav class="flex items-center space-x-2 text-outline mb-2">
        <a href="{{ route('attendance') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Attendance Monitoring</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="font-label-sm text-label-sm text-primary">Manual Entry</span>
    </nav>
    <h2 class="font-display-xl text-display-xl text-on-surface">Add Attendance Record</h2>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Create a new attendance record manually for a student.</p>
</div>

<div class="max-w-3xl">
    <form action="{{ route('attendance.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white p-8 rounded-3xl custom-shadow border border-slate-100 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student Selection -->
                <div class="space-y-2 md:col-span-2">
                    <label for="student_id" class="block text-sm font-bold text-slate-700">Student</label>
                    <select name="student_id" id="student_id" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('student_id') border-error @enderror" required>
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nama_lengkap }} ({{ $student->id_siswa }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <label for="date" class="block text-sm font-bold text-slate-700">Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('date') border-error @enderror" required>
                    @error('date') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Check-in Time -->
                <div class="space-y-2">
                    <label for="check_in" class="block text-sm font-bold text-slate-700">Check-in Time</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('check_in') border-error @enderror">
                    <p class="text-[10px] text-slate-400 font-medium italic">* Kosongkan jika Alpa/Izin</p>
                    @error('check_in') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="status" class="block text-sm font-bold text-slate-700">Status</label>
                    <select name="status" id="status" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('status') border-error @enderror" required>
                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present (Auto if Time set)</option>
                        <option value="absent" {{ old('status', 'absent') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late (Auto if Time set)</option>
                        <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Excused (Izin/Sakit)</option>
                    </select>
                    <p id="status-hint" class="text-[10px] text-orange-500 font-bold hidden uppercase tracking-widest">Otomatis ditentukan oleh sistem</p>
                    @error('status') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Location -->
                <div class="space-y-2">
                    <label for="location" class="block text-sm font-bold text-slate-700">Location / Keterangan</label>
                    <input type="text" name="location" id="location" value="{{ old('location', 'Manual Entry') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('location') border-error @enderror" placeholder="e.g. Sakit, Izin Lomba, dll">
                    @error('location') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('attendance') }}" class="px-6 py-3 rounded-xl font-label-md text-label-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-3 rounded-xl font-label-md text-label-md text-white bg-primary hover:bg-orange-700 custom-shadow transition-all">Save Attendance</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const checkInInput = document.getElementById('check_in');
    const statusSelect = document.getElementById('status');
    const statusHint = document.getElementById('status-hint');
    const checkInLimit = "{{ \App\Models\Setting::get('check_in_time', '08:00') }}";

    function updateStatus() {
        if (checkInInput.value) {
            const time = checkInInput.value;
            statusHint.classList.remove('hidden');
            
            if (time > checkInLimit) {
                statusSelect.value = 'late';
            } else {
                statusSelect.value = 'present';
            }
        } else {
            statusHint.classList.add('hidden');
            if (statusSelect.value === 'present' || statusSelect.value === 'late') {
                statusSelect.value = 'absent';
            }
        }
    }

    checkInInput.addEventListener('change', updateStatus);
    checkInInput.addEventListener('input', updateStatus);
</script>
@endpush
@endsection
