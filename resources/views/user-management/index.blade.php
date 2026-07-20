@extends('layouts.app')

@section('content')
<!-- Tambahkan x-data untuk menangkap 2 target modal -->
<div class="space-y-6" x-data="{ deactivateTarget: null, reactivateTarget: null }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
                @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
                Kembali
            </a>
            <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Kelola Pengguna Sistem</h1>
        </div>
        <a href="{{ route('user-management.create') }}" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            @include('partials.icon', ['name' => 'plus-circle', 'class' => 'h-4 w-4'])
            Tambah User
        </a>
    </div>

    <!-- CARD 1: USER AKTIF -->
    <div>
        <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800">
            @include('partials.icon', ['name' => 'users', 'class' => 'h-4 w-4 text-indigo-800'])
            Daftar User Aktif
        </h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">ORG_CODE</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($activeUsers as $u)
                            <tr class="hover:bg-slate-50/70" x-data="{ editing: false, orgCode: @js($u->org_code) }">
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $u->hasRole('admin') ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $u->hasRole('admin') ? 'Admin' : 'Karyawan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($u->hasRole('admin'))
                                        <span class="text-slate-400" title="Admin akses semua ORG_CODE, gak dibatasi cabang tertentu">&mdash; (global)</span>
                                    @else
                                        <div x-show="!editing" class="flex items-center gap-2">
                                            <span class="{{ !$u->org_code ? 'text-red-600 font-medium' : 'text-slate-700' }}">
                                                {{ $u->org_code ?? 'BELUM DI-SET' }}
                                            </span>
                                            <button type="button" @click="editing = true" class="text-xs text-indigo-600 hover:underline">Edit</button>
                                        </div>
                                        <form x-show="editing" x-cloak action="{{ route('user-management.org-code', $u->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="org_code" x-model="orgCode" class="w-28 rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                            <button type="submit" class="text-xs font-medium text-green-600 hover:underline">Simpan</button>
                                            <button type="button" @click="editing = false" class="text-xs text-slate-400 hover:underline">Batal</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Aktif</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($u->id !== auth()->id())
                                        <!-- Tombol Pemicu Modal Nonaktifkan -->
                                        <button type="button" 
                                                @click="deactivateTarget = { url: '{{ route('user-management.deactivate', $u->id) }}', nama: @js($u->name) }"
                                                class="ml-auto flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100">
                                            @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-3.5 w-3.5'])
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">(akun kamu)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">@include('partials.empty-state', ['icon' => 'user', 'title' => 'Belum ada user aktif'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activeUsers->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">{{ $activeUsers->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </div>

    <!-- CARD 2: USER NONAKTIF -->
    <div>
        <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800">
            @include('partials.icon', ['name' => 'user-minus', 'class' => 'h-4 w-4 text-slate-800'])
            Daftar User Nonaktif
        </h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">ORG_CODE</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inactiveUsers as $u)
                            <tr class="hover:bg-slate-50/70 bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-700 opacity-75">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-slate-500 opacity-75">{{ $u->email }}</td>
                                <td class="px-4 py-3 opacity-75">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $u->hasRole('admin') ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $u->hasRole('admin') ? 'Admin' : 'Karyawan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 opacity-75">
                                    @if ($u->hasRole('admin'))
                                        <span class="text-slate-400">&mdash; (global)</span>
                                    @else
                                        <span class="text-slate-700">{{ $u->org_code ?? 'BELUM DI-SET' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-medium text-slate-600">Nonaktif</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <!-- Tombol Pemicu Modal Aktifkan -->
                                    <button type="button" 
                                            @click="reactivateTarget = { url: '{{ route('user-management.reactivate', $u->id) }}', nama: @js($u->name) }"
                                            class="ml-auto flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">
                                        @include('partials.icon', ['name' => 'restore', 'class' => 'h-3.5 w-3.5'])
                                        Aktifkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">@include('partials.empty-state', ['icon' => 'user', 'title' => 'Gak ada user yang dinonaktifkan'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($inactiveUsers->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">{{ $inactiveUsers->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 1: NONAKTIFKAN USER -->
    <!-- ============================================== -->
    <div x-show="deactivateTarget" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div @click.outside="deactivateTarget = null" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-display text-lg font-bold text-slate-800">Konfirmasi Nonaktifkan User</h2>
                <button type="button" @click="deactivateTarget = null" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                    @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
                </button>
            </div>
            <form :action="deactivateTarget?.url" method="POST" class="space-y-4">
                @csrf
                <div>
                    <p class="text-sm text-slate-600">
                        Apakah kamu yakin ingin menonaktifkan akun <span class="font-bold text-slate-800" x-text="deactivateTarget?.nama"></span>? 
                        Mereka gak bisa login lagi, tapi riwayat log tetap tersimpan di dalam sistem.
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="deactivateTarget = null" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-4 w-4'])
                        Ya, Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 2: AKTIFKAN KEMBALI USER -->
    <!-- ============================================== -->
    <div x-show="reactivateTarget" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div @click.outside="reactivateTarget = null" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-display text-lg font-bold text-slate-800">Konfirmasi Aktifkan User</h2>
                <button type="button" @click="reactivateTarget = null" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                    @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-5 w-5'])
                </button>
            </div>
            <form :action="reactivateTarget?.url" method="POST" class="space-y-4">
                @csrf
                <div>
                    <p class="text-sm text-slate-600">
                        Apakah kamu yakin ingin mengaktifkan kembali akun <span class="font-bold text-slate-800" x-text="reactivateTarget?.nama"></span>? 
                        User ini akan bisa login dan menggunakan sistem kembali.
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="reactivateTarget = null" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                        @include('partials.icon', ['name' => 'restore', 'class' => 'h-4 w-4'])
                        Ya, Aktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection