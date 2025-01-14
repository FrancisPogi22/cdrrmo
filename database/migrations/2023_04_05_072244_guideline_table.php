<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guideline', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('organization');
            $table->string('cover_image')->nullable();
            $table->string('content_image');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guideline');
    }
};
