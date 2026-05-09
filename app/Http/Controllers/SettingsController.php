<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoommateRequest;
use App\Http\Requests\StoreTasksRequest;
use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $avatarSlug = $request->validated('avatar_slug');
        $avatarUrl = null;

        if ($request->hasFile('avatar_photo')) {
            $avatarUrl = $request->file('avatar_photo')->store('avatars', 'public');
            $avatarSlug = $avatarSlug ?: 'personnage-01';
        } elseif (! $avatarSlug) {
            return back()->withErrors(['avatar_slug' => 'Choisis un avatar ou importe une photo.'])->withInput();
        } else {
            $takenAvatars = $coloc->roommates()->pluck('avatar_slug')->toArray();
            if (in_array($avatarSlug, $takenAvatars, true)) {
                return back()->withErrors(['avatar_slug' => 'Cet avatar est déjà pris !'])->withInput();
            }
        }

        $coloc->roommates()->create([
            'first_name' => $request->validated('first_name'),
            'avatar_slug' => $avatarSlug,
            'avatar_url' => $avatarUrl,
            'order' => $coloc->roommates()->count(),
        ]);

        return back()->with('success', 'Coloc ajouté !');
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

        return back()->with('success', 'Tâches mises à jour !');
    }

    public function storeCustomTask(Request $request, Coloc $coloc): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:40',
            'icon_slug' => 'required|string|max:30',
        ]);

        $coloc->tasks()->create([
            'name' => $validated['name'],
            'icon_slug' => $validated['icon_slug'],
            'enabled' => true,
            'order' => $coloc->tasks()->count(),
        ]);

        return back()->with('success', 'Tâche ajoutée !');
    }

    public function destroyTask(Coloc $coloc, Task $task): RedirectResponse
    {
        $task->completions()->delete();
        $task->delete();

        $coloc->tasks()->orderBy('order')->get()->each(function (Task $t, int $index): void {
            $t->update(['order' => $index]);
        });

        return back()->with('success', 'Tâche supprimée.');
    }
}
