<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoommateRequest;
use App\Http\Requests\StoreTasksRequest;
use App\Models\Coloc;
use App\Models\Roommate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Coloc $coloc): View
    {
        $coloc->load(['roommates', 'tasks']);

        return view('settings', [
            'coloc' => $coloc,
            'roommates' => $coloc->roommates,
            'tasks' => $coloc->tasks,
            'takenAvatars' => $coloc->roommates->pluck('avatar_slug')->toArray(),
        ]);
    }

    public function storeRoommate(StoreRoommateRequest $request, Coloc $coloc): RedirectResponse
    {
        $takenAvatars = $coloc->roommates()->pluck('avatar_slug')->toArray();

        if (in_array($request->validated('avatar_slug'), $takenAvatars, true)) {
            return back()->withErrors(['avatar_slug' => 'Cet avatar est deja pris !'])->withInput();
        }

        $coloc->roommates()->create([
            'first_name' => $request->validated('first_name'),
            'avatar_slug' => $request->validated('avatar_slug'),
            'order' => $coloc->roommates()->count(),
        ]);

        return back()->with('success', 'Coloc ajoute !');
    }

    public function destroyRoommate(Coloc $coloc, Roommate $roommate): RedirectResponse
    {
        $roommate->delete();

        $coloc->roommates()->orderBy('order')->get()->each(function (Roommate $r, int $index): void {
            $r->update(['order' => $index]);
        });

        $remaining = $coloc->roommates()->count();
        if ($remaining < 2) {
            return back()->with('warning', 'Attention : il faut au moins 2 colocs pour que la rotation fonctionne.');
        }

        return back();
    }

    public function storeTasks(StoreTasksRequest $request, Coloc $coloc): RedirectResponse
    {
        $enabledIds = $request->validated('tasks');

        $coloc->tasks()->update(['enabled' => false]);
        $coloc->tasks()->whereIn('id', $enabledIds)->update(['enabled' => true]);

        return back()->with('success', 'Taches mises a jour !');
    }
}
