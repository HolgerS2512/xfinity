<?php

namespace App\Enums;

enum CookieType: string
{
    case necessary = 'necessary';
    case preferences = 'preferences';
    case statistics = 'statistics';
    case marketing = 'marketing';
    case unclassified = 'unclassified';
}
