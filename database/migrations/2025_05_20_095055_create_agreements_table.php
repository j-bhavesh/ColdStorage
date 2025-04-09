<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgreementsTable extends Migration
{
    public function up()
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('seed_variety_id');
            $table->decimal('rate_per_kg', 10, 2);
            $table->date('agreement_date');
            $table->decimal('vighas', 5, 2);
            $table->integer('bag_quantity');

            $table->enum('status', ['active', 'completed', 'rejected', 'hold'])->default('active');

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
            $table->foreign('seed_variety_id')->references('id')->on('seed_varieties')->onDelete('cascade');

            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agreements');
    }
}
