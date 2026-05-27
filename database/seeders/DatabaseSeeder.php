<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Contact;
use App\Models\ExperienceRole;
use App\Models\Hero;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Admin user (Sanctum tokenable) ────────────────────────────────────
        // firstOrCreate so that re-seeding never overwrites credentials changed via the API.
        $user = User::firstOrCreate(
            ['email' => 'admin@app.local'],
            [
                'name'     => 'Admin',
                'username' => env('ADMIN_USERNAME', 'admin'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'admin')),
            ]
        );

        // On first run after the username migration, back-fill the column if empty.
        if (empty($user->username)) {
            $user->update(['username' => env('ADMIN_USERNAME', 'admin')]);
        }

        // ── Hero ──────────────────────────────────────────────────────────────
        Hero::firstOrCreate([], [
            'name' => 'M. Irfan Maulana',
            'role' => 'Software Engineer | Product Manager | Sports Enthusiast.',
        ]);

        // ── About ─────────────────────────────────────────────────────────────
        About::firstOrCreate([], [
            'bio' => 'I am a software engineer and product manager who loves building products people actually use. '
                   . 'Believing technology is a catalyst for business success, I combine technical expertise with '
                   . 'product thinking to create solutions that are useful, valuable, and built to last.',
        ]);

        // ── Contact ───────────────────────────────────────────────────────────
        Contact::firstOrCreate([], [
            'linkedin'  => 'https://www.linkedin.com/in/irfanmim',
            'github'    => 'https://github.com/irfanmim',
            'instagram' => 'https://www.instagram.com/irfanmim',
            'cv_url'    => '',
        ]);

        // ── Experience roles ──────────────────────────────────────────────────
        $product = ExperienceRole::firstOrCreate(
            ['role' => 'Product'],
            ['order' => 0]
        );

        if ($product->companies()->count() === 0) {
            $product->companies()->createMany([
                ['summary' => 'Product Manager', 'company' => 'Visual Analysis',               'period' => 'Jan 2025 - Present',  'order' => 0],
                ['summary' => 'Product Manager', 'company' => 'VA Insight Software Pte. Ltd.', 'period' => 'Jul 2023 - Dec 2024', 'order' => 1],
            ]);
        }

        $eng = ExperienceRole::firstOrCreate(
            ['role' => 'Software Engineering'],
            ['order' => 1]
        );

        if ($eng->companies()->count() === 0) {
            $eng->companies()->createMany([
                ['summary' => 'Development Team Lead', 'company' => 'VA Insight Software Pte. Ltd.', 'period' => 'Jul 2021 - Jul 2023', 'order' => 0],
                ['summary' => 'Fullstack Developer',   'company' => 'VA Insight Software Pte. Ltd.', 'period' => 'Jul 2020 - Jul 2021', 'order' => 1],
                ['summary' => 'Fullstack Developer',   'company' => 'VA Worldwide',                  'period' => 'Sep 2019 - Jul 2020', 'order' => 2],
            ]);
        }

        // ── Projects ──────────────────────────────────────────────────────────
        if (Project::count() === 0) {
            Project::insert([
                ['title' => 'ExamGrader', 'description' => 'A web application that implements a crowdsourcing method for exam assessment.',     'tags' => json_encode(['Web App', 'Fullstack', 'Django', 'React']), 'demo' => '', 'image' => '/images/exam-grader.svg', 'order' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Farmer App', 'description' => 'Mobile application that helps farmers manage their crops with real-time data and expert advice.', 'tags' => json_encode(['Mobile App', 'Frontend', 'React Native']), 'demo' => '', 'image' => '/images/farmer-app.svg',  'order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'GamesHub',   'description' => 'A web application that combine Augmented Reality with gamification.',                              'tags' => json_encode(['Web App', 'Frontend', 'React']),           'demo' => '', 'image' => '/images/games-hub.svg',  'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
