<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteTaskRequest;
use App\Http\Requests\StoreColocRequest;
use App\Models\Coloc;
use App\Models\TaskCompletion;
use App\Services\RotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ColocController extends Controller
{
    public function __construct(
        private RotationService $rotation,
    ) {}

    public function index(): View
    {
        return view('landing');
    }

    public function store(StoreColocRequest $request): RedirectResponse
    {
        $coloc = Coloc::create(['name' => $request->validated('name')]);
        $coloc->createDefaultTasks();

        return redirect()->route('coloc.setup', $coloc);
    }

    public function show(Coloc $coloc): View|RedirectResponse
    {
        $coloc->load(['roommates', 'tasks']);

        if ($coloc->roommates->isEmpty() || $coloc->tasks->where('enabled', true)->isEmpty()) {
            return redirect()->route('coloc.setup', $coloc);
        }

        $weekInfo = $this->rotation->getActiveWeekAndYear();
        $assignments = $this->rotation->getAssignments($coloc, $weekInfo['week'], $weekInfo['year']);

        $completions = $coloc->taskCompletions()
            ->where('week', $weekInfo['week'])
            ->where('year', $weekInfo['year'])
            ->get()
            ->keyBy('task_id');

        return view('dashboard', [
            'coloc' => $coloc,
            'assignments' => $assignments,
            'completions' => $completions,
            'weekRange' => $this->rotation->formatWeekRange(),
            'weekInfo' => $weekInfo,
        ]);
    }

    public function complete(CompleteTaskRequest $request, Coloc $coloc): JsonResponse
    {
        $weekInfo = $this->rotation->getActiveWeekAndYear();
        $validated = $request->validated();

        TaskCompletion::updateOrCreate(
            [
                'task_id' => $validated['task_id'],
                'week' => $weekInfo['week'],
                'year' => $weekInfo['year'],
            ],
            [
                'coloc_id' => $coloc->id,
                'assigned_roommate_id' => $validated['assigned_roommate_id'],
                'actual_roommate_id' => $validated['actual_roommate_id'] ?? null,
                'status' => $validated['status'],
            ],
        );

        return response()->json(['success' => true, 'status' => $validated['status']]);
    }
}
