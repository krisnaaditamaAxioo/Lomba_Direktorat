<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Siswa Baru - Smart Edu Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%); 
        }
        .step-content { display: none; }
        .step-content.active { 
            display: block; 
            animation: slideIn 0.4s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .step-indicator.active { color: #4f46e5; border-color: #4f46e5; }
        .step-indicator.completed { background-color: #4f46e5; color: white; border-color: #4f46e5; }
        
        textarea::-webkit-scrollbar { width: 6px; }
        textarea::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-0 md:p-8">
    <div class="max-w-6xl w-full bg-white md:rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-screen md:min-h-[700px]">
        
        <!-- Sidebar / Header Section -->
        <div class="w-full md:w-5/12 bg-orange-600 relative p-8 md:p-12 text-white flex flex-col justify-between overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-[-10%] left-[-10%] w-64 h-64 bg-orange-500 rounded-full opacity-20"></div>
            <div class="absolute bottom-[-5%] right-[-5%] w-48 h-48 bg-orange-400 rounded-full opacity-20"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6 md:mb-8">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-md">
                        <i data-lucide="graduation-cap" class="w-6 h-6 md:w-8 md:h-8 text-white"></i>
                    </div>
                    <span class="text-lg md:text-xl font-bold tracking-tight">Smart Edu Hub</span>
                </div>
                
                <h2 class="text-2xl md:text-4xl font-extrabold leading-tight mb-4 md:mb-6">Mulai Masa Depan Digitalmu di Sini.</h2>
                <p class="text-orange-100 text-sm md:text-lg opacity-90">Bergabunglah dengan ekosistem pendidikan teknologi terbaik untuk mencetak generasi unggul.</p>
            </div>

            <!-- Stepper Progress (Responsive: Horizontal on Mobile, Vertical on Desktop) -->
            <div class="relative z-10 mt-8 md:mt-0 flex flex-row md:flex-col justify-between md:justify-start md:space-y-8">
                <!-- Step 1 Indicator -->
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 group">
                    <div id="side-step-1" class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/30 flex items-center justify-center text-sm md:text-base font-bold transition-all">1</div>
                    <div class="hidden md:block">
                        <p class="text-[10px] md:text-sm font-semibold opacity-60">Langkah 1</p>
                        <p class="text-xs md:font-medium">Data Pribadi</p>
                    </div>
                </div>
                <!-- Connector Line Mobile -->
                <div class="flex-1 h-[2px] bg-white/20 self-center mx-2 md:hidden"></div>
                <!-- Step 2 Indicator -->
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 group">
                    <div id="side-step-2" class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/30 flex items-center justify-center text-sm md:text-base font-bold transition-all">2</div>
                    <div class="hidden md:block">
                        <p class="text-[10px] md:text-sm font-semibold opacity-60">Langkah 2</p>
                        <p class="text-xs md:font-medium">Detail Fisik</p>
                    </div>
                </div>
                <!-- Connector Line Mobile -->
                <div class="flex-1 h-[2px] bg-white/20 self-center mx-2 md:hidden"></div>
                <!-- Step 3 Indicator -->
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 group">
                    <div id="side-step-3" class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/30 flex items-center justify-center text-sm md:text-base font-bold transition-all">3</div>
                    <div class="hidden md:block">
                        <p class="text-[10px] md:text-sm font-semibold opacity-60">Langkah 3</p>
                        <p class="text-xs md:font-medium">Angkatan</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-[10px] md:text-sm text-orange-200 mt-8 md:mt-0 hidden md:block">
                &copy; 2026 Smart Edu Hub Management System
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="flex-1 p-8 md:p-12 lg:p-16 overflow-y-auto bg-white">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('success') }}
                </div>
            @endif


            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        {{ $error }}
                    </div>
                @endforeach
            @endif

            <form action="{{ route('registrations.submit') }}" method="POST" id="regForm" class="h-full flex flex-col">
                @csrf
                
                <!-- STEP 1: Personal Info -->
                <div id="step-1" class="step-content active space-y-6">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-slate-800">Data Pribadi</h3>
                        <p class="text-slate-500">Informasi identitas dasar calon siswa.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" required placeholder="nama@email.com">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">No. HP / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" required placeholder="0812...">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Jenis Kelamin</label>
                            <select name="gender" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all appearance-none bg-white">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">NIK</label>
                            <input type="text" name="no_nik" value="{{ old('no_nik') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" placeholder="16 digit NIK">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" placeholder="10 digit NISN">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Birth & Background -->
                <div id="step-2" class="step-content space-y-6">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-slate-800">Detail & Latar Belakang</h3>
                        <p class="text-slate-500">Lengkapi informasi fisik dan sekolah asal.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Tempat Lahir</label>
                            <input type="text" name="place_of_birth" value="{{ old('place_of_birth') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Tanggal Lahir</label>
                            <input type="date" name="dateofbirth" value="{{ old('dateofbirth') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Agama</label>
                            <input type="text" name="religion" value="{{ old('religion') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Tinggi (cm)</label>
                                <input type="number" name="height" value="{{ old('height') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Berat (kg)</label>
                                <input type="number" name="weight" value="{{ old('weight') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Sekolah Asal</label>
                            <input type="text" name="previous_school" value="{{ old('previous_school') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" required placeholder="SMP Negeri...">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Address & Angkatan -->
                <div id="step-3" class="step-content space-y-6">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-slate-800">Domisili & Angkatan</h3>
                        <p class="text-slate-500">Tahap akhir pendaftaran Anda.</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                @if($autoAngkatan)
                                    <label class="text-sm font-semibold text-slate-700">Angkatan (Otomatis)</label>
                                    <input type="text" value="{{ $autoAngkatan->name }}{{ $autoAngkatan->tahun_masuk ? ' • Tahun Masuk ' . $autoAngkatan->tahun_masuk : '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-bold" readonly>
                                @else
                                    <label class="text-sm font-semibold text-slate-700">Pilih Angkatan</label>
                                    <select name="angkatan" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all appearance-none bg-white" required>
                                        <option value="">-- Pilih Angkatan --</option>
                                        @foreach($angkatans as $angkatan)
                                            <option value="{{ $angkatan->code }}" {{ old('angkatan') == $angkatan->code ? 'selected' : '' }}>
                                                {{ $angkatan->name }} ({{ $angkatan->tahun_masuk ? 'Tahun Masuk ' . $angkatan->tahun_masuk : '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Nomor Induk Siswa</label>
                                <input type="text" name="nis_siswa" value="{{ old('nis_siswa') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                            <textarea name="address" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all resize-none" required placeholder="Jl. Nama Jalan No. 123, Kelurahan, Kecamatan...">{{ old('address') }}</textarea>
                        </div>
                        
                        <div class="p-4 bg-orange-50 rounded-2xl flex gap-4 items-start">
                            <i data-lucide="info" class="w-5 h-5 text-orange-600 shrink-0 mt-1"></i>
                            <p class="text-xs text-orange-800 leading-relaxed">
                                Dengan menekan tombol kirim, Anda menyatakan bahwa data yang diisi adalah benar dan bersedia mengikuti prosedur penerimaan siswa baru di Smart Edu Hub.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="mt-auto pt-10 flex gap-4">
                    @if(!$errors->has('angkatan'))
                        <button type="button" id="prevBtn" class="hidden flex-1 py-4 px-6 border-2 border-slate-100 hover:bg-slate-50 text-slate-600 font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i> <span class="hidden sm:inline">Kembali</span>
                        </button>
                        <button type="button" id="nextBtn" class="flex-[2] py-4 px-6 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-2xl shadow-xl shadow-orange-200 transition-all flex items-center justify-center gap-2">
                            Lanjut <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                        <button type="submit" id="submitBtn" class="hidden flex-[2] py-4 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                            Kirim Pendaftaran <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    @else
                        <a href="{{ url('/') }}" class="flex-1 py-4 px-6 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-2xl shadow-xl shadow-slate-200 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i> Kembali ke Beranda
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            @if(!$errors->has('angkatan'))
                const steps = [document.getElementById('step-1'), document.getElementById('step-2'), document.getElementById('step-3')];
                const sideSteps = [document.getElementById('side-step-1'), document.getElementById('side-step-2'), document.getElementById('side-step-3')];
                
                const nextBtn = document.getElementById('nextBtn');
                const prevBtn = document.getElementById('prevBtn');
                const submitBtn = document.getElementById('submitBtn');
                
                let currentStep = 0;

                function updateStepUI() {
                    steps.forEach((step, idx) => {
                        step.classList.toggle('active', idx === currentStep);
                    });

                    sideSteps.forEach((sStep, idx) => {
                        sStep.classList.remove('bg-white', 'text-orange-600', 'border-white', 'bg-orange-400');
                        sStep.innerHTML = (idx + 1);
                        
                        if (idx < currentStep) {
                            sStep.classList.add('bg-orange-400', 'border-orange-400', 'text-white');
                            sStep.innerHTML = '<i data-lucide="check" class="w-4 h-4 md:w-5 md:h-5"></i>';
                        } else if (idx === currentStep) {
                            sStep.classList.add('bg-white', 'text-orange-600', 'border-white');
                        } else {
                            sStep.classList.add('border-white/30');
                        }
                    });
                    
                    if (typeof lucide !== 'undefined') lucide.createIcons();

                    prevBtn.classList.toggle('hidden', currentStep === 0);
                    if (currentStep === steps.length - 1) {
                        nextBtn.classList.add('hidden');
                        submitBtn.classList.remove('hidden');
                    } else {
                        nextBtn.classList.remove('hidden');
                        submitBtn.classList.add('hidden');
                    }
                }

                nextBtn.addEventListener('click', () => {
                    const inputs = steps[currentStep].querySelectorAll('input[required], select[required], textarea[required]');
                    let valid = true;
                    inputs.forEach(input => {
                        if(!input.value) {
                            input.classList.add('border-red-500', 'ring-red-500/10');
                            valid = false;
                        } else {
                            input.classList.remove('border-red-500', 'ring-red-500/10');
                        }
                    });

                    if (valid && currentStep < steps.length - 1) {
                        currentStep++;
                        updateStepUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });

                prevBtn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep--;
                        updateStepUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });

                updateStepUI();
            @endif
        };
    </script>
</body>
</html>
