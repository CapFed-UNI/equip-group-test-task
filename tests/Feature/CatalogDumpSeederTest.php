<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Price;
use App\Models\Product;
use Database\Seeders\CatalogDumpSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogDumpSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_groups_products_and_prices_from_test_sql(): void
    {
        $this->seed(CatalogDumpSeeder::class);

        $this->assertSame(32, Group::query()->count());
        $this->assertSame(82, Product::query()->count());
        $this->assertSame(82, Price::query()->count());
        $this->assertTrue(Group::query()->where('id_parent', 0)->where('name', 'Электроника')->exists());
    }
}
