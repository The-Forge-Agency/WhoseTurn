<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteTaskRequest;
use App\Http\Requests\StoreColocRequest;
use App\Models\Coloc;
use App\Models\TaskCompletion;
use App\Models\UrgentTodo;
use App\Services\RotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $urgentTodos = $coloc->urgentTodos()
            ->where('done', false)
            ->latest()
            ->get();

        return view('dashboard', [
            'coloc' => $coloc,
            'assignments' => $assignments,
            'completions' => $completions,
            'weekRange' => $this->rotation->formatWeekRange(),
            'weekInfo' => $weekInfo,
            'urgentTodos' => $urgentTodos,
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

    public function storeTodo(Request $request, Coloc $coloc): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
        ]);

        $coloc->urgentTodos()->create([
            'title' => $validated['title'],
        ]);

        return back();
    }

    public function completeTodo(Coloc $coloc, UrgentTodo $todo): RedirectResponse
    {
        $todo->update(['done' => true]);

        return back();
    }

    public function deleteTodo(Coloc $coloc, UrgentTodo $todo): RedirectResponse
    {
        $todo->delete();

        return back();
    }

    public function stats(Coloc $coloc): View
    {
        $coloc->load(['roommates', 'tasks']);

        $fourWeeksAgo = now()->subWeeks(4);
        $completions = $coloc->taskCompletions()
            ->where('created_at', '>=', $fourWeeksAgo)
            ->with(['actualRoommate'])
            ->get();

        $roommates = $coloc->roommates;
        $tasks = $coloc->tasks->where('enabled', true);

        $roommateStats = $roommates->mapWithKeys(function ($roommate) use ($completions) {
            $assigned = $completions->where('assigned_roommate_id', $roommate->id);
            $done = $assigned->where('status', 'done')->count();
            $notDone = $assigned->where('status', 'not_done')->count();
            $doneByOther = $assigned->where('status', 'done_by_other')->count();
            $helpedOthers = $completions->where('actual_roommate_id', $roommate->id)->count();
            $total = $assigned->count();

            return [$roommate->id => [
                'roommate' => $roommate,
                'done' => $done,
                'not_done' => $notDone,
                'done_by_other' => $doneByOther,
                'helped_others' => $helpedOthers,
                'total' => $total,
                'rate' => $total > 0 ? round(($done / $total) * 100) : 0,
            ]];
        });

        $taskStats = $tasks->mapWithKeys(function ($task) use ($completions, $roommates) {
            $taskCompletions = $completions->where('task_id', $task->id);
            $perRoommate = $roommates->mapWithKeys(function ($r) use ($taskCompletions) {
                return [$r->id => $taskCompletions->where('assigned_roommate_id', $r->id)->count()];
            });

            return [$task->id => [
                'task' => $task,
                'total' => $taskCompletions->count(),
                'done' => $taskCompletions->where('status', 'done')->count(),
                'per_roommate' => $perRoommate,
            ]];
        });

        $todosTotal = $coloc->urgentTodos()->count();
        $todosDone = $coloc->urgentTodos()->where('done', true)->count();

        return view('stats', [
            'coloc' => $coloc,
            'roommates' => $roommates,
            'roommateStats' => $roommateStats,
            'taskStats' => $taskStats,
            'todosTotal' => $todosTotal,
            'todosDone' => $todosDone,
        ]);
    }

    public function history(Coloc $coloc): View
    {
        $coloc->load(['roommates', 'tasks']);

        $weekInfo = $this->rotation->getActiveWeekAndYear();
        $weeks = collect();

        for ($i = 0; $i < 4; $i++) {
            $w = $weekInfo['week'] - $i;
            $y = $weekInfo['year'];
            if ($w < 1) {
                $w += 52;
                $y--;
            }

            $assignments = $this->rotation->getAssignments($coloc, $w, $y);
            $completions = $coloc->taskCompletions()
                ->where('week', $w)
                ->where('year', $y)
                ->with('actualRoommate')
                ->get()
                ->keyBy('task_id');

            $weeks->push([
                'week' => $w,
                'year' => $y,
                'label' => $this->rotation->formatWeekRangeFor($w, $y),
                'assignments' => $assignments,
                'completions' => $completions,
                'isCurrent' => $i === 0,
            ]);
        }

        return view('history', [
            'coloc' => $coloc,
            'weeks' => $weeks,
        ]);
    }
}
