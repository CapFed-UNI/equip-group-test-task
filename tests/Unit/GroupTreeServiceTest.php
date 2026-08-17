<?php

namespace Tests\Unit;

use App\Services\GroupTreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTreeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_descendant_ids_include_self_and_nested_groups(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $smartphones = $this->makeGroup('Смартфоны', $phones->id);
        $watches = $this->makeGroup('Часы', $phones->id);
        $this->makeGroup('Одежда');

        $ids = app(GroupTreeService::class)->descendantIdsIncludingSelf($electronics->id);

        $this->assertEqualsCanonicalizing(
            [$electronics->id, $phones->id, $smartphones->id, $watches->id],
            $ids
        );
    }

    public function test_product_counts_roll_up_through_the_tree(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $smartphones = $this->makeGroup('Смартфоны', $phones->id);
        $clothes = $this->makeGroup('Одежда');

        $this->makeProduct($smartphones, 'Phone A', 1000);
        $this->makeProduct($smartphones, 'Phone B', 2000);
        $this->makeProduct($phones, 'Accessory', 500);
        $this->makeProduct($clothes, 'Jacket', 3000);

        $tree = app(GroupTreeService::class);

        $this->assertSame(2, $tree->productCount($smartphones->id));
        $this->assertSame(3, $tree->productCount($phones->id));
        $this->assertSame(3, $tree->productCount($electronics->id));
        $this->assertSame(1, $tree->productCount($clothes->id));
    }

    public function test_breadcrumbs_include_home_and_all_parents(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $smartphones = $this->makeGroup('Смартфоны', $phones->id);

        $crumbs = app(GroupTreeService::class)->breadcrumbs($smartphones);

        $this->assertSame(['Главная', 'Электроника', 'Телефоны', 'Смартфоны'], array_column($crumbs, 'title'));
        $this->assertSame(route('catalog.index'), $crumbs[0]['url']);
        $this->assertSame(route('catalog.group', $smartphones), $crumbs[3]['url']);
    }

    public function test_sidebar_on_home_shows_only_root_groups(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $this->makeGroup('Телефоны', $electronics->id);
        $this->makeGroup('Одежда');

        $sidebar = app(GroupTreeService::class)->sidebar(null);

        $this->assertCount(2, $sidebar);
        $this->assertSame('Электроника', $sidebar[0]['group']->name);
        $this->assertSame([], $sidebar[0]['children']);
    }

    public function test_sidebar_expands_path_to_the_active_group(): void
    {
        $electronics = $this->makeGroup('Электроника');
        $phones = $this->makeGroup('Телефоны', $electronics->id);
        $smartphones = $this->makeGroup('Смартфоны', $phones->id);
        $this->makeGroup('Часы', $phones->id);
        $this->makeGroup('Одежда');

        $sidebar = app(GroupTreeService::class)->sidebar($smartphones->id);

        $this->assertTrue($sidebar[0]['on_path']);
        $this->assertTrue($sidebar[0]['children'][0]['on_path']);
        $this->assertTrue($sidebar[0]['children'][0]['children'][0]['active']);
        $this->assertSame('Смартфоны', $sidebar[0]['children'][0]['children'][0]['group']->name);
        $this->assertCount(2, $sidebar[0]['children'][0]['children']);
        $this->assertSame([], $sidebar[1]['children']);
    }
}
