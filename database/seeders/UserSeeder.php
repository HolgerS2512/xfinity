<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'firstname' => 'Holger',
            'lastname' => 'Schatte',
            'email' => 'schatte-@gmx.de',
            'password' => Hash::make('ZZW!9Vm-+rc*$q&'),
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
    }
}
