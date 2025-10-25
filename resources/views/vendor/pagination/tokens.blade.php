@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation">
        <ul class="pagination-list">
            <li class="pagination-item">
                @if ($paginator->onFirstPage())
                    <span class="pagination-link disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="pagination-icon">&lsaquo;</span>
                    </span>
                @else
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <span class="pagination-icon">&lsaquo;</span>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination-item">
                        <span class="pagination-ellipsis">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="pagination-item">
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-link active" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li class="pagination-item">
                @if ($paginator->hasMorePages())
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <span class="pagination-icon">&rsaquo;</span>
                    </a>
                @else
                    <span class="pagination-link disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="pagination-icon">&rsaquo;</span>
                    </span>
                @endif
            </li>
        </ul>
        <div class="pagination-summary">
            Menampilkan
            <span>{{ $paginator->firstItem() }}</span>
            –
            <span>{{ $paginator->lastItem() }}</span>
            dari
            <span>{{ $paginator->total() }}</span>
            token
        </div>
    </nav>
@endif
