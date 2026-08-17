<?php

namespace Tests\Unit;

use App\Enums\ProductSort;
use App\Support\CatalogQuery;
use Illuminate\Http\Request;
use Tests\TestCase;

class CatalogQueryTest extends TestCase
{
    public function test_it_uses_defaults_for_missing_query_parameters(): void
    {
        $query = CatalogQuery::fromRequest(Request::create('/', 'GET'));

        $this->assertSame(ProductSort::Default, $query->sort);
        $this->assertSame(6, $query->perPage);
        $this->assertSame(1, $query->page);
        $this->assertSame([], $query->toQuery());
    }

    public function test_it_rejects_unknown_per_page_and_sort_values(): void
    {
        $query = CatalogQuery::fromRequest(Request::create('/', 'GET', [
            'sort' => 'unknown',
            'per_page' => 99,
            'page' => 0,
        ]));

        $this->assertSame(ProductSort::Default, $query->sort);
        $this->assertSame(6, $query->perPage);
        $this->assertSame(1, $query->page);
    }

    public function test_it_keeps_allowed_values_in_the_query_string(): void
    {
        $query = CatalogQuery::fromRequest(Request::create('/', 'GET', [
            'sort' => 'price_desc',
            'per_page' => 12,
            'page' => 3,
        ]));

        $this->assertSame(ProductSort::PriceDesc, $query->sort);
        $this->assertSame([
            'sort' => 'price_desc',
            'per_page' => 12,
            'page' => 3,
        ], $query->toQuery());
    }
}
