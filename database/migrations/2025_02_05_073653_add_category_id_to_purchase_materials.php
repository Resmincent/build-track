d<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class AddCategoryIdToPurchaseMaterials extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('purchase_materials', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('purchase_materials', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            });
        }
    }
