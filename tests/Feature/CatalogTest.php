<?php

namespace Tests\Feature;

use App\Enums\ProductSort;
use App\Repositories\ProductRepository;
use App\Support\CatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_root_groups_and_products(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $this->makeProduct($phones, 'Смартфон Alpha', 10000);
        $this->makeGroup('Одежда');

        $this->get('/')
            ->assertOk()
            ->assertSee('Электроника')
            ->assertSee('(1)')
            ->assertSee('Одежда')
            ->assertSee('Смартфон Alpha')
            ->assertSee('10 000 руб.');
    }

    public function test_group_page_lists_products_from_subgroups_only(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $clothes = $this->makeGroup('Одежда');
        $this->makeProduct($phones, 'Смартфон Alpha', 10000);
        $this->makeProduct($clothes, 'Куртка Beta', 5000);

        $this->get(route('catalog.group', $electronics))
            ->assertOk()
            ->assertSee('Смартфон Alpha')
            ->assertDontSee('Куртка Beta')
            ->assertSee('Телефоны');
    }

    public function test_products_are_sorted_by_price_ascending(): void
    {
        $group = $this->makeGroup('Электроника');
        $this->makeProduct($group, 'Дорогой', 9000);
        $this->makeProduct($group, 'Дешёвый', 1000);

        $html = $this->get('/?sort=price_asc')->getContent();

        $this->assertTrue(strpos($html, 'Дешёвый') < strpos($html, 'Дорогой'));
    }

    public function test_pagination_limits_products_per_page(): void
    {
        $group = $this->makeGroup('Электроника');

        for ($i = 1; $i <= 8; $i++) {
            $this->makeProduct($group, "Товар {$i}", $i * 100);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('Товар 1')
            ->assertSee('Товар 6')
            ->assertDontSee('Товар 7')
            ->assertSee('Страница:');

        $this->get('/?page=2')
            ->assertOk()
            ->assertSee('Товар 7')
            ->assertDontSee('Товар 1');
    }

    public function test_per_page_query_changes_page_size(): void
    {
        $group = $this->makeGroup('Электроника');

        for ($i = 1; $i <= 8; $i++) {
            $this->makeProduct($group, "Товар {$i}", $i * 100);
        }

        $this->get('/?per_page=12')
            ->assertOk()
            ->assertSee('Товар 8')
            ->assertDontSee('Страница:');
    }

    public function test_product_card_shows_price_and_breadcrumbs(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $product = $this->makeProduct($phones, 'Смартфон Alpha', 12345);

        $this->get(route('catalog.product', $product))
            ->assertOk()
            ->assertSee('Смартфон Alpha')
            ->assertSee('Цена: 12 345 руб.')
            ->assertSee('Главная')
            ->assertSee('Электроника')
            ->assertSee('Телефоны');
    }

    public function test_ajax_request_returns_listing_fragment_without_layout(): void
    {
        $group = $this->makeGroup('Электроника');
        $this->makeProduct($group, 'Смартфон Alpha', 10000);

        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/?page=1&sort=name_asc')
            ->assertOk()
            ->assertSee('Смартфон Alpha')
            ->assertSee('Сортировать:')
            ->assertDontSee('navbar-brand', false);
    }

    public function test_repository_filters_and_sorts_in_one_query_scope(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $clothes = $this->makeGroup('Одежда');
        $this->makeProduct($electronics, 'B-phone', 300);
        $this->makeProduct($electronics, 'A-phone', 200);
        $this->makeProduct($clothes, 'Jacket', 100);

        $page = app(ProductRepository::class)->paginate(
            [$electronics->id],
            new CatalogQuery(ProductSort::NameAsc, 6, 1)
        );

        $this->assertSame(['A-phone', 'B-phone'], $page->pluck('name')->all());
        $this->assertSame(2, $page->total());
    }
}
