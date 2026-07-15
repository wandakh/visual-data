@extends('layouts.guest')

@section('heading', 'Di Luar Jam Kerja')

@section('content')
    <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 ring-1 ring-amber-100">
        Akses ke sistem ini dibatasi jam <strong>07:00–18:00</strong> buat akun biasa.
        Sekarang jam <strong>{{ now()->format('H:i') }}</strong>.
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form action="{{ route('overtime.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Alasan Lembur</label>
            <textarea name="reason" rows="3" required placeholder="Jelasin kenapa perlu akses sekarang..."
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
            @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Durasi Tambahan</label>
            <select name="duration_minutes" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="30">30 menit</option>
                <option value="60">1 jam</option>
                <option value="120" selected>2 jam</option>
                <option value="240">4 jam</option>
            </select>
            @error('duration_minutes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Minta Akses Lembur
        </button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-500">
        <a href="/sesi/logout" class="font-medium text-slate-600 hover:underline">Logout aja</a>
    </p>
@endsection
