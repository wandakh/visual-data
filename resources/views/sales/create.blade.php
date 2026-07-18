@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Tambah Data</h1>
    </div>

    <div x-data="{
            draft: null,
            init() {
                const saved = localStorage.getItem('autosave:tambah-data:{{ auth()->id() }}');
                if (saved) this.draft = JSON.parse(saved);
            },
            pulihkan() {
                const form = document.querySelector('form[data-autosave=\'tambah-data\']');
                Object.entries(this.draft).forEach(([key, value]) => {
                    const field = form.querySelector(`[name=\'${key}\']`);
                    if (field) {
                        field.value = value;
                        // Dispatch event 'input' biar x-model Alpine (dipakai
                        // field Nama Customer buat nyesuain dropdown lain)
                        // ikut ke-update, bukan cuma nilai HTML-nya doang.
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                localStorage.removeItem('autosave:tambah-data:{{ auth()->id() }}');
                this.draft = null;
            },
            buang() {
                localStorage.removeItem('autosave:tambah-data:{{ auth()->id() }}');
                this.draft = null;
            }
        }"
    >
        <div x-show="draft" x-cloak class="mb-4 flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 ring-1 ring-amber-100">
            <span>Ada draft tersimpan dari sesi sebelumnya (kena auto-logout sebelum sempat disimpan).</span>
            <div class="flex gap-2">
                <button type="button" @click="pulihkan()" class="rounded-lg bg-amber-600 px-3 py-1 text-xs font-medium text-white hover:bg-amber-700">Pulihkan</button>
                <button type="button" @click="buang()" class="rounded-lg px-3 py-1 text-xs text-amber-700 hover:bg-amber-100">Buang</button>
            </div>
        </div>

        <form action="{{ route('insertdata') }}" method="POST" data-autosave="tambah-data" class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            @csrf
            @include('partials.sales-form-fields', ['dropdownData' => $dropdownData, 'lockedOrgCode' => $lockedOrgCode])
            <div class="flex justify-end border-t border-slate-100 pt-4">
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    @include('partials.icon', ['name' => 'plus-circle', 'class' => 'h-4 w-4'])
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
