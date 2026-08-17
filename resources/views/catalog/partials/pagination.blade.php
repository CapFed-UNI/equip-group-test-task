@if($products->lastPage() > 1)
    <nav class="mt-4" aria-label="Страницы каталога">
        <ul class="pagination justify-content-end flex-wrap mb-0">
            <li class="page-item disabled"><span class="page-link">Страница:</span></li>
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                <li class="page-item {{ $page === $products->currentPage() ? 'active' : '' }}">
                    <a class="page-link" data-catalog-ajax href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
@endif
