<nav aria-label="Хлебные крошки" class="mb-3">
    <ol class="breadcrumb mb-0">
        @foreach($breadcrumbs as $crumb)
            @if($loop->last && ($currentGroup ?? null))
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['title'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
