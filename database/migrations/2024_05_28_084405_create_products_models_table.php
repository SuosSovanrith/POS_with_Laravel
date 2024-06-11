<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->bigInteger("category_id")->nullable();
            $table->bigInteger("supplier_id")->nullable();
            $table->string("barcode")->unique();
            $table->text("barcode_image")->nullable();
            $table->integer("quantity")->default(0);
            $table->decimal("price_in", 8, 2)->default(0);
            $table->decimal("price_out", 8, 2)->default(0);
            $table->boolean("in_stock")->default(true);
            $table->string("image")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
