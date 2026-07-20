{{--
    Badge status filter yang reusable & konsisten di semua halaman (Dashboard,
    Data Terhapus, Log Data, Log Login). Dipisah dari card filter, berdiri
    sendiri sebagai pill horizontal.

    Variabel yang perlu dikirim:
    - $showAll (bool)
    - $urlSemua   -> link buat "Tampilkan Semua"
    - $urlHariIni -> link buat "Kembali ke Hari Ini"
    - $labelHariIni (opsional, default "Menampilkan data hari ini")
    - $labelSemua (opsional, default "Menampilkan semua data")
--}}
<div class="inline-flex flex-wrap items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700">
    @include('partials.icon', ['name' => 'filter', 'class' => 'h-3.5 w-3.5'])
    <span>{{ $showAll ? ($labelSemua ?? 'Menampilkan semua data') : ($labelHariIni ?? 'Menampilkan data hari ini') }}</span>
    <span class="text-indigo-300">|</span>
    @if ($showAll)
        <a href="{{ $urlHariIni }}" class="underline hover:text-indigo-900">Kembali ke Hari Ini</a>
    @else
        <a href="{{ $urlSemua }}" class="underline hover:text-indigo-900">Tampilkan Semua</a>
    @endif
</div>
