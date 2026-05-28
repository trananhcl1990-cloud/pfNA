<?php

namespace App\Http\Controllers;

use App\Services\JsonCmsStore;

class SiteController extends Controller
{
    public function home(JsonCmsStore $cms)
    {
        return view('site.home', [
            'pageHtml' => $this->withDynamicContent($cms->pageHtml(), $cms),
            'posts' => array_slice($cms->posts(true), 0, 6),
        ]);
    }

    public function blog(JsonCmsStore $cms)
    {
        return view('site.blog', [
            'posts' => $cms->posts(true),
        ]);
    }

    public function post(string $slug, JsonCmsStore $cms)
    {
        $post = $cms->findPost($slug);

        abort_if(! $post || ($post['status'] ?? 'draft') !== 'published', 404);

        return view('site.post', compact('post'));
    }

    private function withDynamicContent(string $html, JsonCmsStore $cms): string
    {
        $html = $this->withDynamicExperience($html, $cms->experiences());
        $html = $this->withDynamicAchievements($html, $cms->achievements());

        return $this->withDynamicCv($html, $cms->settings());
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
