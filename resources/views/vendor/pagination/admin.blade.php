@if ($paginator->hasPages())
<nav class="ad-page-nav" role="navigation" aria-label="Pagination">
  <p class="ad-page-meta">
    Showing
    <b>{{ $paginator->firstItem() ?? 0 }}</b>
    to
    <b>{{ $paginator->lastItem() ?? 0 }}</b>
    of
    <b>{{ $paginator->total() }}</b>
    results
  </p>
  <div class="ad-page-links">
    @if ($paginator->onFirstPage())
      <span class="ad-page-btn disabled" aria-disabled="true">&laquo;</span>
    @else
      <a class="ad-page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="ad-page-btn disabled">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="ad-page-btn active" aria-current="page">{{ $page }}</span>
          @else
            <a class="ad-page-btn" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a class="ad-page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
    @else
      <span class="ad-page-btn disabled" aria-disabled="true">&raquo;</span>
    @endif
  </div>
</nav>
@endif
