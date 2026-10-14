@extends('layouts.parent')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div><h1 class="text-2xl font-black text-slate-900">Notifikasi</h1><p class="text-sm text-slate-500 mt-1">Informasi terbaru dari sekolah untuk Anda.</p></div>
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $type = $notification->type;
                if ($type === 'finance') {
                    $iconBox = 'bg-emerald-100 text-emerald-600';
                    $icon = 'account_balance_wallet';
                    $label = 'Keuangan';
                } elseif ($type === 'point') {
                    $iconBox = 'bg-amber-100 text-amber-600';
                    $icon = 'grade';
                    $label = 'Poin';
                } elseif ($type === 'admin_custom') {
                    $iconBox = 'bg-orange-100 text-orange-600';
                    $icon = 'campaign';
                    $label = 'Pengumuman';
                } else {
                    $iconBox = 'bg-slate-100 text-slate-500';
                    $icon = $type === 'letter' ? 'description' : 'notifications';
                    $label = ucfirst($type);
                }
            @endphp
            <div class="p-5 rounded-2xl border {{ $notification->read ? 'bg-white border-slate-100' : 'bg-emerald-50 border-emerald-100' }} flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 {{ $iconBox }}">
                        <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-slate-900">{{ $notification->title }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500">{{ $label }}</span>
                        </div>
                        <p class="text-sm text-slate-600 mt-1">{{ $notification->message }}</p>
                        <p class="text-[11px] text-slate-400 mt-2">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @if(!$notification->read)<form method="POST" action="{{ route('parent.notifications.read', $notification) }}">@csrf<button class="text-xs font-bold text-emerald-700 whitespace-nowrap">Tandai dibaca</button></form>@endif
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200"><span class="material-symbols-outlined text-4xl text-slate-300">notifications_off</span><p class="text-sm text-slate-400 mt-2">Belum ada notifikasi.</p></div>
        @endforelse
    </div>
    {{ $notifications->links() }}
</div>
@endsection