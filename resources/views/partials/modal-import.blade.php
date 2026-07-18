<div x-data="{ open: {{ $errors->has('excel_file') ? 'true' : 'false' }} }" @open-modal.window="if ($event.detail === 'import-modal') open = true"
     x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div @click.outside="open = false" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-slate-800">Import Data (Excel)</h2>
            <button type="button" @click="open = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
            </button>
        </div>
        <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-400">Maksimal 100MB. Format: .xlsx, .xls, atau .csv</p>
                @error('excel_file')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" data-loading-text="Mengimport... (bisa beberapa menit)" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    @include('partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4'])
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
