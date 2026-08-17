@php
    $baseParams = $currentGroup
        ? ['group' => $currentGroup]
        : [];
    $routeName = $currentGroup ? 'catalog.group' : 'catalog.index';
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <span class="me-2">Сортировать:</span>
        @foreach(\App\Enums\ProductSort::publicOptions() as $option)
            @php
                $url = route($routeName, array_merge($baseParams, $query->toQuery([
                    'sort' => $option->value,
                    'page' => null,
                ])));
            @endphp
            <a class="me-2 {{ $query->sort === $option ? 'fw-semibold text-decoration-underline' : '' }}"
               data-catalog-ajax
               href="{{ $url }}">{{ $option->label() }}</a>
        @endforeach
    </div>

    <form class="d-flex align-items-center gap-2" method="get" action="{{ route($routeName, $baseParams) }}" data-catalog-per-page>
        @if($query->sort !== \App\Enums\ProductSort::Default)
            <input type="hidden" name="sort" value="{{ $query->sort->value }}">
        @endif
        <label class="form-label mb-0 text-nowrap" for="per-page">На странице:</label>
        <select class="form-select form-select-sm" id="per-page" name="per_page" style="width: auto;">
            @foreach(config('catalog.per_page_options') as $option)
                <option value="{{ $option }}" @selected($query->perPage === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </form>
</div>
