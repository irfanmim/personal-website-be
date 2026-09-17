<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('description', 500);
            // MySQL/MariaDB reject a literal default on JSON columns; an expression
            // default (JSON_ARRAY()) is required and works on both engines.
            $table->json('tags')->default(new Expression('(JSON_ARRAY())'));
            $table->string('demo', 300)->default('');
            $table->string('image', 500)->default('');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
