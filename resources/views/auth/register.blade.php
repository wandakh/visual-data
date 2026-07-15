@extends('layouts.guest')

@section('heading', 'Buat akun baru')

@section('content')
    <form action="/sesi/create" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
            <input type="text" name="name" required autofocus value="{{ old('name') }}"
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
        <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Register
        </button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-500">
        Sudah punya akun? <a href="/sesi" class="font-medium text-indigo-600 hover:underline">Login</a>
    </p>
@endsection
