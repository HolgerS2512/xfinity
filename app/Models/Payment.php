<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends ModelRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id', 
        'payment_method_id', 
        'amount', 
        'currency', 
        'status', 
        'transaction_id', 
        'payment_details',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class>
     */
    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    /**
     * Get the order for this payment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the payment method for this payment.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
