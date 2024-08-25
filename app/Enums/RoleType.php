<?php

namespace App\Enums;

enum RoleType: string
{
    case Ingeneur = 'ingeneur';
    case Admin = 'admin';
    case Editor = 'editor';
    case User = 'user';
}
