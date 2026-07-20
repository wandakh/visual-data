<div class="space-y-4">
    <div>
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Info Umum</p>
        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-4">
            <div><span class="font-semibold text-slate-500">ID Data:</span> #{{ $item->id }}</div>
            <div><span class="font-semibold text-slate-500">Hari:</span> {{ $item->Hari }}</div>
            <div><span class="font-semibold text-slate-500">Bulan:</span> {{ $item->Bulan }}</div>
            <div><span class="font-semibold text-slate-500">Ket Produk:</span> {{ $item->KET_PROD }}</div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-3">
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Produk &amp; Mitra</p>
        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-4">
            <div><span class="font-semibold text-slate-500">Type Mitra:</span> {{ $item->TYPE_MITRA }}</div>
            <div><span class="font-semibold text-slate-500">Amount Fix:</span> {{ number_format((float) $item->AMMOUNT_FIX, 0, ',', '.') }}</div>
            <div><span class="font-semibold text-slate-500">Produk Fix:</span> {{ $item->PRODUK_FIX }}</div>
            <div><span class="font-semibold text-slate-500">Bucket Name:</span> {{ $item->BUCKET_NAME }}</div>
            <div><span class="font-semibold text-slate-500">Type Produk:</span> {{ $item->Type_Produk }}</div>
            <div><span class="font-semibold text-slate-500">Type Bisnis:</span> {{ $item->TYPE_BISNIS }}</div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-3">
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pajak</p>
        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-4">
            <div><span class="font-semibold text-slate-500">Rev In PPN:</span> Rp {{ number_format((float) $item->REV_INPPN, 0, ',', '.') }}</div>
            <div><span class="font-semibold text-slate-500">Pajak:</span> {{ $item->PAJAK }}</div>
            <div><span class="font-semibold text-slate-500">Rev Ex PPN:</span> Rp {{ number_format((float) $item->REV_EXPPN, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Data sensitif (HPP & Margin) cuma keliatan buat yang punya permission view-margin --}}
    @can('view-margin')
        <div class="border-t border-slate-200 pt-3">
            <p class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-amber-600">
                @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-3 w-3'])
                <span title="Cuma admin yang bisa lihat bagian ini">Finansial (Rahasia)</span>
            </p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-5">
                <div><span class="font-semibold text-slate-500">HPP:</span> Rp {{ number_format((float) $item->HPP, 0, ',', '.') }}</div>
                <div><span class="font-semibold text-slate-500">Total HPP In PPN:</span> Rp {{ number_format((float) $item->TOTAL_HPP_INPPN, 0, ',', '.') }}</div>
                <div><span class="font-semibold text-slate-500">Total HPP Ex PPN:</span> Rp {{ number_format((float) $item->TOTAL_HPP_EXPPN, 0, ',', '.') }}</div>
                <div><span class="font-semibold text-emerald-600">Margin In PPN:</span> Rp {{ number_format((float) $item->Margin_INPPN, 0, ',', '.') }}</div>
                <div><span class="font-semibold text-emerald-600">Margin Ex PPN:</span> Rp {{ number_format((float) $item->Margin_EXPPN, 0, ',', '.') }}</div>
            </div>
        </div>
    @endcan
</div>
