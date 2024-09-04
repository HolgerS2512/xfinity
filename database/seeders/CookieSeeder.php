<?php

namespace Database\Seeders;

use App\Models\Cookie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CookieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cookie::create([
            'name' => 'xFs_csL',
            'description' => 'Diese Cookies tragen zur Sicherheit Ihrer Sitzung bei. Sie stellen sicher, dass nur Sie Zugriff auf Ihre Daten haben, indem sie überprüfen, dass keine unbefugten Dritten Ihre Sitzung übernehmen können.',
            'category' => 'necessary',
            'duration' => 'Session',
        ]);

        Cookie::create([
            'name' => 'xFs_cCv',
            'description' => 'Diese Cookies speichern Ihre Datenschutzeinstellungen.',
            'category' => 'necessary',
            'duration' => '6 Monate',
        ]);
        
        Cookie::create([
            'name' => 'xFs_at',
            'description' => 'Identifiziert den Benutzer und gestattet die Authentifizierung zum Server.',
            'category' => 'necessary',
            'duration' => '10 Tage',
        ]);
        
        // Cookie::create([
        //     'name' => 'L_CD',
        //     'description' => 'Speichert die Versionsnummer der Kategorien',
        //     'category' => 'preferences',
        //     'duration' => '30 Tage',
        // ]);
        
        // Cookie::create([
        //     'name' => '1i1pYjxiEY0QPFaJupNxIRIWxU20240809151433',
        //     'description' => 'Speichert Kategoriedaten, einschließlich bevorzugter Sortieroptionen, Layouts und Anzeigemodi.',
        //     'category' => 'preferences',
        //     'duration' => 'Dauerhaft',
        // ]);
        
        // Cookie::create([
        //     'name' => 'aC_us',
        //     'description' => 'Der Vorname des Benutzers wird aus gestalterischen Gründen nach der Anmeldung sicher und verschlüsselt in Ihrem Browser gespeichert.',
        //     'category' => 'preferences',
        //     'duration' => 'Dauerhaft',
        // ]);
    }
}
