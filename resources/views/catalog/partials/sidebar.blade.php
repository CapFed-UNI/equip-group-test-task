@if($nodes === [])
    <p class="text-muted small mb-0">Нет групп</p>
@else
    <ul class="list-unstyled catalog-tree mb-0">
        @foreach($nodes as $node)
            <li class="mb-1">
                @php
                    $classes = 'catalog-tree__link';
                    if ($node['active']) {
                        $classes .= ' catalog-tree__link--active';
                    } elseif ($node['on_path']) {
                        $classes .= ' catalog-tree__link--path';
                    }
                @endphp
                <a class="{{ $classes }}"
                   href="{{ route('catalog.group', array_merge(['group' => $node['group']], $query->toQuery(['page' => null]))) }}">
                    {{ $node['group']->name }}
                </a>
                <span class="text-muted">({{ $node['count'] }})</span>

                @if($node['children'] !== [])
                    <div class="ms-3 mt-1">
                        @include('catalog.partials.sidebar', ['nodes' => $node['children'], 'query' => $query])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endif
