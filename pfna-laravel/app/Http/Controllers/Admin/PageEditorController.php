<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use Illuminate\Http\Request;

class PageEditorController extends Controller
{
    public function edit(JsonCmsStore $cms)
    {
        return view('admin.page-editor', [
            'pageHtml' => $this->withDynamicContent($cms->pageHtml(), $cms),
        ]);
    }

    public function update(Request $request, JsonCmsStore $cms)
    {
        $data = $request->validate([
            'html' => ['required', 'string'],
        ]);

        $cms->savePageHtml($data['html']);

        return response()->json(['ok' => true]);
    }

    public function upload(Request $request, JsonCmsStore $cms)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg,mp4,webm,pdf', 'max:51200'],
        ]);

        return response()->json([
            'url' => $cms->storeUpload($request->file('file')),
        ]);
    }

    private function withDynamicExperience(string $html, array $experiences): string
    {
        if ($experiences === []) {
            return $html;
        }

        $experienceHtml = view('site.partials.experience', compact('experiences'))->render();

        return preg_replace(
            '/<section id="experience" class="section-container dark-section">.*?(?=<section id="achievements")/s',
            $experienceHtml,
            $html,
            1
        ) ?? $html;
    }

    private function withDynamicContent(string $html, JsonCmsStore $cms): string
    {
        $html = $this->withDynamicExperience($html, $cms->experiences());
        $html = $this->withDynamicAchievements($html, $cms->achievements());

        return $this->withDynamicCv($html, $cms->settings());
    }

    private function withDynamicCv(string $html, array $settings): string
    {
        $cvUrl = e($settings['cv_url'] ?? '/site/CV.pdf');

        return preg_replace(
            '/href="[^"]*" class="btn-primary" target="_blank" rel="noopener noreferrer" download/',
            'href="'.$cvUrl.'" class="btn-primary" target="_blank" rel="noopener noreferrer" download',
            $html,
            1
        ) ?? $html;
    }

    private function withDynamicAchievements(string $html, array $achievements): string
    {
        if ($achievements === []) {
            return $html;
        }

        $achievementHtml = view('site.partials.achievements', compact('achievements'))->render();

        return preg_replace(
            '/<section id="achievements" class="section-container">.*?(?=<section id="skills")/s',
            $achievementHtml,
            $html,
            1
        ) ?? $html;
    }
}
