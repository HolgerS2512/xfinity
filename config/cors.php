<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Wichtig: Füge 'sanctum/csrf-cookie' hier hinzu

    'allowed_methods' => ['*'], // Alle Methoden (GET, POST, etc.) erlauben

    'allowed_origins' => ['http://localhost:3000', 'http://xfinity.test:3000'], // Ersetze mit deiner Frontend-Domain

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Alle Header erlauben

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
