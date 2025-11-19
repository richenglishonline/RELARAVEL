<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Curriculum::with('creator');

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $curricula = $query->orderBy('order')->orderBy('title')
            ->paginate(max(1, min((int) $request->integer('limit', 20), 100)));

        return response()->json([
            'curricula' => $curricula->items(),
            'pagination' => [
                'total' => $curricula->total(),
                'page' => $curricula->currentPage(),
                'limit' => $curricula->perPage(),
                'totalPages' => $curricula->lastPage(),
            ],
        ]);
    }

    public function show(Curriculum $curriculum): JsonResponse
    {
        return response()->json($curriculum->load('creator'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'topics' => ['nullable', 'array'],
            'materials' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['order'] = $data['order'] ?? 0;

        $curriculum = Curriculum::create($data);

        return response()->json(['curriculum' => $curriculum->load('creator')], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, Curriculum $curriculum): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'topics' => ['nullable', 'array'],
            'materials' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $curriculum->update($data);

        return response()->json(['curriculum' => $curriculum->fresh()->load('creator')]);
    }

    public function destroy(Curriculum $curriculum): JsonResponse
    {
        $curriculum->delete();

        return response()->json(['message' => 'Curriculum deleted successfully']);
    }
}
