<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Contact;
use App\Models\ExperienceRole;
use App\Models\Hero;
use App\Models\Project;
use App\Models\SkillArea;
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

        // ── Skill areas (hero charts) ─────────────────────────────────────────
        // Fresh installs only, so levels edited in the admin are never overwritten.
        if (SkillArea::count() === 0) {
            foreach (SkillArea::defaults() as $i => $area) {
                SkillArea::create($area + ['order' => $i]);
            }
        }

        // ── Experience roles ──────────────────────────────────────────────────
        // Fresh installs only; CvExperienceSeeder overwrites, so don't clobber
        // edits made through the admin on an existing database.
        if (ExperienceRole::count() === 0) {
            $this->call(CvExperienceSeeder::class);
        }

        // ── Projects ──────────────────────────────────────────────────────────
        if (Project::count() === 0) {
            // Mirrors production; images are served from the frontend's public/images.
            Project::insert([
                ['title' => 'AI-Native SWE Concept', 'description' => 'A field guide to the concepts needed to build software with AI natively.', 'tags' => json_encode(['React', 'TypeScript']), 'demo' => 'https://ai-native-swe-concept.irfanmim.com/', 'image' => '/images/ai-native-swe-concept.png', 'order' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'FinTrack',     'description' => 'A personal finance web app that helps you track spending, manage budgets, and stay on top of your financial goals.', 'tags' => json_encode(['Web App', 'React', 'Laravel', 'Fullstack']), 'demo' => 'https://finance.irfanmim.com', 'image' => '/images/fintrack.png',     'order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Nexus',        'description' => 'A personal learning OS that maps your goals, tracks your progress, and surfaces what to focus on next.',            'tags' => json_encode(['Web App', 'React', 'Laravel', 'Fullstack']), 'demo' => 'https://nexus.irfanmim.com',   'image' => '/images/nexus.png',        'order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'LearnTracker', 'description' => 'A personal dashboard to track your learning platforms, subscriptions, and course progress in one place.',               'tags' => json_encode(['Web App', 'Vue', 'Laravel', 'Fullstack']),   'demo' => 'https://manager.irfanmim.com', 'image' => '/images/learntracker.png', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'ExamGrader',   'description' => 'A web application that implements a crowdsourcing method for exam assessment.',     'tags' => json_encode(['Web App', 'Fullstack', 'Django', 'React']), 'demo' => '', 'image' => '/images/exam-grader.svg', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Farmer App',   'description' => 'Mobile application that helps farmers manage their crops with real-time data and expert advice.', 'tags' => json_encode(['Mobile App', 'Frontend', 'React Native']), 'demo' => '', 'image' => '/images/farmer-app.svg',  'order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'GamesHub',     'description' => 'A web application that combine Augmented Reality with gamification.',                              'tags' => json_encode(['Web App', 'Frontend', 'React']),           'demo' => '', 'image' => '/images/games-hub.svg',  'order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
