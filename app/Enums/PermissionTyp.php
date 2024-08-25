<?php

namespace App\Enums;

enum PermissionType: string
{
    case Create = 'create';
    case Read = 'read';
    case Edit = 'edit';
    case Update = 'update';
    case Delete = 'delete';
}
