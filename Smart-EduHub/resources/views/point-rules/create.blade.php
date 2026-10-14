@extends('layouts.app')

@section('content')
@php
    $inputClass = 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 placeholder:font-normal focus:border-orange-500 focus:ring-4 focus:ring-orange-500/15 outline-none transition';
    $labelClass = 'flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-slate-500 mb-2';
    $iconClass  = 'material-symbols-outlined text-[16px] text-orange-500';
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ===== HEADER: menempel ke atas & samping di mobile, tanpa celah ===== --}}
    <header class="relative overflow-hidden -mx-4 -mt-4 sm:-mx-6 sm:-mt-6 md:mx-0 md:mt-0
                   rounded-b-[2rem] md:rounded-3xl
                   bg-gradient-to-br from-orange-500 via-orange-600 to-red-700
                   text-white px-5 pt-5 pb-8 md:px-10 md:py-9 shadow-xl shadow-orange-600/25">

        {{-- dekorasi lingkaran --}}
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 w-48 h-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute bottom-0 right-1/4 w-32 h-32 rounded-full bg-black/10"></div>

        <div class="relative">
            {{-- baris atas: brand + tombol kembali --}}
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <span class="font-black text-lg tracking-tight">SEH</span>
                </div>

                <a href="{{ route('point-rules.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-slate-800 text-sm font-bold shadow-md hover:bg-orange-50 active:scale-95 transition">
                    <span class="material-symbols-outlined text-[18px] text-orange-500">arrow_back</span>
                    Kembali
                </a>
            </div>

            {{-- judul --}}
            <div class="text-center mt-6 md:mt-8">
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-orange-200">Kedisiplinan</p>
                <h2 class="mt-1 text-3xl md:text-4xl font-black tracking-tight leading-tight">Tambah Peraturan</h2>
                <p class="mt-2 text-sm md:text-base text-orange-100">Tambahkan peraturan poin baru untuk kedisiplinan siswa.</p>
            </div>
        </div>
    </header>

    {{-- ===== ISI HALAMAN ===== --}}
    <div class="space-y-4 md:space-y-6">

        {{-- error validasi --}}
        @if($errors->any())
            <div class="flex gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl">
                <span class="material-symbols-outlined text-[20px] mt-0.5">error</span>
                <ul class="text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('point-rules.store') }}" class="space-y-4 md:space-y-6">
            @csrf

            {{-- Kartu 1: Informasi --}}
            <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 md:p-7">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">rule</span>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 leading-tight">Informasi Peraturan</h3>
                        <p class="text-xs text-slate-400">Nama dan kondisi kehadiran</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">edit_note</span>Nama Peraturan</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Terlambat 5x" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">how_to_reg</span>Status Kehadiran</label>
                        <select name="attendance_status" required class="{{ $inputClass }}">
                            @foreach(['late' => 'Terlambat', 'absent' => 'Alpa', 'present' => 'Hadir', 'excused' => 'Izin'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('attendance_status') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">repeat</span>Batas (Jumlah)</label>
                        <input type="number" name="threshold" value="{{ old('threshold') }}" required min="1" inputmode="numeric" placeholder="Misal: 5" class="{{ $inputClass }}">
                    </div>
                </div>
            </section>

            {{-- Kartu 2: Poin --}}
            <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 md:p-7">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">stars</span>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 leading-tight">Pengaturan Poin</h3>
                        <p class="text-xs text-slate-400">Tipe, nilai, dan urutan tampil</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                    <div>
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">swap_vert</span>Tipe Poin</label>
                        <select name="point_type" required class="{{ $inputClass }}">
                            <option value="achievement" @selected(old('point_type') == 'achievement')>Prestasi (+)</option>
                            <option value="violation" @selected(old('point_type') == 'violation')>Pelanggaran (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">pin</span>Jumlah Poin</label>
                        <input type="number" name="point_score" value="{{ old('point_score') }}" required min="1" inputmode="numeric" placeholder="Misal: 5" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label class="{{ $labelClass }}"><span class="{{ $iconClass }}">format_list_numbered</span>Urutan Sortir</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" inputmode="numeric" class="{{ $inputClass }}">
                    </div>

                    <div class="flex items-end">
                        <label for="createIsActive" class="w-full flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer hover:bg-orange-50/60 hover:border-orange-200 transition">
                            <input type="checkbox" name="is_active" id="createIsActive" @checked($errors->any() ? old('is_active') : true) class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            <span class="text-sm font-semibold text-slate-700">Aktifkan peraturan ini</span>
                        </label>
                    </div>
                </div>
            </section>

            {{-- Tombol aksi --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-4">
                <a href="{{ route('point-rules.index') }}"
                   class="w-full sm:w-auto text-center px-6 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 active:scale-95 transition">
                    Batal
                </a>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection