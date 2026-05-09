<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoommateRequest;
use App\Http\Requests\StoreTasksRequest;
use App\Models\Coloc;
use App\Models\Roommate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function index(Coloc $coloc): View
    {
        $coloc->load(['roommates', 'tasks']);

        $step = $coloc->roommates->count() >= 2
            ? (int) request()->query('step', 2)
            : 1;

        return view('setup.index', [
            'coloc' => $coloc,
            'roommates' => $coloc->roommates,
            'tasks' => $coloc->tasks,
            'currentStep' => $step,
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

        return back();
    }

    public function destroyRoommate(Coloc $coloc, Roommate $roommate): RedirectResponse
    {
        $roommate->delete();

        $coloc->roommates()->orderBy('order')->get()->each(function (Roommate $r, int $index): void {
            $r->update(['order' => $index]);
        });

        return back();
    }

    public function storeTasks(StoreTasksRequest $request, Coloc $coloc): RedirectResponse
    {
        if ($coloc->roommates()->count() < 2) {
            return back()->withErrors(['tasks' => 'Il faut au moins 2 colocs pour continuer.']);
        }

        $enabledIds = $request->validated('tasks');

        $coloc->tasks()->update(['enabled' => false]);
        $coloc->tasks()->whereIn('id', $enabledIds)->update(['enabled' => true]);

        return redirect()->route('coloc.dashboard', $coloc);
    }
}
