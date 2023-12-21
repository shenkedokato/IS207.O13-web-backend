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
        Schema::dropIfExists('sanpham_mausac_sizes');

        Schema::create('sanpham_mausac_sizes', function (Blueprint $table) {
            $table->increments('MAXDSP');
            $table->unsignedInteger('MASP');
            $table->unsignedInteger('MAMAU');
            $table->string('MASIZE', 3);
            $table->integer('SOLUONG');
            // $table->primary(['MAXDSP']);

            $table->foreign('MASP')->references('MASP')->on('sanphams');
            $table->foreign('MAMAU')->references('MAMAU')->on('mausacs');
            $table->foreign('MASIZE')->references('MASIZE')->on('sizes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham_mausac_sizes');
    }
};
