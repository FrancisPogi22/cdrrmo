<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flood', function (Blueprint $table) {
            $table->id('flood_id');
            $table->string('location')->nullable();
            $table->date('month')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flood');
    }
};
