<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('taken_by', ['self', 'other']);
            $table->string('taken_by_name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advance_payments');
    }
}
