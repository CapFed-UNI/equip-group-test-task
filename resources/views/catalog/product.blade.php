@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @include('catalog.partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'currentGroup' => null])

    <article class="col-lg-8 px-0">
        <h1 class="h2 mb-3">{{ $product->name }}</h1>
        <p class="fs-4 fw-semibold mb-0">Цена: {{ $product->formattedPrice() }}</p>
    </article>
@endsection
