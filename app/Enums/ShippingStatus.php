<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';
    case SHIPPING = 'shipping';
    case DELIVERED = 'delivered';
}
