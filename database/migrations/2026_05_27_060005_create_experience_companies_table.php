<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')
                  ->constrained('experience_roles')
                  ->cascadeOnDelete();
            $table->string('summary', 150);   // job title / position held
            $table->string('company', 150);
            $table->string('period', 50);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_companies');
    }
};
