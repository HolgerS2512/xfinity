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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('billing_address_id'); // Foreign key to billing address
            $table->unsignedBigInteger('shipping_address_id');  // Foreign key to shipping address
            $table->string('order_number')->unique();
            $table->decimal('shipping_cost', 8, 2)->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending'); // Order status
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
