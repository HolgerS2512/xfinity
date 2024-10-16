<?php

namespace Database\Seeders;

use App\Models\Cookie;
use App\Models\ProductImage;
use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductImage::create([
            'product_variants_id' => '1',
            'ranking' => '1',
            'url' => 'https://cdn.aboutstatic.com/file/images/ad6024cc45a6375076deda73ea68901a.png?bg=F4F4F5&quality=75&trim=1&height=480&width=360',
            'ext' => 'png',
            'is_primary' => '1',
        ]);

        ProductImage::create([
            'product_variants_id' => '1',
            'ranking' => '2',
            'url' => 'https://cdn.aboutstatic.com/file/images/b6a359d79babfc19bc7295c8f44f7fc2.jpg?quality=75&height=480&width=360',
            'ext' => 'jpg',
        ]);

        ProductImage::create([
            'product_variants_id' => '1',
            'ranking' => '3',
            'url' => 'https://cdn.aboutstatic.com/file/images/56a1716f09c2556272f5f2e68af0a1e1.jpg?quality=75&height=480&width=360',
            'ext' => 'jpg',
        ]);

        ProductImage::create([
            'product_variants_id' => '1',
            'ranking' => '3',
            'url' => 'https://cdn.aboutstatic.com/file/images/41eb06aea89a02f1b2bc210510e21f4d.jpg?quality=75&height=480&width=360',
            'ext' => 'jpg',
        ]);

        ProductImage::create([
            'product_variants_id' => '1',
            'ranking' => '3',
            'url' => 'https://cdn.aboutstatic.com/file/images/899fad11e48b0f48ee934b3528a2b35b.jpg?quality=75&height=480&width=360',
            'ext' => 'jpg',
        ]);
    }
}
