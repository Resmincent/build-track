<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30);
            $table->integer('quantity');
            $table->text('description');
            $table->enum('status', ['pending', 'rejected', 'approved'])->default('pending');
            $table->text('reject_reason')->nullable();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

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
        Schema::dropIfExists('purchase_materials');
    }
}
