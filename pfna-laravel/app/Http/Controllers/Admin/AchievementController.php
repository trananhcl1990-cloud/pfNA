<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(JsonCmsStore $cms)
    {
        return view('admin.achievements.index', [
            'achievements' => $cms->achievements(),
        ]);
    }

    public function create()
    {
        return view('admin.achievements.form', [
            'achievement' => null,
        ]);
    }

    public function store(Request $request, JsonCmsStore $cms)
    {
        $achievement = $cms->saveAchievement($this->validated($request));

        return redirect()->route('admin.achievements.edit', $achievement['id'])->with('status', 'Da them thanh tich.');
    }

    public function edit(string $id, JsonCmsStore $cms)
    {
        $achievement = $cms->findAchievement($id);
        abort_if(! $achievement, 404);

        return view('admin.achievements.form', compact('achievement'));
    }

    public function update(Request $request, string $id, JsonCmsStore $cms)
    {
        $cms->saveAchievement($this->validated($request), $id);

        return back()->with('status', 'Da luu thanh tich.');
    }

    public function destroy(string $id, JsonCmsStore $cms)
    {
        $cms->deleteAchievement($id);

        return redirect()->route('admin.achievements.index')->with('status', 'Da xoa thanh tich.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
            'badge' => ['nullable', 'string', 'max:120'],
            'date' => ['nullable', 'string', 'max:120'],
            'title_vi' => ['required', 'string', 'max:220'],
            'title_en' => ['nullable', 'string', 'max:220'],
            'title_zh' => ['nullable', 'string', 'max:220'],
            'organization_vi' => ['nullable', 'string', 'max:220'],
            'organization_en' => ['nullable', 'string', 'max:220'],
            'organization_zh' => ['nullable', 'string', 'max:220'],
            'description_vi' => ['required', 'string', 'max:1200'],
            'description_en' => ['nullable', 'string', 'max:1200'],
            'description_zh' => ['nullable', 'string', 'max:1200'],
        ]);
    }
}
