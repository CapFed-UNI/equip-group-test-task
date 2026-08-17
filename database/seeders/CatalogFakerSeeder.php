<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogFakerSeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Электроника' => [
                'Телефоны и смарт-часы' => [
                    'Смартфоны' => ['Смартфон', 'Apple', 'Samsung', 'Xiaomi'],
                    'Смарт-часы и фитнес-браслеты' => ['Смарт-часы', 'Huawei', 'Xiaomi', 'Samsung'],
                ],
                'Компьютеры и комплектующие' => [
                    'Комплектующие для ПК' => ['Видеокарта', 'NVIDIA', 'AMD', 'Intel'],
                    'Моноблоки' => ['Моноблок', 'Apple', 'HP', 'Lenovo'],
                    'Системные блоки' => ['Системный блок', 'HyperPC', 'DNS', 'Alienware'],
                ],
            ],
            'Одежда' => [
                'Женщинам' => [
                    'Блузы и рубашки' => ['Блузка', 'Zara', 'H&M', 'Mango'],
                ],
                'Мужчинам' => [
                    'Брюки' => ['Брюки', 'Levi\'s', 'Nike', 'Adidas'],
                    'Верхняя одежда' => ['Куртка', 'The North Face', 'Columbia', 'Nike'],
                ],
            ],
            'Бытовая техника' => [
                'Крупная бытовая техника' => [
                    'Холодильники' => ['Холодильник', 'Bosch', 'LG', 'Samsung'],
                    'Морозильные камеры' => ['Морозильник', 'Haier', 'Бирюса', 'Atlant'],
                ],
                'Встраиваемая бытовая техника' => [
                    'Стиральные машины' => ['Стиральная машина', 'Bosch', 'Indesit', 'Weissgauff'],
                    'Посудомойки' => ['Посудомоечная машина', 'Bosch', 'Electrolux', 'Midea'],
                ],
            ],
        ];

        foreach ($tree as $rootName => $branches) {
            $this->seedBranch($rootName, 0, $branches);
        }
    }

    private function seedBranch(string $name, int $parentId, array $children): int
    {
        $group = Group::query()->create([
            'id_parent' => $parentId,
            'name' => $name,
        ]);

        $isLeafMeta = $this->isProductMeta($children);

        if ($isLeafMeta) {
            $this->seedProducts((int) $group->id, $children);

            return (int) $group->id;
        }

        foreach ($children as $childName => $nested) {
            $this->seedBranch($childName, (int) $group->id, $nested);
        }

        return (int) $group->id;
    }

    /**
     * Метаданные листа: [тип, бренд1, бренд2, ...].
     */
    private function isProductMeta(array $children): bool
    {
        return $children !== [] && array_is_list($children);
    }

    /**
     * @param  list<string>  $meta
     */
    private function seedProducts(int $groupId, array $meta): void
    {
        $type = $meta[0];
        $brands = array_slice($meta, 1);
        $count = fake()->numberBetween(4, 8);

        for ($i = 0; $i < $count; $i++) {
            $product = Product::query()->create([
                'id_group' => $groupId,
                'name' => sprintf(
                    '%s %s %s',
                    $type,
                    fake()->randomElement($brands),
                    fake()->bothify('??-###')
                ),
            ]);

            Price::query()->create([
                'id_product' => $product->id,
                'price' => fake()->randomFloat(2, 299, 149990),
            ]);
        }
    }
}
