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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('firstname', 60)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->enum('address_type', ['billing', 'shipping'])->default('billing'); // Address type (e.g., 'billing', 'shipping')
            $table->string('street', 80);
            $table->string('city', 80);
            $table->string('state', 50)->nullable();
            $table->string('zip', 20);
            $table->string('country', 2);
            $table->tinyInteger('active')->default(0);
            $table->string('details')->nullable();
            $table->string('phone', 60)->nullable();
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
        Schema::dropIfExists('addresses');
    }
};
