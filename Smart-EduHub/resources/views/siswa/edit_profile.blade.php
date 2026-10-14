@extends('layouts.siswa')

@section('title', 'Edit Profil')
@section('page_title', 'Perbarui Data Diri')

@php
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/15';
    $labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700';
    $btnPrimary = 'inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-brand-600/30 active:translate-y-0 active:scale-[0.98] focus:outline-none focus-visible:ring-4 focus-visible:ring-brand-500/30 disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0';
    $btnGhost = 'inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition duration-200 hover:bg-slate-50 hover:text-slate-900 active:scale-[0.98] focus:outline-none focus-visible:ring-4 focus-visible:ring-slate-300/50';
    $photoUrl = $student->foto
        ? asset('storage/' . $student->foto)
        : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=6366f1&color=fff&size=256';
    $dob = $student->dateofbirth ? \Illuminate\Support\Carbon::parse($student->dateofbirth)->format('Y-m-d') : '';
@endphp

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-28 sm:px-6 md:pb-10">

    {{-- Pesan error umum --}}
    @if ($errors->any())
        <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <span class="material-symbols-outlined mt-0.5 text-base">error</span>
            <div>
                <p class="font-semibold">Data belum bisa disimpan</p>
                <p class="mt-0.5 text-rose-600">Periksa kolom yang ditandai di bawah, lalu coba lagi.</p>
            </div>
        </div>
    @endif

    <form id="profile-form" action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-3 lg:items-start">

            {{-- ============ Kolom kiri: foto ============ --}}
            <aside class="lg:sticky lg:top-6">
                <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-sm">
                    <div class="h-20 bg-gradient-to-br from-brand-500 to-brand-700 sm:h-24"></div>

                    <div class="-mt-12 flex flex-col items-center px-6 pb-6 text-center sm:-mt-14">
                        <div class="relative">
                            <img id="photo-preview" src="{{ $photoUrl }}" alt="Foto profil {{ $student->name }}"
                                 class="h-24 w-24 rounded-full border-4 border-white bg-slate-100 object-cover shadow-md sm:h-28 sm:w-28">
                            <label for="foto"
                                   class="absolute bottom-0 right-0 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-[3px] border-white bg-gradient-to-br from-brand-600 to-brand-500 text-white shadow-lg transition duration-200 hover:scale-110 active:scale-95 focus-within:ring-4 focus-within:ring-brand-500/30"
                                   title="Ganti foto">
                                <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                                <span class="sr-only">Ganti foto profil</span>
                                <input type="file" name="foto" id="foto" class="sr-only" accept="image/jpeg,image/png,image/webp">
                            </label>
                        </div>

                        <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $student->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                        <p id="photo-error" class="mt-2 hidden text-sm text-rose-600" role="alert"></p>
                        @error('foto') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </aside>

            {{-- ============ Kolom kanan: form ============ --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Kontak --}}
                <section class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-sm sm:p-8">
                    <header class="mb-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <span class="material-symbols-outlined">contact_phone</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 sm:text-lg">Kontak dan akun</h3>
                            <p class="text-sm text-slate-500">Dipakai untuk pemberitahuan dan masuk ke akun.</p>
                        </div>
                    </header>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="email" class="{{ $labelClass }}">Alamat email</label>
                            <input type="email" name="email" id="email" autocomplete="email" inputmode="email"
                                   value="{{ old('email', $student->email) }}" class="{{ $inputClass }} @error('email') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">
                            @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="{{ $labelClass }}">Nomor HP / WhatsApp</label>
                            <input type="tel" name="phone" id="phone" autocomplete="tel" inputmode="tel" placeholder="08xxxxxxxxxx"
                                   value="{{ old('phone', $student->phone) }}" class="{{ $inputClass }} @error('phone') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">
                            @error('phone') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- Informasi pribadi --}}
                <section class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-sm sm:p-8">
                    <header class="mb-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <span class="material-symbols-outlined">person_pin</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 sm:text-lg">Informasi pribadi</h3>
                            <p class="text-sm text-slate-500">Sesuaikan dengan data pada dokumen resmi.</p>
                        </div>
                    </header>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="place_of_birth" class="{{ $labelClass }}">Tempat lahir</label>
                            <input type="text" name="place_of_birth" id="place_of_birth"
                                   value="{{ old('place_of_birth', $student->place_of_birth) }}" class="{{ $inputClass }} @error('place_of_birth') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">
                            @error('place_of_birth') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="dateofbirth" class="{{ $labelClass }}">Tanggal lahir</label>
                            <input type="date" name="dateofbirth" id="dateofbirth"
                                   value="{{ old('dateofbirth', $dob) }}" class="{{ $inputClass }} @error('dateofbirth') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">
                            @error('dateofbirth') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="{{ $labelClass }}">Alamat lengkap</label>
                            <textarea name="address" id="address" rows="3" autocomplete="street-address"
                                      class="{{ $inputClass }} resize-y @error('address') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">{{ old('address', $student->address) }}</textarea>
                            @error('address') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- Keamanan --}}
                <section class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-sm sm:p-8">
                    <header class="mb-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <span class="material-symbols-outlined">lock</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 sm:text-lg">Keamanan (PIN akses)</h3>
                            <p class="text-sm text-slate-500">Kosongkan jika tidak ingin mengubah PIN. PIN terdiri dari 6 angka.</p>
                        </div>
                    </header>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="pin" class="{{ $labelClass }}">PIN baru</label>
                            <div class="relative">
                                <input type="password" name="pin" id="pin" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                                       autocomplete="new-password" placeholder="••••••"
                                       class="{{ $inputClass }} pr-12 tracking-[0.3em] @error('pin') border-rose-400 focus:border-rose-500 focus:ring-rose-500/15 @enderror">
                                <button type="button" data-toggle-pin="pin" aria-label="Tampilkan PIN"
                                        class="absolute inset-y-1 right-1 flex w-10 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            @error('pin') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="pin_confirmation" class="{{ $labelClass }}">Konfirmasi PIN baru</label>
                            <div class="relative">
                                <input type="password" name="pin_confirmation" id="pin_confirmation" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                                       autocomplete="new-password" placeholder="••••••"
                                       class="{{ $inputClass }} pr-12 tracking-[0.3em]">
                                <button type="button" data-toggle-pin="pin_confirmation" aria-label="Tampilkan konfirmasi PIN"
                                        class="absolute inset-y-1 right-1 flex w-10 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p id="pin-match" class="mt-1.5 hidden text-sm text-rose-600" role="alert">PIN dan konfirmasi belum sama.</p>
                        </div>
                    </div>
                </section>

                {{-- Tombol aksi (desktop) --}}
                <div class="hidden items-center justify-end gap-3 md:flex">
                    <a href="{{ route('siswa.profile') }}" class="{{ $btnGhost }}">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                        Batal
                    </a>
                    <button type="submit" data-submit class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors">
    <span class="material-symbols-outlined text-[18px]">check</span>
    <span data-label>Simpan perubahan</span>
