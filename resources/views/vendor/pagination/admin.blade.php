@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="admin-pagination-btn disabled" aria-disabled="true">Previous Page</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="admin-pagination-btn" rel="prev">Previous Page</a>
        @endif

        {{-- Center Group --}}
        <div class="admin-pagination-center">
            {{-- Chevron Left --}}
            @if ($paginator->onFirstPage())
                <span class="admin-pagination-nav-item disabled" aria-disabled="true" aria-label="Previous">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="admin-pagination-nav-item" rel="prev" aria-label="Previous">&lsaquo;</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="admin-pagination-nav-item disabled" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="admin-pagination-nav-item active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="admin-pagination-nav-item" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Chevron Right --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="admin-pagination-nav-item" rel="next" aria-label="Next">&rsaquo;</a>
            @else
                <span class="admin-pagination-nav-item disabled" aria-disabled="true" aria-label="Next">&rsaquo;</span>
            @endif
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="admin-pagination-btn" rel="next">Next Page</a>
        @else
            <span class="admin-pagination-btn disabled" aria-disabled="true">Next Page</span>
        @endif
    </nav>
@endif
