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
        Schema::dropIfExists('hinhanhsanphams');

        Schema::create('hinhanhsanphams', function (Blueprint $table) {

            $table->string('MAHINHANH', 255); 
            $table->unsignedInteger('MASP');
            $table->string('imgURL'); 
   

            $table->primary(['MAHINHANH', 'MASP']); 
            $table->foreign('MASP')->references('MASP')->on('sanphams'); 
            $table->foreign('MAHINHANH')->references('MAHINHANH')->on('hinhanhs'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hinhanhsanphams');
    }
};
