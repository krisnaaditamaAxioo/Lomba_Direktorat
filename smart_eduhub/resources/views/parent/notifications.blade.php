@extends('layouts.parent')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div><h1 class="text-2xl font-black text-slate-900">Notifikasi</h1><p class="text-sm text-slate-500 mt-1">Informasi terbaru dari sekolah untuk Anda.</p></div>
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div class="p-5 rounded-2xl border {{ $notification->read ? 'bg-white border-slate-100' : 'bg-emerald-50 border-emerald-100' }} flex items-start justify-between gap-4">
                <div class="flex items-start gap-3"><span class="material-symbols-outlined text-emerald-600">{{ $notification->type === 'letter' ? 'description' : 'notifications' }}</span><div><h3 class="font-black text-slate-900">{{ $notification->title }}</h3><p class="text-sm text-slate-600 mt-1">{{ $notification->message }}</p><p class="text-[11px] text-slate-400 mt-2">{{ $notification->created_at->format('d M Y H:i') }}</p></div></div>
                @if(!$notification->read)<form method="POST" action="{{ route('parent.notifications.read', $notification) }}">@csrf<button class="text-xs font-bold text-emerald-700 whitespace-nowrap">Tandai dibaca</button></form>@endif
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200"><span class="material-symbols-outlined text-4xl text-slate-300">notifications_off</span><p class="text-sm text-slate-400 mt-2">Belum ada notifikasi.</p></div>
        @endforelse
    </div>
    {{ $notifications->links() }}
</div>
@endsection