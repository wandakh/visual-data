<div x-show="restoreTarget" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div @click.outside="restoreTarget = null" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        
        <!-- Header Modal (Konsisten dengan Import) -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-slate-800">Konfirmasi Pemulihan</h2>
            <button type="button" @click="restoreTarget = null" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
            </button>
        </div>

        <!-- Body & Form (Konsisten dengan Import) -->
        <form :action="restoreTarget?.url" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <p class="text-sm text-slate-600">
                    Apakah kamu yakin ingin memulihkan data transaksi milik <span class="font-bold text-slate-800" x-text="restoreTarget?.nama"></span>? Data ini akan dikembalikan ke halaman utama.
                </p>
            </div>
            
            <!-- Footer Buttons (Konsisten dengan Import) -->
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="restoreTarget = null" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    @include('partials.icon', ['name' => 'restore', 'class' => 'h-4 w-4'])
                    Ya, Pulihkan
                </button>
            </div>
            
        </form>
    </div>
</div>