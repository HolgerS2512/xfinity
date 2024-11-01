# Documentation

## Installation

- Install Laravel v9.5.2

```shell
composer create-project laravel/laravel:^9.5.2 xfinity.test
```

- Change Directory

```shell
cd xfinity.test
```

- ENV configs (db, url, ...)

```shell
APP_URL=http://xfinity.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xfinity
DB_USERNAME=root
DB_PASSWORD='ZZW!9Vm-+rc*$q&'

CACHE_DRIVER=redis
QUEUE_CONNECTION=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=6f758fc5713358
MAIL_PASSWORD=87bcce64c55b2d
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="kontakt@xFinity.com"

SESSION_SECURE_COOKIE=false
# Supported: "lax", "strict", "none", null
SESSION_SAME_SITE_COOKIE=strict
SESSION_HTTP_ONLY_COOKIE=false # have to always false!
```

- Install Passport package

```shell
composer require laravel/passport -W
```

- Migrate DB

```shell
php artisan migrate
```

- Install Passport
````shell
php artisan passport:install
php artisan passport:client --personal
````

- Save values in ENV

```shell
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=z8TQU8N5U4IZrH1eLn4aJwJwUYe4a2BzWVF7QPYv
```

- Edit User Model

```php
use Laravel\Passport\HasApiTokens;
```

- Configuration auth in config/auth.php

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

- Configuration auth in app/Providers/AuthServiceProvider.php

```php
<?php
namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}

```

- In Passport class customize token: `laravel_token`.

- Install Audit-Package
```shell
composer require owen-it/laravel-auditing
php artisan vendor:publish --provider="OwenIt\Auditing\AuditingServiceProvider" --tag="config"
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="migrations"
php artisan migrate
```
- add in app/config
```php
/*
* Package Service Providers...
*/
OwenIt\Auditing\AuditingServiceProvider::class,
```


## Deploy

### ToDo

- activate CSRF protection
- Mailing
- Redis
- Queue Server Settings
- Service Worker

- [Follow documentation -> passport](https://laravel.com/docs/9.x/passport#deploying-passport)

### Important

- CSRF protection comment out
```php
App\Http\Middleware\VerifyCsrfToken
```

- Install Supervisor
1. 
```shell
sudo apt-get install supervisor
```

1. Add file: /etc/supervisor/conf.d/laravel-worker.conf

```shell
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=yourusername
numprocs=1
redirect_stderr=true
stdout_logfile=/path-to-your-project/worker.log
```
```shell
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```
1. Start
```shell
php artisan queue:work
```

#### tip
```shell
php artisan config:cache & php artisan route:cache & php artisan view:cache & php artisan serve
```