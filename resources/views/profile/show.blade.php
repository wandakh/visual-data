@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Profile</h1>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-100">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="bg-gradient-to-br from-[#0f1729] via-[#1a2540] to-indigo-900 px-6 pb-14 pt-8">
            <p class="text-xs font-medium uppercase tracking-wider text-indigo-300">Akun Saya</p>
        </div>
        <div class="flex flex-col items-center gap-2 px-6 pb-6 -mt-10">
            <img src="{{ auth()->user()->profilePhotoUrl() }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-white">
            <h2 class="mt-2 text-lg font-semibold text-slate-800">{{ auth()->user()->name }}</h2>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                {{ auth()->user()->getRoleNames()->first() ?? '-' }}
            </span>
        </div>

        <dl class="divide-y divide-slate-100 border-t border-slate-100 px-6 text-sm">
            <div class="flex justify-between py-3">
                <dt class="text-slate-500">Email</dt>
                <dd class="font-medium text-slate-800">{{ auth()->user()->email }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-slate-500">Bergabung</dt>
                <dd class="font-medium text-slate-800">{{ auth()->user()->created_at->format('d M Y') }}</dd>
            </div>
        </dl>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <h2 class="mb-4 text-sm font-semibold text-slate-600">Update Profile</h2>
        <form method="POST" action="/update/profile" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Foto</label>
                <input type="file" name="images" accept=".png,.jpg,.jpeg" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @error('images')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end border-t border-slate-100 pt-4">
                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
