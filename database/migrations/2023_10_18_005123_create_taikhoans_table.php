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
        // Schema::dropIfExists('taikhoans'); 
        Schema::create('taikhoans', function (Blueprint $table) {
            $table->increments('MATK');
            $table->string('TEN');
            $table->string('EMAIL');
            $table->string('PASSWORD');
            $table->string('GIOITINH')->nullable();
            $table->string('SDT')->nullable();
            $table->string('DIACHI')->nullable();
            $table->string('ROLE')->nullable();
            $table->string('AdminVerify')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taikhoans');
    }
};
