<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case pending = 'pending';
    case completed = 'completed';
    case failed = 'failed';
    case refunded = 'refunded';
}
