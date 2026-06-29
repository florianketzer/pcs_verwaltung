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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicetype_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company')->nullable();
            $table->string('contact')->nullable();
            $table->string('street')->nullable();
            $table->text('addition')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('comment_intern')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
