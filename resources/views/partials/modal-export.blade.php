<div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'export-modal') open = true"
     x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div @click.outside="open = false" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-slate-800">Export Data</h2>
            <button type="button" @click="open = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
            </button>
        </div>
        <form action="{{ route('database.export') }}" method="GET" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama Customer</label>
                <select name="filter_export" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Customer</option>
                    @foreach ($dropdown['NAMA_CUSTOMER'] as $nama)
                        <option value="{{ $nama }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Tipe Export</label>
                <select name="export_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="semua">Semua Kolom</option>
                    <option value="summary">Summary</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    @include('partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                    Export
                </button>
            </div>
        </form>
    </div>
</div>
