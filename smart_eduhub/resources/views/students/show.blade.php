@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <nav class="flex items-center space-x-2 text-outline mb-2">
            <a href="{{ route('students') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Student Records</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="font-label-sm text-label-sm text-primary">Student Profile</span>
        </nav>
        <h2 class="font-display-xl text-display-xl text-on-surface">{{ $student->nama_lengkap }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Detailed information and academic records.</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('students.edit', $student) }}" class="bg-white text-on-surface border border-slate-200 font-label-md text-label-md px-6 py-3 rounded-xl flex items-center space-x-2 custom-shadow hover:bg-slate-50 transition-all">
            <span class="material-symbols-outlined">edit</span>
            <span>Edit Profile</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Profile Card -->
    <div class="lg:col-span-1 space-y-8">
        <div class="bg-white rounded-3xl custom-shadow border border-slate-100 overflow-hidden">
            <div class="bg-primary h-24 relative"></div>
            <div class="px-8 pb-8 -mt-12 relative text-center">
                <div class="w-24 h-24 rounded-full bg-white p-1 inline-block custom-shadow">
                    <div class="w-full h-full rounded-full bg-primary/10 flex items-center justify-center text-primary text-3xl font-black">
                        {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                    </div>
                </div>
                <h3 class="mt-4 font-headline-md text-on-surface font-bold text-xl">{{ $student->nama_lengkap }}</h3>
                <p class="text-outline font-medium">{{ $student->id_siswa }}</p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Angkatan {{ $student->angkatan ?: '-' }}</span>
                    <span class="px-3 py-1 {{ $student->status === 0 ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' }} text-xs font-bold rounded-full">{{ $student->status_text }}</span>
                </div>
            </div>
            <div class="border-t border-slate-50 p-8 space-y-4">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">vpn_key</span>
                        <p class="text-xs font-black uppercase tracking-widest text-amber-700">Akun Login Rahasia</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-amber-700">Username</span>
                            <span class="font-black text-amber-900 text-lg font-mono bg-amber-100 px-3 py-1 rounded-lg">{{ $student->username }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-amber-700">PIN Login (6 digit)</span>
                            <span class="font-black text-amber-900 text-lg font-mono bg-amber-100 px-3 py-1 rounded-lg tracking-wider">{{ $student->pin }}</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-amber-600 mt-3 italic">⚠️ Username dan PIN di atas TIDAK DISENSOR. Harap jaga kerahasiaan akun ini.</p>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">ID Siswa</span>
                    <span class="font-bold text-on-surface">{{ $student->id_siswa }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">NIS Siswa</span>
                    <span class="font-bold text-on-surface">{{ $student->nis_siswa ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">NISN</span>
                    <span class="font-bold text-on-surface">{{ $student->nisn ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">No NIK</span>
                    <span class="font-bold text-on-surface">{{ $student->no_nik ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">Angkatan</span>
                    <span class="font-bold text-on-surface">{{ $student->angkatan ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">No. HP Siswa</span>
                    <span class="font-bold text-on-surface">{{ $student->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">WA Wali / Ortu</span>
                    <span class="font-bold text-on-surface">{{ $student->parent_phone ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">Email</span>
                    <span class="font-bold text-on-surface">{{ $student->email }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">Sekolah Asal</span>
                    <span class="font-bold text-on-surface">{{ $student->previous_school ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">Tanggal Daftar</span>
                    <span class="font-bold text-on-surface">{{ $student->created_at ? \Carbon\Carbon::parse($student->created_at)->format('d M Y') : '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-outline">Role</span>
                    <span class="font-bold text-on-surface">{{ $student->role ?: 'student' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details & Records -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Personal Details -->
        <div class="bg-white rounded-3xl custom-shadow border border-slate-100 p-8">
            <h4 class="font-headline-md text-on-surface flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-primary">info</span>
                Personal Details Lengkap (Semua Data)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">ID Siswa</p>
                    <p class="text-on-surface font-medium font-mono">{{ $student->id_siswa }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Nama Lengkap</p>
                    <p class="text-on-surface font-medium">{{ $student->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Gender</p>
                    <p class="text-on-surface font-medium">{{ $student->gender_text }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Agama</p>
                    <p class="text-on-surface font-medium">{{ $student->religion ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Place & Date of Birth</p>
                    <p class="text-on-surface font-medium">{{ $student->place_of_birth ?: '-' }}, {{ $student->dateofbirth ? date('d M Y', strtotime($student->dateofbirth)) : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Mother's Name (Nama Ibu)</p>
                    <p class="text-on-surface font-medium">{{ $student->mother_name ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Height & Weight</p>
                    <p class="text-on-surface font-medium">{{ $student->height ? $student->height . ' cm' : '-' }} / {{ $student->weight ? $student->weight . ' kg' : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Previous School (Sekolah Asal)</p>
                    <p class="text-on-surface font-medium">{{ $student->previous_school ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">NIS Siswa</p>
                    <p class="text-on-surface font-medium font-mono">{{ $student->nis_siswa ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">NISN</p>
                    <p class="text-on-surface font-medium font-mono">{{ $student->nisn ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">No. NIK</p>
                    <p class="text-on-surface font-medium font-mono">{{ $student->no_nik ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Status & Role</p>
                    <p class="text-on-surface font-medium">{{ $student->status_text }} / {{ $student->role ?: 'student' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Email</p>
                    <p class="text-on-surface font-medium">{{ $student->email ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">No. HP Siswa</p>
                    <p class="text-on-surface font-medium">{{ $student->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">WA Wali / Orang Tua</p>
                    <p class="text-on-surface font-medium">{{ $student->parent_phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Tanggal Daftar</p>
                    <p class="text-on-surface font-medium">{{ $student->created_at ? \Carbon\Carbon::parse($student->created_at)->format('d M Y H:i') : '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Address (Alamat Lengkap)</p>
                    <p class="text-on-surface font-medium leading-relaxed">{{ $student->address ?: 'No address information provided.' }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-3xl custom-shadow border border-slate-100 overflow-hidden">
            <div class="p-8 flex items-center justify-between">
                <h4 class="font-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">event_available</span>
                    Recent Attendance
                </h4>
                <a href="{{ route('attendance') }}" class="text-sm font-bold text-primary hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-8 py-3 text-xs font-bold text-outline uppercase">Date</th>
                            <th class="px-8 py-3 text-xs font-bold text-outline uppercase">Check In</th>
                            <th class="px-8 py-3 text-xs font-bold text-outline uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($student->attendances->take(5) as $attendance)
                        <tr>
                            <td class="px-8 py-4 text-sm font-medium text-on-surface">{{ date('d M Y', strtotime($attendance->date)) }}</td>
                            <td class="px-8 py-4 text-sm text-on-surface-variant">
                                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i A') : '--:--' }}
                            </td>
                            <td class="px-8 py-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $attendance->status === 'present' ? 'bg-green-50 text-green-600' : 'bg-rose-50 text-rose-600' }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-8 text-center text-sm text-outline italic">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