</button>
                </div>
            </div>
        </div>

        {{-- Bar aksi tetap di bawah (mobile) --}}
        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200/80 bg-white/85 px-4 pt-3 shadow-[0_-8px_24px_-12px_rgba(15,23,42,0.15)] backdrop-blur-xl md:hidden"
             style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
            <div class="mx-auto flex max-w-5xl gap-3">
                <a href="{{ route('siswa.profile') }}" class="{{ $btnGhost }} flex-1 !px-4">
                    Batal
                </a>
                <button type="submit" data-submit class="{{ $btnPrimary }} flex-[2] !px-4">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span data-label>Simpan</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    var MAX_SIZE = 2 * 1024 * 1024; // 2 MB
    var form = document.getElementById('profile-form');
    var fileInput = document.getElementById('foto');
    var preview = document.getElementById('photo-preview');
    var photoError = document.getElementById('photo-error');
    var originalSrc = preview.src;

    // Pratinjau foto + validasi ukuran
    fileInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        photoError.classList.add('hidden');
        if (!file) return;

        if (file.size > MAX_SIZE) {
            photoError.textContent = 'Ukuran foto melebihi 2 MB. Pilih foto yang lebih kecil.';
            photoError.classList.remove('hidden');
            this.value = '';
            preview.src = originalSrc;
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) { preview.src = e.target.result; };
        reader.readAsDataURL(file);
    });

    // Tampilkan / sembunyikan PIN
    document.querySelectorAll('[data-toggle-pin]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.togglePin);
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.querySelector('.material-symbols-outlined').textContent = show ? 'visibility_off' : 'visibility';
            this.setAttribute('aria-label', show ? 'Sembunyikan PIN' : 'Tampilkan PIN');
        });
    });

    // Hanya angka untuk PIN
    ['pin', 'pin_confirmation'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            checkPin();
        });
    });

    var pin = document.getElementById('pin');
    var pinConfirm = document.getElementById('pin_confirmation');
    var pinMatch = document.getElementById('pin-match');

    function checkPin() {
        var mismatch = pinConfirm.value.length > 0 && pin.value !== pinConfirm.value;
        pinMatch.classList.toggle('hidden', !mismatch);
        return !mismatch;
    }

    // Cegah kirim ganda + cek PIN sebelum submit
    var spinner = '<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
        '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>' +
        '<path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"></path></svg>';
    var submitButtons = form.querySelectorAll('[data-submit]');

    submitButtons.forEach(function (b) { b.dataset.original = b.innerHTML; });

    function resetButtons() {
        submitButtons.forEach(function (b) {
            b.disabled = false;
            b.innerHTML = b.dataset.original;
        });
    }

    form.addEventListener('submit', function (e) {
        if (!checkPin()) {
            e.preventDefault();
            pinConfirm.focus();
            return;
        }
        // Tunda sedikit agar tombol yang diklik tetap ikut terkirim sebelum dinonaktifkan
        setTimeout(function () {
            submitButtons.forEach(function (b) {
                b.disabled = true;
                b.innerHTML = spinner + '<span>Menyimpan…</span>';
            });
        }, 0);
    });

    // Pulihkan tombol jika pengguna kembali ke halaman ini lewat tombol Back
    window.addEventListener('pageshow', function (e) { if (e.persisted) resetButtons(); });
})();
</script>
@endsection