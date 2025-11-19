<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Setting::query();

        if ($request->filled('group')) {
            $query->where('group', $request->input('group'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $settings = $query->orderBy('group')->orderBy('key')
            ->paginate(max(1, min((int) $request->integer('limit', 50), 100)));

        return response()->json([
            'settings' => $settings->items(),
            'pagination' => [
                'total' => $settings->total(),
                'page' => $settings->currentPage(),
                'limit' => $settings->perPage(),
                'totalPages' => $settings->lastPage(),
            ],
        ]);
    }

    public function show(Setting $setting): JsonResponse
    {
        return response()->json($setting);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value' => ['nullable', 'string'],
            'type' => ['required', 'in:string,number,boolean,json'],
            'group' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['group'] = $data['group'] ?? 'general';

        $setting = Setting::create($data);

        return response()->json(['setting' => $setting], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, Setting $setting): JsonResponse
    {
        $data = $request->validate([
            'key' => ['sometimes', 'string', 'max:255', 'unique:settings,key,'.$setting->id],
            'value' => ['nullable', 'string'],
            'type' => ['sometimes', 'in:string,number,boolean,json'],
            'group' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $setting->update($data);

        return response()->json(['setting' => $setting->fresh()]);
    }

    public function destroy(Setting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json(['message' => 'Setting deleted successfully']);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($data['settings'] as $item) {
            Setting::set($item['key'], $item['value'] ?? '');
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
