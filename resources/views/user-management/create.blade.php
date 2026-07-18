@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('user-management.index') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Tambah User</h1>
    </div>

    <form action="{{ route('user-management.store') }}" method="POST" x-data="{ role: 'user' }" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
            <input type="text" name="name" required value="{{ old('name') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" required value="{{ old('email') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-slate-400">Minimal 8 karakter, kombinasi huruf &amp; angka</p>
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
            <select name="role" x-model="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="user">Karyawan (akses terbatas ke ORG_CODE sendiri)</option>
                <option value="admin">Admin (akses penuh &amp; global)</option>
            </select>
        </div>
        <div x-show="role === 'user'" x-cloak>
            <label class="mb-1 block text-sm font-medium text-slate-700">ORG_CODE <span class="text-red-500">*</span></label>
            <input type="text" name="org_code" list="list-org-code" value="{{ old('org_code') }}"
                   placeholder="Kode cabang/wilayah, mis. 703010"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <datalist id="list-org-code">
                @foreach ($orgCodes as $code)
                    <option value="{{ $code }}"></option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-slate-400">Wajib buat Karyawan — nentuin data cabang mana yang bisa mereka akses</p>
            @error('org_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end border-t border-slate-100 pt-4">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Buat Akun
            </button>
        </div>
    </form>
</div>
@endsection
