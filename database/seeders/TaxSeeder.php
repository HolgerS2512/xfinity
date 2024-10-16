<?php

namespace Database\Seeders;

use App\Models\Cookie;
use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tax::create([
            'country' => 'DE',
            'vat' => '19',
        ]);

        Tax::create([
            'country' => 'FR',
            'vat' => '20',
        ]);

        Tax::create([
            'country' => 'IT',
            'vat' => '22',
        ]);

        Tax::create([
            'country' => 'ES',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'AT',
            'vat' => '20',
        ]);

        Tax::create([
            'country' => 'NL',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'BE',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'SE',
            'vat' => '25',
        ]);

        Tax::create([
            'country' => 'DK',
            'vat' => '25',
        ]);

        Tax::create([
            'country' => 'FI',
            'vat' => '24',
        ]);

        Tax::create([
            'country' => 'IE',
            'vat' => '23',
        ]);

        Tax::create([
            'country' => 'PT',
            'vat' => '23',
        ]);

        Tax::create([
            'country' => 'GR',
            'vat' => '24',
        ]);

        Tax::create([
            'country' => 'CZ',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'HU',
            'vat' => '27',
        ]);

        Tax::create([
            'country' => 'SK',
            'vat' => '20',
        ]);

        Tax::create([
            'country' => 'HR',
            'vat' => '25',
        ]);

        Tax::create([
            'country' => 'EE',
            'vat' => '20',
        ]);

        Tax::create([
            'country' => 'LV',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'LT',
            'vat' => '21',
        ]);

        Tax::create([
            'country' => 'SI',
            'vat' => '22',
        ]);

        Tax::create([
            'country' => 'CY',
            'vat' => '19',
        ]);

        Tax::create([
            'country' => 'MT',
            'vat' => '18',
        ]);
    }
}
