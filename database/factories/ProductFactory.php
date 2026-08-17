<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $types = ['Смартфон', 'Ноутбук', 'Наушники', 'Холодильник', 'Куртка', 'Процессор', 'Монитор'];
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'Sony', 'Bosch', 'Intel', 'AMD', 'LG'];

        return [
            'id_group' => Group::factory(),
            'name' => sprintf(
                '%s %s %s',
                fake()->randomElement($types),
                fake()->randomElement($brands),
                fake()->bothify('??-###')
            ),
        ];
    }
}
