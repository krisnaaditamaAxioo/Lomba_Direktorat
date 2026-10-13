@props([
    'title' => '',
    'eyebrow' => null,
    'subtitle' => null,
    'pill' => null,
    'icon' => 'school',
])

<!-- Gradient Header Block (Fintech Style) - Full Width on Mobile, Rounded Card on Desktop -->
<div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-4 pt-8 pb-14 px-6 text-white overflow-hidden shadow-2xl shadow-orange-950/10 rounded-b-[2.5rem] lg:mx-0 lg:rounded-[2.5rem] lg:pt-6 lg:pb-8">
    <!-- Abstract Circular Glow Overlays -->
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute -bottom-8 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

    <div class="relative z-10 space-y-5">
        <!-- Branding & Stat Pill -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">{{ $icon }}</span>
                </div>
                <span class="text-base font-black tracking-tight">SEH</span>
            </div>
            @if($pill)
            <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                {!! $pill !!}
            </div>
            @endif
        </div>

        <!-- Title Center -->
        <div class="text-center py-1 space-y-1">
            @if($eyebrow)
            <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">{{ $eyebrow }}</p>
            @endif
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight">{{ $title }}</h2>
            @if($subtitle)
            <p class="text-xs text-orange-100/70 font-medium">{!! $subtitle !!}</p>
            @endif
        </div>
    </div>
</div>
