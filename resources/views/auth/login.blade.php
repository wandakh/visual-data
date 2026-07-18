@extends('layouts.guest')

@section('heading', 'Masuk ke akun kamu')

@section('content')
    @if (session('berhasil'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('berhasil') }}</div>
    @endif
    @if (session('loginError'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('loginError') }}</div>
    @endif
    @if (session('middleware'))
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('middleware') }}</div>
    @endif
    @if (session('guest'))
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ session('guest') }}</div>
    @endif

    <form action="/sesi/login" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" required autofocus value="{{ old('email') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Login
        </button>
    </form>
@endsection
