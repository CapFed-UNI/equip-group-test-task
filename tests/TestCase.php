<?php

namespace Tests;

use App\Models\Group;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function makeGroup(string $name, int $parentId = 0): Group
    {
        return Group::query()->create([
            'id_parent' => $parentId,
            'name' => $name,
        ]);
    }

    protected function makeProduct(Group $group, string $name, float $price): Product
    {
        $product = Product::query()->create([
            'id_group' => $group->id,
            'name' => $name,
        ]);

        Price::query()->create([
            'id_product' => $product->id,
            'price' => $price,
        ]);

        return $product;
    }
}
