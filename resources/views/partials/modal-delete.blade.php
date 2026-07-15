<div x-show="deleteTarget !== null" x-cloak x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div @click.outside="deleteTarget = null" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-slate-800">Hapus Data</h2>
            <button type="button" @click="deleteTarget = null" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
            </button>
        </div>

        <p class="mb-4 text-sm text-slate-600">
            Hapus data #<span x-text="deleteTarget?.id"></span> milik <span class="font-medium" x-text="deleteTarget?.nama"></span>?
            Data masih bisa dipulihkan dalam 24 jam lewat menu Data Terhapus.
        </p>

        <form :action="deleteTarget ? '/delete/' + deleteTarget.id : ''" method="POST" x-data="{ reason: '', detail: '' }">
            @csrf
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Alasan Hapus <span class="text-red-500">*</span></label>
                @foreach (\App\Models\SalesRecord::ALASAN_HAPUS as $value => $label)
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50">
                        <input type="radio" name="reason" value="{{ $value }}" x-model="reason" required class="text-indigo-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>

            <div class="mt-3" x-show="reason === 'lainnya'" x-cloak>
                <label class="mb-1 block text-sm font-medium text-slate-700">Detail Alasan</label>
                <input type="text" name="reason_detail" x-model="detail" placeholder="Jelasin alasannya..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="deleteTarget = null" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" :disabled="!reason" :class="!reason && 'opacity-50 cursor-not-allowed'"
                        class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    @include('partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                    Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>
