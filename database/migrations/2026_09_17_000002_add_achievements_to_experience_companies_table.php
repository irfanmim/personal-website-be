<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_companies', function (Blueprint $table) {
            $table->json('achievements')->nullable()->after('summary');
        });
    }

    public function down(): void
    {
        Schema::table('experience_companies', function (Blueprint $table) {
            $table->dropColumn('achievements');
        });
    }
};
