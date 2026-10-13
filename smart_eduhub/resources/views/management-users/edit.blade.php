@extends('layouts.app')

@section('title', 'Edit Management User')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <nav class="flex items-center space-x-2 text-outline mb-2">
            <a href="{{ route('management-users.index') }}" class="font-label-sm text-label-sm hover:text-primary transition-colors">Management Users</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="font-label-sm text-label-sm text-primary">Edit User</span>
        </nav>
        <h2 class="font-display-xl text-display-xl text-on-surface">Edit User: {{ $managementUser->name }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Update the account details for this user.</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-3xl custom-shadow border border-slate-100 overflow-hidden">
        <form action="{{ route('management-users.update', $managementUser) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-bold text-slate-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $managementUser->name) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('name') border-error @enderror" required>
                    @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-bold text-slate-700">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $managementUser->username) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('username') border-error @enderror" required>
                        @error('username') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-bold text-slate-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $managementUser->email) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('email') border-error @enderror" required>
                        @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-bold text-slate-700">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" id="password" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('password') border-error @enderror">
                        @error('password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="role" class="block text-sm font-bold text-slate-700">Role</label>
                        <select name="role" id="role" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('role') border-error @enderror" required>
                            <option value="admin" {{ old('role', $managementUser->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="guru" {{ old('role', $managementUser->role) == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="wali_kelas" {{ old('role', $managementUser->role) == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                            <option value="wakasis" {{ old('role', $managementUser->role) == 'wakasis' ? 'selected' : '' }}>Waka Kesiswaan</option>
                            <option value="bk" {{ old('role', $managementUser->role) == 'bk' ? 'selected' : '' }}>BK (Bimbingan Konseling)</option>
                            <option value="teller" {{ old('role', $managementUser->role) == 'teller' ? 'selected' : '' }}>Teller</option>
                            <option value="bums" {{ old('role', $managementUser->role) == 'bums' ? 'selected' : '' }}>BUMS</option>
                        </select>
                        @error('role') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="no_wa" class="block text-sm font-bold text-slate-700">WhatsApp Number</label>
                    <input type="text" name="no_wa" id="no_wa" value="{{ old('no_wa', $managementUser->no_wa) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('no_wa') border-error @enderror">
                    @error('no_wa') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 flex items-center justify-end space-x-3 border-t border-slate-50">
                <a href="{{ route('management-users.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="bg-primary text-white font-bold text-sm px-10 py-3 rounded-xl custom-shadow hover:translate-y-[-1px] active:scale-95 transition-all shadow-orange-200/50">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
