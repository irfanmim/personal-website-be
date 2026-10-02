<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero', function (Blueprint $table) {
            $table->string('greeting', 100)->default('')->after('role');
            $table->string('headline', 150)->default('')->after('greeting');
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->string('heading', 100)->default('')->after('id');
            $table->string('blurb', 200)->default('')->after('heading');
        });

        // Keep an existing site looking the same: these were hardcoded in the frontend.
        DB::table('hero')->update(['greeting' => 'Hi! Welcome!', 'headline' => "I'm Irfan"]);
        DB::table('contact')->update(['heading' => 'Get in touch', 'blurb' => "Have a role in mind? Let's connect."]);
    }

    public function down(): void
    {
        Schema::table('hero', function (Blueprint $table) {
            $table->dropColumn(['greeting', 'headline']);
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn(['heading', 'blurb']);
        });
    }
};
