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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variants_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('price', 8, 2);
            $table->char('locale', 2)->default('de');
            $table->char('currency', 3)->default('EUR');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('price_type')->default('Regular');
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
        Schema::dropIfExists('prices');
    }
};
