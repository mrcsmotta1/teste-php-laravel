<?php

namespace Database\Seeders;

use App\Models\ImportFile\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categoryias = [
            ['name' => 'Remessa Parcial'],
            ['name' => 'Remessa']
        ];

        foreach ($categoryias as $categoria) {
            Category::create($categoria);
        }

    }
}
