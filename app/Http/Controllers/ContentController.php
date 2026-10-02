<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAboutRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Requests\UpdateHeroRequest;
use App\Http\Requests\UpdateSkillsRequest;
use App\Models\About;
use App\Models\Contact;
use App\Models\ExperienceRole;
use App\Models\Hero;
use App\Models\SkillArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * Aggregate GET: returns all content sections in one response.
     * Called by the public site on mount to hydrate the store.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'hero'        => Hero::first(),
            'about'       => About::first(),
            'contact'     => $this->formatContact(Contact::first()),
            'experiences' => ExperienceRole::with(['companies' => fn ($q) => $q->orderBy('order')])
                                ->orderBy('order')
                                ->get(),
            'skills'      => $this->formatSkills(),
        ]);
    }

    /**
     * Replace the whole skills list (small, ordered by array position).
     * Sent as one list so reordering, hiding and deleting are a single atomic save.
     */
    public function updateSkills(UpdateSkillsRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            $keys = [];
            foreach ($request->input('skills') as $i => $area) {
                $keys[] = $area['key'];
                SkillArea::updateOrCreate(
                    ['key' => $area['key']],
                    [
                        'label'       => $area['label'],
                        'short_label' => $area['shortLabel'],
                        'pillar'      => $area['pillar'],
                        'level'       => $area['level'],
                        'tech'        => array_values($area['tech']),
                        'visible'     => $area['visible'],
                        'order'       => $i,
                    ]
                );
            }
            SkillArea::whereNotIn('key', $keys)->delete();
        });

        return response()->json($this->formatSkills());
    }

    private function formatSkills(): array
    {
        return SkillArea::orderBy('order')->get()->map->toApiArray()->all();
    }

    /** Update the hero section (singleton). */
    public function updateHero(UpdateHeroRequest $request): JsonResponse
    {
        $hero = Hero::firstOrNew([]);
        $hero->fill($request->only('name', 'role', 'tagline'))->save();

        return response()->json($hero);
    }

    /** Update the about section (singleton). */
    public function updateAbout(UpdateAboutRequest $request): JsonResponse
    {
        $about = About::firstOrNew([]);
        $about->fill($request->only('bio'))->save();

        return response()->json($about);
    }

    /** Update the contact section (singleton). */
    public function updateContact(UpdateContactRequest $request): JsonResponse
    {
        $contact = Contact::firstOrNew([]);
        $contact->fill([
            'linkedin'  => $request->linkedin,
            'github'    => $request->github,
            'instagram' => $request->instagram ?? '',
            'cv_url'    => $request->cvUrl ?? '',
        ])->save();

        return response()->json($this->formatContact($contact));
    }

    /**
     * Map the DB snake_case column to the API camelCase key expected by the frontend.
     */
    private function formatContact(?Contact $contact): array
    {
        if (! $contact) {
            return ['linkedin' => '', 'github' => '', 'instagram' => '', 'cvUrl' => ''];
        }

        return $contact->toApiArray();
    }
}
