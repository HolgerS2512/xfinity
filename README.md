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

MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=6f758fc5713358
MAIL_PASSWORD=87bcce64c55b2d
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="kontakt@xFinity.com"
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
````

- Save values in ENV

```shell
Client_1=zZ8SVE8bQokaS0BsukhY5rissc1tZnzlbP5QA0aM
Client_2=xd4BUYVpr73ubcbWCD4GJlDvQDuu3jgTfQuX0Nga
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







## Deploy

- [Follow documentation -> passport](https://laravel.com/docs/9.x/passport#deploying-passport)
