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
        Schema::create('workingtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workreport_id')->constrained()->onDelete('cascade');
            $table->timestamp('date')->nullable();
            $table->time('travel_time_from')->nullable();
            $table->time('travel_time_to')->nullable();
            $table->time('work_from')->nullable();
            $table->time('work_to')->nullable();
            $table->string('work_type')->nullable();
            $table->time('overtime')->nullable();
            $table->text('text')->nullable();
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
        Schema::dropIfExists('workingtimes');
    }
};
