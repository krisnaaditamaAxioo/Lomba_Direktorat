<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Seleksi - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .option-card:has(input:checked) { border-color: #4f46e5 !important; background-color: #eef2ff !important; }
        .option-card:has(input:checked) .radio-circle { border-color: #4f46e5; background-color: #4f46e5; }
        .option-card:has(input:checked) .radio-circle::after { content: ''; display: block; width: 6px; height: 6px; border-radius: 999px; background: white; }
        .question-step { display: none; }
        .question-step.active { display: block; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen pb-24">
    
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-3xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex flex-col">
                <h1 class="text-sm font-bold text-slate-900">Ujian Seleksi</h1>
                @if(isset($applicant))
                <div class="text-[12px] text-slate-500 mt-1">
                    Pendaftar: <span class="font-semibold text-slate-800">{{ $applicant->name ?? $applicant['name'] ?? '-' }}</span>
                    &nbsp;•&nbsp;
                    No: <a href="#" class="text-orange-600 font-semibold" onclick="document.getElementById('step-0').scrollIntoView(); return false;">{{ $applicant->application_id ?? $applicant['application_id'] ?? '-' }}</a>
                </div>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <div id="timer" class="text-lg font-black text-orange-600">60:00</div>
                <!-- Tombol Navigasi Mobile -->
                <button onclick="toggleModal()" class="md:hidden p-2 bg-slate-100 rounded-lg">
                    <i data-lucide="grid"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Modal Navigasi Mobile -->
    <div id="navModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-3xl w-full max-w-xs shadow-2xl">
            <h3 class="font-bold mb-4">Daftar Soal</h3>
            <div id="numberGridMobile" class="grid grid-cols-5 gap-2"></div>
            <button onclick="toggleModal()" class="mt-6 w-full py-3 bg-slate-100 rounded-xl font-bold text-sm">Tutup</button>
        </div>
    </div>

    <main class="max-w-3xl mx-auto px-4 py-8 flex flex-col md:flex-row gap-8">
        <!-- Area Soal -->
        <form id="examForm" class="flex-grow" action="{{ route('exams.submit', $registration->application_id) }}" method="POST">
            @csrf
            <input type="hidden" name="started_at" id="started_at" value="">
            @foreach($questions as $index => $q)
            <div class="question-step {{ $index == 0 ? 'active' : '' }}" id="step-{{ $index }}">
                <div class="bg-white rounded-3xl p-8 shadow-lg shadow-slate-200/50 border border-slate-100">
                    <p class="text-xl font-semibold text-slate-800 mb-8">{{ $q->question }}</p>
                    <div class="grid gap-4">
                        @foreach(['a', 'b', 'c', 'd'] as $option)
                        <label class="option-card relative flex items-center p-5 rounded-2xl border-2 border-slate-100 cursor-pointer hover:border-orange-200">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $option }}" class="peer sr-only" onchange="markAnswered({{ $index }})">
                            <div class="radio-circle w-6 h-6 border-2 border-slate-300 rounded-full"></div>
                            <span class="ml-4 font-medium text-slate-600">{{ strtoupper($option) }}. {{ $q->{'option_'.$option} }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="flex gap-4 mt-8">
                <button type="button" id="prevBtn" onclick="changeStep(-1)" class="flex-1 py-4 bg-white border-2 border-slate-200 rounded-2xl font-bold text-slate-600 disabled:opacity-50">Sebelumnya</button>
                <button type="button" id="nextBtn" onclick="changeStep(1)" class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-bold">Selanjutnya</button>
            </div>
        </form>

        <!-- Sidebar Navigasi Desktop -->
        <aside class="hidden md:block w-64 shrink-0">
            <div class="sticky top-24 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-4">Navigasi Soal</h3>
                <div class="mb-4">
                    <div class="flex justify-between text-[11px] font-bold text-slate-400 mb-1.5">
                        <span>Progres pengerjaan</span>
                        <span id="answeredProgress">0%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div id="answeredProgressBar" class="h-full bg-emerald-500 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                </div>
                <div id="numberGridDesktop" class="grid grid-cols-4 gap-2"></div>
            </div>
        </aside>
    </main>

    <script>
        let currentStep = 0;
        const totalSteps = {{ count($questions) }};

        function toggleModal() { document.getElementById('navModal').classList.toggle('hidden'); }

        // Exam timer (60 minutes)
        const TOTAL_SECONDS = 60 * 60; // 60 minutes
        let remainingSeconds = TOTAL_SECONDS;
        let timerInterval = null;

        function formatTime(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }

        function startTimer() {
            const timerEl = document.getElementById('timer');
            if (!timerEl) return;
            // set initial
            timerEl.innerText = formatTime(remainingSeconds);

            timerInterval = setInterval(() => {
                remainingSeconds -= 1;
                if (remainingSeconds < 0) {
                    clearInterval(timerInterval);
                    // Auto-submit when time is up
                    submitExam(true);
                    return;
                }
                timerEl.innerText = formatTime(remainingSeconds);
            }, 1000);
        }

        function stopTimer() {
            if (timerInterval) clearInterval(timerInterval);
        }

        function markAnswered(index) {
            const navs = [document.getElementById('nav-d-' + index), document.getElementById('nav-m-' + index)];
            navs.forEach(n => {
                if (n) {
                    n.dataset.answered = 'true';
                    n.classList.remove('bg-slate-100', 'bg-orange-600', 'text-slate-400');
                    n.classList.add('bg-emerald-500', 'text-white');
                }
            });
            updateAnsweredProgress();
            updateNavState();
        }

        function updateAnsweredProgress() {
            const answered = document.querySelectorAll('#examForm input[type="radio"]:checked').length;
            const percentage = totalSteps ? Math.round((answered / totalSteps) * 100) : 0;
            const label = document.getElementById('answeredProgress');
            const bar = document.getElementById('answeredProgressBar');
            if (label) label.innerText = percentage + '%';
            if (bar) bar.style.width = percentage + '%';
        }

        function updateNavState() {
            for (let j = 0; j < totalSteps; j++) {
                const nd = document.getElementById('nav-d-' + j);
                const nm = document.getElementById('nav-m-' + j);

                [nd, nm].forEach(el => {
                    if (!el) return;

                    if (el.dataset.answered === 'true') {
                        el.classList.remove('bg-slate-100', 'bg-orange-600', 'text-slate-400');
                        el.classList.add('bg-emerald-500', 'text-white');
                    } else if (j === currentStep) {
                        el.classList.remove('bg-slate-100', 'bg-emerald-500', 'text-slate-400');
                        el.classList.add('bg-orange-600', 'text-white');
                    } else {
                        el.classList.remove('bg-orange-600', 'bg-emerald-500', 'text-white');
                        el.classList.add('bg-slate-100', 'text-slate-400');
                    }
                });
            }
        }

        function changeStep(dir) {
            goToStep(currentStep + dir);
        }

        function goToStep(index) {
            if (index < 0) index = 0;
            if (index > totalSteps - 1) index = totalSteps - 1;

            // Hide previous
            const prevStepEl = document.getElementById('step-' + currentStep);
            if (prevStepEl) prevStepEl.classList.remove('active');

            // Update current
            currentStep = index;

            // Show current
            const curStepEl = document.getElementById('step-' + currentStep);
            if (curStepEl) curStepEl.classList.add('active');

            // Update buttons
            if (currentStep >= totalSteps - 1) {
                document.getElementById('nextBtn').innerText = 'Selesaikan';
                document.getElementById('nextBtn').onclick = () => submitExam(false);
            } else {
                document.getElementById('nextBtn').innerText = 'Selanjutnya';
                document.getElementById('nextBtn').onclick = () => changeStep(1);
            }
            document.getElementById('prevBtn').disabled = currentStep === 0;

            updateNavState();
            updateAnsweredProgress();

            // Close mobile modal after selecting
            const modal = document.getElementById('navModal');
            if (modal && !modal.classList.contains('hidden')) modal.classList.add('hidden');
        }

        // Submit exam via AJAX (used for manual or auto submit)
        function submitExam(isAuto = false) {
            const form = document.getElementById('examForm');
            if (!form) return;

            // disable controls
            document.getElementById('nextBtn').disabled = true;
            document.getElementById('prevBtn').disabled = true;

            // ensure started_at is set
            const startedInput = document.getElementById('started_at');
            if (startedInput && !startedInput.value) startedInput.value = new Date().toISOString();

            const fd = new FormData(form);

            // POST via fetch
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
            }).then(r => r.json()).then(data => {
                if (data && data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else if (data && !data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else if (data && data.message) {
                    alert(data.message);
                    if (data.redirect) window.location.href = data.redirect;
                } else {
                    // fallback: reload
                    window.location.reload();
                }
            }).catch(err => {
                console.error(err);
                if (!isAuto) alert('Gagal mengirim jawaban. Silakan coba lagi.');
            });
        }

        window.onload = () => {
            const gridDesktop = document.getElementById('numberGridDesktop');
            const gridMobile = document.getElementById('numberGridMobile');
            for(let i=0; i<totalSteps; i++) {
                const elD = `<div id="nav-d-${i}" onclick="goToStep(${i})" class="cursor-pointer w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-400">${i+1}</div>`;
                const elM = `<div id="nav-m-${i}" onclick="goToStep(${i})" class="cursor-pointer w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-400">${i+1}</div>`;
                gridDesktop.innerHTML += elD;
                gridMobile.innerHTML += elM;
            }
            document.getElementById('prevBtn').disabled = true;
            lucide.createIcons();
            // Initialize nav highlighting and active step
            goToStep(0);

            // Set started_at and start countdown
            const startedInput = document.getElementById('started_at');
            if (startedInput && !startedInput.value) startedInput.value = new Date().toISOString();
            startTimer();
        }
    </script>
</body>
</html>