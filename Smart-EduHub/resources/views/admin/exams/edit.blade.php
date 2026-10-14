@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @include('admin.partials.page-header', [
        'title' => 'Edit Soal Ujian',
        'eyebrow' => 'Bank Soal',
        'subtitle' => 'Perbarui pertanyaan, pilihan jawaban, dan kunci jawaban.',
        'icon' => 'edit',
    ])

    <div class="flex justify-center">
        <a href="{{ route('admin.exams.index') }}" class="px-5 py-3 rounded-2xl border border-slate-200 bg-white font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl space-y-1">
        <div class="flex items-center gap-3 mb-2">
            <span class="material-symbols-outlined">error</span>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-xs font-medium ml-8">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.exams.update', $question->id) }}" method="POST" class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pertanyaan</label>
            <textarea name="question" rows="4" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium" placeholder="Masukkan teks pertanyaan...">{{ old('question', $question->question) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan A</label>
                <input type="text" name="option_a" value="{{ old('option_a', $question->option_a) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan B</label>
                <input type="text" name="option_b" value="{{ old('option_b', $question->option_b) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan C</label>
                <input type="text" name="option_c" value="{{ old('option_c', $question->option_c) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan D</label>
                <input type="text" name="option_d" value="{{ old('option_d', $question->option_d) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jawaban Benar</label>
                <select name="correct_answer" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                    <option value="a" @selected(old('correct_answer', $question->correct_answer) === 'a')>Pilihan A</option>
                    <option value="b" @selected(old('correct_answer', $question->correct_answer) === 'b')>Pilihan B</option>
                    <option value="c" @selected(old('correct_answer', $question->correct_answer) === 'c')>Pilihan C</option>
                    <option value="d" @selected(old('correct_answer', $question->correct_answer) === 'd')>Pilihan D</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Poin Soal</label>
                <input type="number" name="score" value="{{ old('score', $question->score) }}" min="1" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
            </div>
        </div>

        <div class="pt-4 flex gap-3">
            <a href="{{ route('admin.exams.index') }}" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm text-center">Batal</a>
            <button type="submit" class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-100">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

