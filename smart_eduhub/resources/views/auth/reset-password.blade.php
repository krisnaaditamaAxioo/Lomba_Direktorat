@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="w-full max-w-md space-y-10">
    <!-- GRADIENT HEADER BLOCK (Fintech Style - like dashboard) -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] pt-8 pb-12 px-6 text-white overflow-hidden shadow-2xl shadow-orange-950/10 rounded-b-[2.5rem]">
        <!-- Abstract Circular Glow Overlays -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-8 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 space-y-5">
            <!-- Branding & Stat Pill -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">school</span>
                    </div>
                    <span class="text-base font-black tracking-tight">SEH</span>
                </div>
                <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
                    <span>Baru</span>
                </div>
            </div>

            <!-- Title Center -->
            <div class="text-center py-1 space-y-1">
                <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">Portal Administrator</p>
                <h2 class="text-2xl lg:text-3xl font-black tracking-tight">Reset Password</h2>
                <p class="text-xs text-orange-100/70 font-medium">Masukkan password baru Anda di bawah ini.</p>
            </div>
        </div>
    </div>

    <form class="space-y-6" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="space-y-2">
            <label class="font-label-md text-label-md text-on-surface flex items-center justify-between" for="email">
                Email Address
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-xl">mail</span>
                </div>
                <input class="block w-full pl-11 pr-4 py-3.5 bg-white border border-outline-variant rounded-xl font-body-md text-on-surface placeholder:text-outline/60 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none @error('email') border-error @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus type="email"/>
            </div>
            @error('email')
                <p class="text-error text-label-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="font-label-md text-label-md text-on-surface flex items-center justify-between" for="password">
                New Password
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <input class="block w-full pl-11 pr-10 py-3.5 bg-white border border-outline-variant rounded-xl font-body-md text-on-surface placeholder:text-outline/60 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none @error('password') border-error @enderror" id="password" name="password" placeholder="Enter new password" required type="password"/>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button class="text-outline hover:text-on-surface transition-colors" type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                    </button>
                </div>
            </div>
            @error('password')
                <p class="text-error text-label-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="font-label-md text-label-md text-on-surface flex items-center justify-between" for="password_confirmation">
                Confirm Password
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <input class="block w-full pl-11 pr-10 py-3.5 bg-white border border-outline-variant rounded-xl font-body-md text-on-surface placeholder:text-outline/60 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required type="password"/>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button class="text-outline hover:text-on-surface transition-colors" type="button" onclick="const p = document.getElementById('password_confirmation'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                    </button>
                </div>
            </div>
        </div>

        <button class="w-full py-4 px-6 bg-primary text-white font-headline-md text-headline-md rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2" type="submit">
            Reset Password
            <span class="material-symbols-outlined">arrow_forward</span>
        </button>
    </form>

    <div class="pt-4 flex flex-col items-center">
        <a class="inline-flex items-center gap-2 font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors group" href="{{ route('login') }}">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Back to Login
        </a>
    </div>

    <div class="pt-12 grid grid-cols-3 gap-4 items-center">
        <div class="h-px bg-outline-variant/50"></div>
        <span class="text-center font-label-sm text-label-sm text-outline uppercase tracking-widest">EduAdmin Portal</span>
        <div class="h-px bg-outline-variant/50"></div>
    </div>
</div>
@endsection
