<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreExperienceRequest;
use App\Models\ExperienceCompany;
use App\Models\ExperienceRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExperienceController extends Controller
{
    /** List all experience roles with nested companies, ordered by display order. */
    public function index(): JsonResponse
    {
        $roles = ExperienceRole::with(['companies' => fn ($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get();

        return response()->json($roles);
    }

    /** Create a new experience role. */
    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $maxOrder = ExperienceRole::max('order') ?? -1;

        $role = ExperienceRole::create([
            'role'  => $request->role,
            'order' => $maxOrder + 1,
        ]);

        // Return with empty companies array.
        $role->setRelation('companies', collect());

        return response()->json($role, 201);
    }

    /** Update an experience role's title. */
    public function update(StoreExperienceRequest $request, int $id): JsonResponse
    {
        $role = ExperienceRole::find($id);

        if (! $role) {
            return response()->json(['error' => 'Experience role not found'], 404);
        }

        $role->update(['role' => $request->role]);

        return response()->json($role->load('companies'));
    }

    /** Delete a role and cascade-delete its companies. */
    public function destroy(int $id): Response|JsonResponse
    {
        $role = ExperienceRole::find($id);

        if (! $role) {
            return response()->json(['error' => 'Experience role not found'], 404);
        }

        $role->delete();

        return response()->noContent();
    }

    /**
     * Reorder all experience roles.
     * Accepts { "ids": [2, 1] } and sets order = index for each id.
     */
    public function reorder(ReorderRequest $request): JsonResponse
    {
        foreach ($request->ids as $index => $id) {
            ExperienceRole::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['reordered' => true]);
    }

    /** Add a company under an experience role. */
    public function addCompany(StoreCompanyRequest $request, int $id): JsonResponse
    {
        $role = ExperienceRole::find($id);

        if (! $role) {
            return response()->json(['error' => 'Experience role not found'], 404);
        }

        $maxOrder = $role->companies()->max('order') ?? -1;

        $company = $role->companies()->create([
            'summary' => $request->summary,
            'company' => $request->company,
            'period'  => $request->period,
            'order'   => $maxOrder + 1,
        ]);

        return response()->json($company, 201);
    }

    /** Update a company entry. */
    public function updateCompany(StoreCompanyRequest $request, int $roleId, int $companyId): JsonResponse
    {
        $company = ExperienceCompany::where('id', $companyId)
            ->where('experience_id', $roleId)
            ->first();

        if (! $company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $company->update([
            'summary' => $request->summary,
            'company' => $request->company,
            'period'  => $request->period,
        ]);

        return response()->json($company->fresh());
    }

    /** Remove a company from a role. */
    public function destroyCompany(int $roleId, int $companyId): Response|JsonResponse
    {
        $company = ExperienceCompany::where('id', $companyId)
            ->where('experience_id', $roleId)
            ->first();

        if (! $company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->noContent();
    }
}
