<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CatalogDumpSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('test.sql');

        if (! is_file($path)) {
            $this->command?->warn('Файл test.sql не найден, пропуск CatalogDumpSeeder.');

            return;
        }

        $sql = file_get_contents($path);
        preg_match_all('/insert\s+into\s+`(\w+)`.*?;/is', $sql, $matches, PREG_SET_ORDER);

        $inserts = [];
        foreach ($matches as $match) {
            $inserts[$match[1]] = $match[0];
        }

        Schema::disableForeignKeyConstraints();

        try {
            foreach (['groups', 'products', 'prices'] as $table) {
                if (! isset($inserts[$table])) {
                    continue;
                }

                $statement = $inserts[$table];

                if (DB::getDriverName() === 'sqlite') {
                    $statement = str_replace('\\"', '"', $statement);
                }

                DB::unprepared($statement);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
