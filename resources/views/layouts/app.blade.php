<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visual Data | {{ $title ?? '' }}</title>
    <link rel="icon" href="{{ asset('images/kisel.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    @auth
        @if (!auth()->user()->hasRole('admin'))
            @php
                $sekarang = now();
                $jamSelesai = $sekarang->copy()->setTime(18, 0);
                $overtimeAktif = \App\Models\OvertimeRequest::activeFor(auth()->user());
                $batasAkses = $overtimeAktif ? $overtimeAktif->granted_until : $jamSelesai;
            @endphp
            @if ($sekarang->lt($batasAkses))
                <div
                    x-data="{
                        deadline: new Date({{ $batasAkses->timestamp * 1000 }}),
                        showWarning: false,
                        secondsLeft: 0,
                        tick() {
                            const diff = Math.floor((this.deadline - new Date()) / 1000);
                            this.secondsLeft = Math.max(0, diff);
                            this.showWarning = this.secondsLeft > 0 && this.secondsLeft <= 600;
                            if (this.secondsLeft <= 0) {
                                this.doAutoLogout();
                            }
                        },
                        doAutoLogout() {
                            const form = document.querySelector('form[data-autosave]');
                            if (form) {
                                const data = {};
                                new FormData(form).forEach((value, key) => data[key] = value);
                                localStorage.setItem('autosave:' + form.dataset.autosave + ':{{ auth()->id() }}', JSON.stringify(data));
                            }
                            window.location.href = '/sesi/logout';
                        }
                    }"
                    x-init="tick(); setInterval(() => tick(), 1000)"
                >
                    <div x-show="showWarning" x-cloak x-transition
                         class="fixed inset-x-0 top-0 z-[60] bg-amber-500 px-4 py-2 text-center text-sm font-medium text-white shadow-md">
                        Sesi kamu bakal berakhir otomatis dalam <span x-text="Math.floor(secondsLeft / 60) + ' menit ' + (secondsLeft % 60) + ' detik'"></span> (jam kerja habis). Simpan pekerjaan kamu sekarang.
                    </div>
                </div>
            @endif
        @endif
    @endauth
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-64 -translate-x-full transform flex-col bg-[#0f1729] text-slate-300 transition-transform duration-200 md:static md:translate-x-0"
            :class="sidebarOpen && '!translate-x-0'"
        >
            <div class="flex h-16 items-center gap-3 border-b border-white/5 px-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center">
                    <img src="{{ asset('images/kisel.png') }}" alt="Logo" class="h-full w-full object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="hidden h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-bold text-white">VD</span>
                </div>
                <div class="leading-tight">
                    <p class="font-display text-sm font-bold tracking-tight text-white">Visual Data</p>
                    <p class="text-[11px] text-slate-400">Sales Monitoring</p>
                </div>
            </div>

            <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
                @php $active = 'flex items-center gap-3 rounded-lg border-l-2 border-indigo-400 bg-white/5 px-3 py-2.5 text-sm font-medium text-white'; @endphp
                @php $inactive = 'flex items-center gap-3 rounded-lg border-l-2 border-transparent px-3 py-2.5 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white'; @endphp

                <a href="{{ url('/database') }}" class="{{ ($title ?? '') === 'Home' ? $active : $inactive }}">
                    @include('partials.icon', ['name' => 'dashboard', 'class' => 'h-5 w-5'])
                    Dashboard
                </a>
                @can('create-data')
                    <a href="{{ route('tambahdata') }}" class="{{ ($title ?? '') === 'Create Data' ? $active : $inactive }}">
                        @include('partials.icon', ['name' => 'plus-circle', 'class' => 'h-5 w-5'])
                        Tambah Data
                    </a>
                @endcan
                <a href="{{ route('diagram') }}" class="{{ ($title ?? '') === 'Diagram' ? $active : $inactive }}">
                    @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-5 w-5'])
                    Diagram
                </a>

                @can('delete-data')
                    <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Admin</p>
                    <a href="{{ route('database.trash') }}" class="{{ ($title ?? '') === 'Data Terhapus' ? $active : $inactive }}">
                        @include('partials.icon', ['name' => 'trash', 'class' => 'h-5 w-5'])
                        Data Terhapus
                    </a>
                    <a href="{{ route('activity-log') }}" class="{{ ($title ?? '') === 'Log Data' ? $active : $inactive }}">
                        @include('partials.icon', ['name' => 'clipboard', 'class' => 'h-5 w-5'])
                        Log Data
                    </a>
                    <a href="{{ route('user-activity-log') }}" class="{{ ($title ?? '') === 'Log Login & Export' ? $active : $inactive }}">
                        @include('partials.icon', ['name' => 'user', 'class' => 'h-5 w-5'])
                        Log Login &amp; Export
                    </a>
                @endcan
            </nav>

            @auth
                <div class="border-t border-white/5 p-3">
                    <div class="flex items-center gap-2 rounded-lg px-2 py-2">
                        <img src="{{ auth()->user()->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/10">
                        <div class="min-w-0 flex-1 leading-tight">
                            <p class="truncate text-xs font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-[11px] text-slate-400">{{ auth()->user()->getRoleNames()->first() ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endauth
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="fixed inset-0 z-20 bg-black/30 md:hidden"></div>

        <div class="flex flex-1 flex-col">
            <!-- Topbar -->
            <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
                <div class="flex items-center gap-3">
                    <button class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 md:hidden" @click="sidebarOpen = !sidebarOpen">
                        @include('partials.icon', ['name' => 'menu', 'class' => 'h-5 w-5'])
                    </button>
                    <h1 class="font-display text-base font-bold tracking-tight text-slate-800 sm:text-lg">{{ $title ?? '' }}</h1>
                </div>
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-50">
                            <img src="{{ auth()->user()->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-100">
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak x-transition
                             class="absolute right-0 z-40 mt-2 w-48 rounded-xl border border-slate-100 bg-white py-1.5 shadow-lg">
                            <a href="/profile" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4'])
                                Profile
                            </a>
                            <a href="/sesi/logout" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-slate-50">
                                @include('partials.icon', ['name' => 'logout', 'class' => 'h-4 w-4'])
                                Logout
                            </a>
                        </div>
                    </div>
                @endauth
            </header>

            <main class="flex-1 p-4 sm:p-6">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-400">
                &copy; Visual Data {{ date('Y') }}
            </footer>
        </div>
    </div>
</body>
</html>
