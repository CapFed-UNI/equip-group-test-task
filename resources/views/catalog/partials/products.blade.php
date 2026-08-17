@if($products->isEmpty())
    <p class="text-muted">В этой группе пока нет товаров.</p>
@else
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 product-card">
                    <div class="card-body d-flex flex-column">
                        <a href="{{ route('catalog.product', $product) }}" class="stretched-link text-decoration-none mb-2">
                            {{ $product->name }}
                        </a>
                        <div class="mt-auto fw-semibold">{{ $product->formattedPrice() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @include('catalog.partials.pagination')
@endif
