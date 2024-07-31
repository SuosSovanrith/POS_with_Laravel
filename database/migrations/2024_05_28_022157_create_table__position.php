<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Position', function (Blueprint $table) {
            $table->id('position_id');
            $table->string('position_name');
            $table->timestamps();
        });

        DB::table('Position')
        ->insert(array(
            array('position_name' => 'Admin'),
            array('position_name' => 'N/A'),
            array('position_name' => 'Cashier'),
            array('position_name' => 'Stock Manager'),
            array('position_name' => 'Customer')
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Position');
    }
};
