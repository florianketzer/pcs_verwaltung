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
        Schema::create('workreports', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('customers')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->foreign('editor_id')->references('id')->on('users')->nullOnDelete();
            $table->text('comment')->nullable();
            $table->boolean('work_finished')->default(false);
            $table->boolean('signature_customer_service')->default(false);
            $table->string('name_customer_service')->nullable();
            $table->boolean('signature_customer')->default(false);
            $table->string('name_customer')->nullable();
            $table->timestamp('date')->nullable();
            $table->boolean('locked')->default(false);
            // $table->foreignId('document_id', 'soeinfach')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('delivery_bill_id')->nullable();
            $table->foreign('delivery_bill_id')->references('id')->on('documents')->nullOnDelete();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->nullOnDelete();
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
        Schema::dropIfExists('workreports');
    }
};
