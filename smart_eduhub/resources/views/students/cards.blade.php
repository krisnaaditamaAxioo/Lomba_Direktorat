<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Siswa - Smart Edu Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }

        /* Kartu: ukuran tetap (rasio kartu ID) agar hasil PNG & cetak konsisten */
        .id-card {
            width: 323px;
            height: 204px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            padding: 16px 16px 14px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .06), 0 8px 24px -12px rgba(15, 23, 42, .18);
            transform: none !important;
        }
        .id-card::before {            /* aksen oranye tipis di sisi kiri */
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
            background: #ea580c;
        }
        .photo {
            width: 64px; height: 64px;
            border-radius: 12px;
            background: #f1f5f9;
            overflow: hidden;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .photo img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* Grid responsif: 1 kolom di HP, otomatis bertambah di layar lebar */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(323px, 1fr));
            gap: 28px 24px;
            justify-items: center;
        }

        @media (max-width: 359px) {   /* layar sangat kecil: kartu diperkecil untuk tampilan layar saja */
            .id-card-wrap { zoom: .9; }
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .card-grid { grid-template-columns: repeat(2, 323px); gap: 16px; justify-content: center; }
            .card-container { break-inside: avoid; }
            .id-card { box-shadow: none; border-color: #cbd5e1; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body class="px-4 py-6 sm:px-8 sm:py-10">
<div class="mx-auto max-w-6xl">

    {{-- Toolbar --}}
    <div class="no-print mb-8 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <h1 class="text-xl font-extrabold sm:text-2xl">Kartu Siswa</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $students->count() }} kartu. Unduh sebagai PNG atau cetak langsung.
            </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="button" onclick="downloadAllCards(this)" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-700 active:scale-[.98]">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                <span>Unduh semua</span>
            </button>
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    {{-- Daftar kartu --}}
    <div class="card-grid">
        @foreach($students as $student)
            <div class="card-container id-card-wrap flex flex-col items-center gap-3">
                <div id="card-{{ $student->id }}" class="id-card" data-student="{{ $student->id_siswa }}">

                    {{-- Atas: identitas lembaga --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="grid h-6 w-6 place-items-center rounded-md bg-orange-600 text-[9px] font-extrabold text-white">SA</span>
                            <span class="text-[11px] font-bold leading-none text-slate-900">Smart Axioo</span>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400">Kartu Siswa</span>
                    </div>

                    {{-- Tengah: foto + nama --}}
                    <div class="flex items-center gap-3">
                        <div class="photo">
                            @if($student->foto)
                                <img src="{{ asset('storage/' . $student->foto) }}" crossorigin="anonymous" alt="Foto {{ $student->nama_lengkap }}">
                            @else
                                <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="line-clamp-2 text-[15px] font-extrabold leading-tight text-slate-900">{{ $student->nama_lengkap }}</h2>
                            <p class="mt-1 text-[11px] font-semibold text-orange-600">Angkatan {{ $student->angkatan ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Bawah: nomor + QR --}}
                    <div class="flex items-end justify-between">
                        <dl class="space-y-1 text-[10px] leading-none">
                            <div class="flex gap-2">
                                <dt class="w-9 text-slate-400">ID</dt>
                                <dd class="font-mono font-semibold text-slate-800">{{ $student->id_siswa }}</dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="w-9 text-slate-400">NISN</dt>
                                <dd class="font-mono font-semibold text-slate-800">{{ $student->nisn ?? '-' }}</dd>
                            </div>
                        </dl>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($student->id_siswa) }}&margin=0"
                             alt="QR {{ $student->id_siswa }}" crossorigin="anonymous"
                             class="block h-12 w-12">
                    </div>
                </div>

                <button type="button" onclick="downloadCard('card-{{ $student->id }}')" class="no-print inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    Unduh PNG
                </button>
            </div>
        @endforeach
    </div>

    @if($students->isEmpty())
        <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
            <p class="font-semibold text-slate-600">Belum ada siswa untuk dicetak</p>
            <p class="mt-1 text-sm text-slate-400">Tambahkan data siswa terlebih dahulu.</p>
        </div>
    @endif
</div>

<script>
    // Mengembalikan Promise supaya "Unduh semua" benar-benar menunggu tiap kartu selesai
    function downloadCard(elementId) {
        const element = document.getElementById(elementId);
        const studentId = element.dataset.student;

        return html2canvas(element, {
            scale: 3,
            useCORS: true,
            allowTaint: false,
            backgroundColor: '#ffffff',
            logging: false,
            width: 323,
            height: 204,
            onclone: (doc) => {
                const el = doc.getElementById(elementId);
                el.style.transform = 'none';
                el.style.boxShadow = 'none';
            }
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `KARTU_${studentId}.png`;
            link.href = canvas.toDataURL('image/png', 1.0);
            link.click();
        }).catch(err => console.error('Gagal mengunduh kartu:', err));
    }

    async function downloadAllCards(btn) {
        const label = btn.querySelector('span');
        const original = label.textContent;
        btn.disabled = true;
        btn.classList.add('opacity-70');

        const cards = document.querySelectorAll('.id-card');
        let i = 0;
        for (const card of cards) {
            label.textContent = `Mengunduh ${++i}/${cards.length}`;
            await downloadCard(card.id);
            await new Promise(r => setTimeout(r, 500)); // jeda agar browser tidak memblokir unduhan
        }

        label.textContent = original;
        btn.disabled = false;
        btn.classList.remove('opacity-70');
    }
</script>
</body>
</html>