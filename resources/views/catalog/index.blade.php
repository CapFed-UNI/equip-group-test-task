@extends('layouts.app')

@section('title', $currentGroup->name ?? 'Каталог')

@section('content')
    @include('catalog.partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card">
                <div class="card-header fw-semibold">Группы товаров</div>
                <div class="card-body py-2">
                    @include('catalog.partials.sidebar', ['nodes' => $sidebar, 'query' => $query])
                </div>
            </div>
        </aside>

        <section class="col-lg-9">
            <h1 class="h3 mb-3">{{ $currentGroup->name ?? 'Все товары' }}</h1>

            @if($childGroups->isNotEmpty())
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach($childGroups as $child)
                        <a class="btn btn-outline-secondary btn-sm"
                           href="{{ route('catalog.group', array_merge(['group' => $child], $query->toQuery(['page' => null]))) }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div id="catalog-listing">
                @include('catalog.partials.listing')
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/catalog.js') }}"></script>
@endpush
