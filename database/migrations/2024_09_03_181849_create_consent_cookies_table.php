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
        Schema::create('consent_cookies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consent_id')->constrained()->onDelete('cascade');
            $table->foreignId('cookie_id')->constrained()->onDelete('cascade');
            $table->boolean('consented');
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
        Schema::dropIfExists('consent_cookies');
    }
};
