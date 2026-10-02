<?php

namespace Database\Seeders;

use App\Models\ExperienceRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Replaces all experience roles/companies with the CV's timeline.
 *
 * The real CV lives in database/seeders/data/cv.private.json (git-ignored);
 * without it a neutral placeholder is seeded, so no CV detail is committed.
 *
 * Unlike DatabaseSeeder (which only fills empty tables), this overwrites
 * existing experience rows — run it deliberately:
 *   php artisan db:seed --class=CvExperienceSeeder
 */
class CvExperienceSeeder extends Seeder
{
    /** Git-ignored file holding the real CV data (same shape as PLACEHOLDER_ROLES). */
    public const PRIVATE_FILE = 'seeders/data/cv.private.json';

    /** Neutral stand-in used when the private CV file is absent. */
    public const PLACEHOLDER_ROLES = [
        [
            'role'      => 'Software Engineering',
            'companies' => [
                [
                    'summary'      => 'Software Engineer',
                    'company'      => 'Your Company',
                    'period'       => 'Jan 2020 - Present',
                    'achievements' => [
                        'Describe a key achievement here.',
                    ],
                ],
            ],
        ],
    ];

    /** @return array<int, array{role: string, companies: array<int, array<string, mixed>>}> */
    private function roles(): array
    {
        $file = database_path(self::PRIVATE_FILE);

        if (is_file($file)) {
            return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        $this->command?->warn('No ' . self::PRIVATE_FILE . ' found; seeding placeholder experience.');

        return self::PLACEHOLDER_ROLES;
    }

    public function run(): void
    {
        $roles = $this->roles();

        DB::transaction(function () use ($roles) {
            $names = array_column($roles, 'role');

            // Roles not on the CV go away (their companies cascade).
            ExperienceRole::whereNotIn('role', $names)->get()->each->delete();

            foreach ($roles as $roleOrder => $data) {
                $role = ExperienceRole::updateOrCreate(
                    ['role' => $data['role']],
                    ['order' => $roleOrder]
                );

                $role->companies()->delete();

                foreach ($data['companies'] as $companyOrder => $company) {
                    $role->companies()->create($company + ['order' => $companyOrder]);
                }
            }
        });
    }
}
