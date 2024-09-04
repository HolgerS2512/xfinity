<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Neu und Highlights',
            'active' => 1,
        ]);
        Category::create([
            'name' => 'Sale',
            'active' => 1,
        ]);
        Category::create([
            'name' => 'Neu no Active',
            'level' => '2',
            'parent_id' => '1',
        ]);
        Category::create([
            'name' => 'Neu',
            'level' => '2',
            'active' => 1,
            'parent_id' => '1',
        ]);
        Category::create([
            'name' => 'Highlights',
            'level' => '2',
            'active' => 1,
            'parent_id' => '1',
        ]);
        Category::create([
            'name' => 'Schuhe',
            'level' => '3',
            'active' => 1,
            'parent_id' => '5',
        ]);
    }
}
