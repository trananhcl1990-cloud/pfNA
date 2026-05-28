<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(JsonCmsStore $cms)
    {
        return view('admin.experiences.index', [
            'experiences' => $cms->experiences(),
        ]);
    }

    public function create()
    {
        return view('admin.experiences.form', [
            'experience' => null,
        ]);
    }

    public function store(Request $request, JsonCmsStore $cms)
    {
        $experience = $cms->saveExperience($this->validated($request));

        return redirect()->route('admin.experiences.edit', $experience['id'])->with('status', 'Đã thêm kinh nghiệm làm việc.');
    }

    public function edit(string $id, JsonCmsStore $cms)
    {
        $experience = $cms->findExperience($id);
        abort_if(! $experience, 404);

        return view('admin.experiences.form', compact('experience'));
    }

    public function update(Request $request, string $id, JsonCmsStore $cms)
    {
        $cms->saveExperience($this->validated($request), $id);

        return back()->with('status', 'Đã lưu kinh nghiệm làm việc.');
    }

    public function destroy(string $id, JsonCmsStore $cms)
    {
        $cms->deleteExperience($id);

        return redirect()->route('admin.experiences.index')->with('status', 'Đã xoá kinh nghiệm làm việc.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
            'year' => ['nullable', 'string', 'max:20'],
            'badge_vi' => ['nullable', 'string', 'max:80'],
            'badge_en' => ['nullable', 'string', 'max:80'],
            'badge_zh' => ['nullable', 'string', 'max:80'],
            'date_vi' => ['required', 'string', 'max:120'],
            'date_en' => ['nullable', 'string', 'max:120'],
            'date_zh' => ['nullable', 'string', 'max:120'],
            'title_vi' => ['required', 'string', 'max:180'],
            'title_en' => ['nullable', 'string', 'max:180'],
            'title_zh' => ['nullable', 'string', 'max:180'],
            'company_vi' => ['required', 'string', 'max:180'],
            'company_en' => ['nullable', 'string', 'max:180'],
            'company_zh' => ['nullable', 'string', 'max:180'],
            'description_vi' => ['required', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
            'description_zh' => ['nullable', 'string', 'max:1000'],
            'skills_vi' => ['nullable', 'string', 'max:1000'],
            'skills_en' => ['nullable', 'string', 'max:1000'],
            'skills_zh' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
