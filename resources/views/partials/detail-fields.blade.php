<div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-4">
    <div><span class="font-semibold text-slate-500">ID Data:</span> #{{ $item->id }}</div>
    <div><span class="font-semibold text-slate-500">Type Mitra:</span> {{ $item->TYPE_MITRA }}</div>
    <div><span class="font-semibold text-slate-500">Amount Fix:</span> {{ $item->AMMOUNT_FIX }}</div>
    <div><span class="font-semibold text-slate-500">Produk Fix:</span> {{ $item->PRODUK_FIX }}</div>
    <div><span class="font-semibold text-slate-500">Bucket Name:</span> {{ $item->BUCKET_NAME }}</div>
    <div><span class="font-semibold text-slate-500">Type Produk:</span> {{ $item->Type_Produk }}</div>
    <div><span class="font-semibold text-slate-500">Type Bisnis:</span> {{ $item->TYPE_BISNIS }}</div>
    <div><span class="font-semibold text-slate-500">Rev In PPN:</span> {{ $item->REV_INPPN }}</div>
    <div><span class="font-semibold text-slate-500">Pajak:</span> {{ $item->PAJAK }}</div>
    <div><span class="font-semibold text-slate-500">Rev Ex PPN:</span> {{ $item->REV_EXPPN }}</div>
    <div><span class="font-semibold text-slate-500">Hari:</span> {{ $item->Hari }}</div>
    <div><span class="font-semibold text-slate-500">Bulan:</span> {{ $item->Bulan }}</div>
    <div><span class="font-semibold text-slate-500">Ket Produk:</span> {{ $item->KET_PROD }}</div>
    {{-- Data sensitif (HPP & Margin) cuma keliatan buat yang punya permission view-margin --}}
    @can('view-margin')
        <div><span class="font-semibold text-slate-500">HPP:</span> {{ $item->HPP }}</div>
        <div><span class="font-semibold text-slate-500">Total HPP In PPN:</span> {{ $item->TOTAL_HPP_INPPN }}</div>
        <div><span class="font-semibold text-slate-500">Total HPP Ex PPN:</span> {{ $item->TOTAL_HPP_EXPPN }}</div>
        <div><span class="font-semibold text-slate-500">Margin In PPN:</span> {{ $item->Margin_INPPN }}</div>
        <div><span class="font-semibold text-slate-500">Margin Ex PPN:</span> {{ $item->Margin_EXPPN }}</div>
    @endcan
</div>
