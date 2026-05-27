<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * List projects ordered by display order.
     * Accepts optional ?limit=N to return only the first N projects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::orderBy('order');

        if ($request->filled('limit')) {
            $query->limit((int) $request->query('limit'));
        }

        return response()->json($query->get());
    }

    /** Create a new project. */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $maxOrder = Project::max('order') ?? -1;

        $project = Project::create([
            'title'       => $request->title,
            'description' => $request->description,
            'tags'        => $request->tags,
            'demo'        => $request->demo ?? '',
            'image'       => $request->image ?? '',
            'order'       => $maxOrder + 1,
        ]);

        return response()->json($project, 201);
    }

    /** Update an existing project. */
    public function update(StoreProjectRequest $request, int $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $project->update([
            'title'       => $request->title,
            'description' => $request->description,
            'tags'        => $request->tags,
            'demo'        => $request->demo ?? '',
            'image'       => $request->image ?? '',
        ]);

        return response()->json($project->fresh());
    }

    /** Delete a project. */
    public function destroy(int $id): Response|JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $project->delete();

        return response()->noContent();
    }

    /**
     * Reorder all projects.
     * Accepts { "ids": [3, 1, 2] } and sets order = index for each id.
     */
    public function reorder(ReorderRequest $request): JsonResponse
    {
        foreach ($request->ids as $index => $id) {
            Project::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['reordered' => true]);
    }
}
