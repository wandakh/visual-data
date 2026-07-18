@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="flex h-8 w-8 items-center justify-center text-sm text-slate-300">&hellip;</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-sm text-slate-600 transition hover:bg-slate-100">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                <span class="rotate-180 inline-block">@include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])</span>
            </a>
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-300">
                <span class="rotate-180 inline-block">@include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])</span>
            </span>
        @endif
    </nav>
@endif
