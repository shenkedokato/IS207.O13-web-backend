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
        Schema::dropIfExists('chitiet_giohangs');

        Schema::create('chitiet_giohangs', function (Blueprint $table) {
            $table->unsignedInteger('MATK');
            $table->unsignedInteger('MASP');
            $table->unsignedInteger('MAMAU');
            $table->string('MASIZE', 3);
            $table->primary(['MATK', 'MASP', 'MAMAU', 'MASIZE']);

            $table->integer('SOLUONG');
            $table->integer('TONGGIA')->nullable();
            $table->integer('SELECTED')->default(1);


            $table->foreign('MATK')->references('MATK')->on('taikhoans');
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
        Schema::dropIfExists('chitiet_giohangs');
    }
};
