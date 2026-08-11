@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn pagination-prev disabled" aria-disabled="true">Previous Page</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn pagination-prev" rel="prev">Previous Page</a>
        @endif

        {{-- Center Group --}}
        <div class="pagination-center">
            {{-- Chevron Left --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-nav-item disabled" aria-disabled="true" aria-label="Previous">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-nav-item" rel="prev" aria-label="Previous">&lsaquo;</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="pagination-nav-item disabled" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-nav-item active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-nav-item">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Chevron Right --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-nav-item" rel="next" aria-label="Next">&rsaquo;</a>
            @else
                <span class="pagination-nav-item disabled" aria-disabled="true" aria-label="Next">&rsaquo;</span>
            @endif
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn pagination-next" rel="next">Next Page</a>
        @else
            <span class="pagination-btn pagination-next disabled" aria-disabled="true">Next Page</span>
        @endif
    </nav>
@endif
