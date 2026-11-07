<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\LegacyName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LegacyNameController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        $legacyNames = LegacyName::query()
            ->whereIn('family_id', $familyIds)
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('legacy-names.index', compact('legacyNames'));
    }

    public function show(LegacyName $legacyName)
    {
        return view('legacy-names.show', compact('legacyName'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $familyId = FamilyMember::where('user_id', $user->id)->value('family_id');

        return view('legacy-names.create', compact('familyId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $familyId = FamilyMember::where('user_id', $user->id)->value('family_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meaning' => 'nullable|string|max:1000',
            'story_behind_the_name' => 'nullable|string|max:3000',
            'who_gave_this_name' => 'nullable|string|max:255',
            'family_generation' => 'nullable|string|max:50',
        ]);

        $legacyName = LegacyName::create(array_merge($validated, [
            'family_id' => $familyId,
        ]));

        return redirect()->route('user.legacy-names', $legacyName)
            ->with('success', 'Legacy name created successfully.');
    }

    public function edit(LegacyName $legacyName)
    {
        return view('legacy-names.edit', compact('legacyName'));
    }

    public function update(Request $request, LegacyName $legacyName)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meaning' => 'nullable|string|max:1000',
            'story_behind_the_name' => 'nullable|string|max:3000',
            'who_gave_this_name' => 'nullable|string|max:255',
            'family_generation' => 'nullable|string|max:50',
        ]);

        $legacyName->update($validated);

        return redirect()->route('user.legacy-names', $legacyName)
            ->with('success', 'Legacy name updated successfully.');
    }

    public function destroy(LegacyName $legacyName)
    {
        $legacyName->delete();

        return redirect()->route('user.legacy-names')
            ->with('success', 'Legacy name deleted successfully.');
    }
}
