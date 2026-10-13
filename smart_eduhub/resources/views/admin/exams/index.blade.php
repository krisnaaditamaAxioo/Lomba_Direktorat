@extends('layouts.app')

@section('content')
<div class="space-y-8">
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Soal Ujian',
        'eyebrow' => 'Bank Soal',
        'subtitle' => 'Tahun Akademik Aktif: <span class="font-bold text-white">' . $academicYear . '</span>',
        'icon' => 'quiz',
    ])

    <div class="flex flex-col sm:flex-row gap-2 justify-center">
        <a href="{{ route('admin.exams.questions.template') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-slate-50 transition-all" title="Download Template Import">
            <span class="material-symbols-outlined">description</span>
            Template
        </a>
        <a href="{{ route('admin.exams.questions.export') }}" class="px-5 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-emerald-100 flex items-center justify-center gap-2 hover:bg-emerald-700 transition-all" title="Download Export Soal">
            <span class="material-symbols-outlined">download</span>
            Export Soal
        </a>
        <button onclick="document.getElementById('addQuestionModal').classList.remove('hidden')" class="px-5 py-3 bg-orange-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-100 flex items-center justify-center gap-2 hover:bg-orange-700 transition-all">
            <span class="material-symbols-outlined">add</span>
            Tambah Soal
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl">
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

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Import Soal</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Upload file Excel/CSV sesuai template.</p>
            </div>
            <form action="{{ route('admin.exams.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @csrf
                <input type="file" name="file" required class="w-full sm:w-auto text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all">
                <button type="submit" class="px-5 py-3 bg-orange-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-100 flex items-center justify-center gap-2 hover:bg-orange-700 transition-all">
                    <span class="material-symbols-outlined">upload</span>
                    Import
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pertanyaan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilihan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jawaban</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($questions as $index => $q)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2">{{ $q->question }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                <span class="text-[10px] text-slate-500"><b class="text-slate-900 dark:text-slate-400">A:</b> {{ $q->option_a }}</span>
                                <span class="text-[10px] text-slate-500"><b class="text-slate-900 dark:text-slate-400">B:</b> {{ $q->option_b }}</span>
                                <span class="text-[10px] text-slate-500"><b class="text-slate-900 dark:text-slate-400">C:</b> {{ $q->option_c }}</span>
                                <span class="text-[10px] text-slate-500"><b class="text-slate-900 dark:text-slate-400">D:</b> {{ $q->option_d }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase">{{ $q->correct_answer }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.exams.edit', $q->id) }}" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Soal">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('admin.exams.delete', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Soal">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">quiz</span>
                                <p class="text-slate-400 font-medium">Belum ada soal ujian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[32px] overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">add_circle</span>
                Tambah Soal Baru
            </h3>
            <button onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="p-2 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('admin.exams.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pertanyaan</label>
                <textarea name="question" rows="3" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium" placeholder="Masukkan teks pertanyaan..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan A</label>
                    <input type="text" name="option_a" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan B</label>
                    <input type="text" name="option_b" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan C</label>
                    <input type="text" name="option_c" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilihan D</label>
                    <input type="text" name="option_d" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jawaban Benar</label>
                    <select name="correct_answer" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                        <option value="a">Pilihan A</option>
                        <option value="b">Pilihan B</option>
                        <option value="c">Pilihan C</option>
                        <option value="d">Pilihan D</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Poin Soal</label>
                    <input type="number" name="score" value="10" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm font-medium">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-orange-100">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection
