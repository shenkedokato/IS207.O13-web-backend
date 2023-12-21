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
        Schema::dropIfExists('chitiet_donhangs');

        Schema::create('chitiet_donhangs', function (Blueprint $table) {
            $table->unsignedInteger('MADH');
            $table->unsignedInteger('MAXDSP'); 
            $table->integer('TONGTIEN');
            $table->integer('SOLUONG');
            $table->integer('DADANHGIA');

            $table->foreign('MADH')->references('MADH')->on('donhangs');
            $table->foreign('MAXDSP')->references('MAXDSP')->on('sanpham_mausac_sizes'); 
 
            $table->primary(['MADH', 'MAXDSP']);

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitiet_donhangs');
    }
};
