@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200">
                @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
                Kembali
            </a>
            <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Kelola User</h1>
        </div>
        <a href="{{ route('user-management.create') }}" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            @include('partials.icon', ['name' => 'plus-circle', 'class' => 'h-4 w-4'])
            Tambah User
        </a>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-100">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{{ session('error') }}</div>
    @endif

    <!-- CARD 1: USER AKTIF -->
    <div>
        <h2 class="mb-3 text-lg font-bold text-slate-800">User Aktif</h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">ORG_CODE</th>
                            <th class="px-4 py-3 text-left">Bergabung</th>
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
                                        <span class="text-slate-400">&mdash; (global)</span>
                                    @else
                                        <div x-show="!editing" class="flex items-center gap-2">
                                            <span class="{{ !$u->org_code ? 'text-red-600 font-medium' : 'text-slate-700' }}">
                                                {{ $u->org_code ?? 'BELUM DI-SET' }}
                                            </span>
                                            <button type="button" @click="editing = true" class="text-xs text-indigo-600 hover:underline">Edit</button>
                                        </div>
                                        <form x-show="editing" x-cloak action="{{ route('user-management.org-code', $u->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="org_code" x-model="orgCode" class="w-28 rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                            <button type="submit" class="text-xs font-medium text-green-600 hover:underline">Simpan</button>
                                            <button type="button" @click="editing = false" class="text-xs text-slate-400 hover:underline">Batal</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-4 py-3 tabular-nums text-slate-500">{{ $u->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if (!$u->hasRole('admin'))
                                                <form action="{{ route('user-management.deactivate', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan akun karyawan ini?');">                                            @method('DELETE')
                                            <button type="submit" class="ml-auto flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100">
                                                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-3.5 w-3.5'])
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Belum ada user aktif</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($activeUsers->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">{{ $activeUsers->appends(['inactive_page' => request('inactive_page')])->links() }}</div>
            @endif
        </div>
    </div>

    <!-- CARD 2: USER NONAKTIF -->
    <div>
        <h2 class="mb-3 text-lg font-bold text-slate-500">User Nonaktif</h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Dinonaktifkan Pada</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inactiveUsers as $u)
                            <tr class="hover:bg-slate-50/70 opacity-70 transition hover:opacity-100">
                                <td class="px-4 py-3 font-medium text-slate-600">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-medium text-slate-600">
                                        {{ $u->hasRole('admin') ? 'Admin' : 'Karyawan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 tabular-nums text-slate-500">{{ $u->deleted_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <!-- Pastikan route ini sesuai dengan route di web.php kamu (bisa restore atau reactivate) -->
                                    <form action="{{ route('user-management.restore', $u->id) }}" method="POST" onsubmit="return confirm('Aktifkan lagi akun {{ $u->name }}?');">
                                        @csrf
                                        <button type="submit" class="ml-auto flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-100">
                                            @include('partials.icon', ['name' => 'restore', 'class' => 'h-3.5 w-3.5'])
                                            Aktifkan Kembali
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Tidak ada akun yang dinonaktifkan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($inactiveUsers->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">{{ $inactiveUsers->appends(['active_page' => request('active_page')])->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection