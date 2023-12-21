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
        Schema::dropIfExists('donhang_vouchers');

        Schema::create('donhang_vouchers', function (Blueprint $table) {
            $table->string('MAVOUCHER', 50); 
            $table->unsignedInteger('MADH');

            $table->primary(['MAVOUCHER', 'MADH']);

            $table->foreign('MAVOUCHER')->references('MAVOUCHER')->on('vouchers');
            $table->foreign('MADH')->references('MADH')->on('donhangs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang_vouchers');
    }
};
