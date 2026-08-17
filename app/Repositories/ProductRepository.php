<?php

namespace App\Repositories;

use App\Enums\ProductSort;
use App\Models\Product;
use App\Support\CatalogQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{
    /**
     * @param  list<int>|null  $groupIds  null — весь каталог
     */
    public function paginate(?array $groupIds, CatalogQuery $query): LengthAwarePaginator
    {
        $builder = Product::query()
            ->select('products.*')
            ->join('prices', 'prices.id_product', '=', 'products.id')
            ->addSelect('prices.price as listed_price');

        if ($groupIds !== null) {
            $builder->whereIn('products.id_group', $groupIds);
        }

        $this->applySort($builder, $query->sort);

        return $builder
            ->paginate($query->perPage, ['*'], 'page', $query->page)
            ->withQueryString();
    }

    private function applySort(Builder $builder, ProductSort $sort): void
    {
        match ($sort) {
            ProductSort::PriceAsc => $builder->orderBy('prices.price')->orderBy('products.id'),
            ProductSort::PriceDesc => $builder->orderByDesc('prices.price')->orderBy('products.id'),
            ProductSort::NameAsc => $builder->orderBy('products.name')->orderBy('products.id'),
            ProductSort::NameDesc => $builder->orderByDesc('products.name')->orderBy('products.id'),
            ProductSort::Default => $builder->orderBy('products.id'),
        };
    }
}
