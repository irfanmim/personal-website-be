<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'image'       => $this->resolveImagePath($request),
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
            'image'       => $this->resolveImagePath($request, $project->image),
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

        $this->deleteStoredImage($project->image);
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

    private function resolveImagePath(StoreProjectRequest $request, string $existing = ''): string
    {
        if ($request->hasFile('image')) {
            $this->deleteStoredImage($existing);
            $path = $request->file('image')->storeAs(
                'projects',
                Str::uuid() . '.' . $request->file('image')->extension(),
                'public'
            );
            return Storage::disk('public')->url($path);
        }
        if ($request->filled('image')) return $request->image;
        return $existing;
    }

    private function deleteStoredImage(string $image): void
    {
        if (!$image || str_starts_with($image, '/images/')) return;
        $parsed   = parse_url($image, PHP_URL_PATH);
        $diskPath = ltrim(str_replace('/storage/', '', $parsed), '/');
        // Only delete files we stored — never touch anything outside projects/
        if (!str_starts_with($diskPath, 'projects/')) return;
        if (Storage::disk('public')->exists($diskPath)) {
            Storage::disk('public')->delete($diskPath);
        }
    }
}
