<?php

namespace App\Enums;

enum RoleType: string
{
    case ingeneur = 'ingeneur';
    case admin = 'admin';
    case editor = 'editor';
    case user = 'user';
}
