<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Salary::with('teacher');

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->input('end_date'));
        }

        $salaries = $query->orderByDesc('created_at')
            ->paginate(max(1, min((int) $request->integer('limit', 20), 100)));

        return response()->json([
            'salaries' => $salaries->items(),
            'pagination' => [
                'total' => $salaries->total(),
                'page' => $salaries->currentPage(),
                'limit' => $salaries->perPage(),
                'totalPages' => $salaries->lastPage(),
            ],
        ]);
    }

    public function show(Salary $salary): JsonResponse
    {
        return response()->json($salary->load('teacher'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'processing', 'paid'])],
            'notes' => ['nullable', 'string'],
        ]);

        $data['total_amount'] = ($data['base_salary'] ?? 0) + ($data['bonus'] ?? 0) - ($data['deduction'] ?? 0);

        $salary = Salary::create($data);

        return response()->json(['salary' => $salary->load('teacher')], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, Salary $salary): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => ['sometimes', 'exists:users,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'base_salary' => ['sometimes', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['pending', 'processing', 'paid'])],
            'notes' => ['nullable', 'string'],
        ]);

        if (isset($data['base_salary']) || isset($data['bonus']) || isset($data['deduction'])) {
            $baseSalary = $data['base_salary'] ?? $salary->base_salary;
            $bonus = $data['bonus'] ?? $salary->bonus ?? 0;
            $deduction = $data['deduction'] ?? $salary->deduction ?? 0;
            $data['total_amount'] = $baseSalary + $bonus - $deduction;
        }

        $salary->update($data);

        return response()->json(['salary' => $salary->fresh()->load('teacher')]);
    }

    public function destroy(Salary $salary): JsonResponse
    {
        $salary->delete();

        return response()->json(['message' => 'Salary deleted successfully']);
    }
}
