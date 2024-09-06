<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25);
            $table->enum('type', [
                'credit_card',
                'paypal',
                'klarna',
                'instant_bank_transfer_klarna',
                'bank_transfer',
                'sepa',
                'apple_pay',
                'google_pay',
                'instant_bank_transfer',
                'direct_debit',
                'invoice_payment',
                'nfc',
                'crypto',
                'cash_on_delivery',
                'cash',
                'coupon',
            ]);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
