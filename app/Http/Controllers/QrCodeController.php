<?php

namespace App\Http\Controllers;

use App\Models\Coloc;
use App\Models\Task;
use App\Services\RotationService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __construct(
        private RotationService $rotation,
    ) {}

    public function index(Coloc $coloc): View
    {
        $tasks = $coloc->tasks()->enabled()->orderBy('order')->get();

        return view('qr.index', [
            'coloc' => $coloc,
            'tasks' => $tasks,
        ]);
    }

    public function coloc(Coloc $coloc): Response
    {
        $url = route('coloc.dashboard', $coloc);
        $svg = QrCode::format('svg')->size(300)->margin(0)->generate($url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function task(Coloc $coloc, Task $task): Response
    {
        $url = route('coloc.task', [$coloc, $task]);
        $svg = QrCode::format('svg')->size(200)->margin(0)->generate($url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function showTask(Coloc $coloc, Task $task): View
    {
        $weekInfo = $this->rotation->getActiveWeekAndYear();
        $assignments = $this->rotation->getAssignments($coloc, $weekInfo['week'], $weekInfo['year']);

        $assignment = $assignments->firstWhere('task.id', $task->id);
        $roommate = $assignment ? $assignment['roommate'] : null;

        return view('qr.task', [
            'coloc' => $coloc,
            'task' => $task,
            'roommate' => $roommate,
            'weekRange' => $this->rotation->formatWeekRange(),
        ]);
    }
}
