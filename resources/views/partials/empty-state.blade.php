<div class="flex flex-col items-center justify-center gap-2 px-4 py-14 text-center">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-300">
        @include('partials.icon', ['name' => $icon ?? 'clipboard', 'class' => 'h-7 w-7'])
    </div>
    <p class="text-sm font-medium text-slate-500">{{ $title ?? 'Belum ada data' }}</p>
    @if (isset($subtitle))
        <p class="max-w-xs text-xs text-slate-400">{{ $subtitle }}</p>
    @endif
</div>
