<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Group::query()->exists()) {
            return;
        }

        $mode = config('catalog.seed');

        if ($mode === 'dump') {
            $this->call(CatalogDumpSeeder::class);

            return;
        }

        $this->call(CatalogFakerSeeder::class);
    }
}
