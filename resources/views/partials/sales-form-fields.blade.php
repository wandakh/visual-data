@php
    $fields = [
        'ORG_CODE' => 'Org Code',
        'KODE_PRODUK' => 'Kode Produk',
        'AMMOUNT' => 'Amount',
        'HARGA_JUAL' => 'Harga Jual',
        'TRX' => 'TRX',
        'TYPE_MITRA' => 'Type Mitra',
        'AMMOUNT_FIX' => 'Amount Fix',
        'PRODUK_FIX' => 'Produk Fix',
        'BUCKET_NAME' => 'Bucket Name',
        'Type_Produk' => 'Type Produk',
        'TYPE_BISNIS' => 'Type Bisnis',
        'REV_INPPN' => 'Rev In PPN',
        'PAJAK' => 'Pajak',
        'REV_EXPPN' => 'Rev Ex PPN',
        'HPP' => 'HPP',
        'TOTAL_HPP_INPPN' => 'Total HPP In PPN',
        'TOTAL_HPP_EXPPN' => 'Total HPP Ex PPN',
        'Margin_INPPN' => 'Margin In PPN',
        'Margin_EXPPN' => 'Margin Ex PPN',
        'Hari' => 'Hari',
        'Bulan' => 'Bulan',
        'KET_PROD' => 'Ket Produk',
    ];
@endphp

{{--
    Diperbaiki: Nama Customer sekarang jadi kolom PERTAMA yang diisi. Begitu
    dipilih/diketik, kolom-kolom lain otomatis nyesuain isinya (via AJAX ke
    /sales-record-options) ke pola yang biasa dipakai customer itu — soalnya
    tiap perusahaan biasanya punya kode/pola sendiri. Sebelum ada customer
    dipilih, dropdown nampilin semua pilihan yang pernah ada (dari semua
    customer). Field tetap <input>+<datalist>, jadi tetap bisa diisi nilai
    baru yang belum pernah ada.
--}}
<div
    x-data="{
        customer: @js(old('NAMA_CUSTOMER', $data->NAMA_CUSTOMER ?? '')),
        options: @js($dropdownData),
        loading: false,
        async fetchOptions() {
            this.loading = true;
            try {
                const url = '{{ route('sales-record-options') }}' + (this.customer ? ('?customer=' + encodeURIComponent(this.customer)) : '');
                const res = await fetch(url);
                this.options = await res.json();
            } catch (e) {
                console.error('Gagal ambil pilihan dropdown:', e);
            } finally {
                this.loading = false;
            }
        }
    }"
    x-init="if (customer) fetchOptions()"
    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">
            Nama Customer
            <span class="ml-1 text-xs font-normal text-indigo-600" x-show="loading" x-cloak>memuat pilihan&hellip;</span>
        </label>
        <input type="text" name="NAMA_CUSTOMER" list="list-NAMA_CUSTOMER" required
               x-model="customer" x-on:input.debounce.400ms="fetchOptions()"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        <datalist id="list-NAMA_CUSTOMER">
            @foreach ($dropdownData['NAMA_CUSTOMER'] ?? [] as $option)
                <option value="{{ $option }}"></option>
            @endforeach
        </datalist>
        <p class="mt-1 text-xs text-slate-400">Pilih/ketik dulu — kolom lain di bawah otomatis nyesuain pilihannya</p>
        @error('NAMA_CUSTOMER')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal</label>
        <input type="date" name="Tanggal" required
               value="{{ old('Tanggal', isset($data) ? \Carbon\Carbon::parse($data->Tanggal)->format('Y-m-d') : '') }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        @error('Tanggal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    @foreach ($fields as $column => $label)
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</label>
            @if ($column === 'ORG_CODE' && ($lockedOrgCode ?? null))
                <input type="text" value="{{ $lockedOrgCode }}" readonly
                       class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                <input type="hidden" name="ORG_CODE" value="{{ $lockedOrgCode }}">
                <p class="mt-1 text-xs text-slate-400">Terkunci ke ORG_CODE cabang kamu</p>
            @else
                <input type="text" name="{{ $column }}" list="list-{{ $column }}" required
                       value="{{ old($column, $data->$column ?? '') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <datalist id="list-{{ $column }}">
                    <template x-for="opt in (options['{{ $column }}'] || [])" :key="opt">
                        <option :value="opt"></option>
                    </template>
                </datalist>
                @error($column)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            @endif
        </div>
    @endforeach
</div>
