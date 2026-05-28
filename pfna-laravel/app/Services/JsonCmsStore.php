<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JsonCmsStore
{
    private string $cmsPath;

    public function __construct()
    {
        $this->cmsPath = storage_path('app/cms');
        File::ensureDirectoryExists($this->cmsPath);
    }

    public function pageHtml(): string
    {
        $page = $this->readJson('page.json', ['html' => '']);

        return (string) ($page['html'] ?? '');
    }

    public function savePageHtml(string $html): void
    {
        $this->writeJson('page.json', [
            'html' => $html,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function posts(bool $publishedOnly = false): array
    {
        $posts = $this->readJson('posts.json', []);

        if ($publishedOnly) {
            $posts = array_values(array_filter($posts, fn ($post) => ($post['status'] ?? 'draft') === 'published'));
        }

        usort($posts, fn ($a, $b) => strcmp($b['updated_at'] ?? '', $a['updated_at'] ?? ''));

        return $posts;
    }

    public function experiences(): array
    {
        $experiences = $this->readJson('experiences.json', []);

        usort($experiences, function ($a, $b) {
            return ((int) ($a['sort_order'] ?? 0)) <=> ((int) ($b['sort_order'] ?? 0));
        });

        return $experiences;
    }

    public function achievements(): array
    {
        $achievements = $this->readJson('achievements.json', []);

        usort($achievements, function ($a, $b) {
            return ((int) ($a['sort_order'] ?? 0)) <=> ((int) ($b['sort_order'] ?? 0));
        });

        return $achievements;
    }

    public function settings(): array
    {
        return $this->readJson('settings.json', [
            'cv_url' => '/site/CV.pdf',
            'cv_original_name' => 'CV.pdf',
        ]);
    }

    public function saveSettings(array $settings): void
    {
        $current = $this->settings();

        $this->writeJson('settings.json', array_merge($current, $settings, [
            'updated_at' => now()->toIso8601String(),
        ]));
    }

    public function findExperience(string $id): ?array
    {
        foreach ($this->experiences() as $experience) {
            if (($experience['id'] ?? null) === $id) {
                return $experience;
            }
        }

        return null;
    }

    public function findAchievement(string $id): ?array
    {
        foreach ($this->achievements() as $achievement) {
            if (($achievement['id'] ?? null) === $id) {
                return $achievement;
            }
        }

        return null;
    }

    public function saveExperience(array $data, ?string $id = null): array
    {
        $experiences = $this->experiences();
        $now = now()->toIso8601String();
        $id ??= (string) Str::uuid();

        $experience = [
            'id' => $id,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'year' => $data['year'] ?? '',
            'badge_vi' => $data['badge_vi'] ?? '',
            'badge_en' => $data['badge_en'] ?? '',
            'badge_zh' => $data['badge_zh'] ?? '',
            'date_vi' => $data['date_vi'] ?? '',
            'date_en' => $data['date_en'] ?? '',
            'date_zh' => $data['date_zh'] ?? '',
            'title_vi' => $data['title_vi'] ?? '',
            'title_en' => $data['title_en'] ?? '',
            'title_zh' => $data['title_zh'] ?? '',
            'company_vi' => $data['company_vi'] ?? '',
            'company_en' => $data['company_en'] ?? '',
            'company_zh' => $data['company_zh'] ?? '',
            'description_vi' => $data['description_vi'] ?? '',
            'description_en' => $data['description_en'] ?? '',
            'description_zh' => $data['description_zh'] ?? '',
            'skills_vi' => $this->normalizeLines($data['skills_vi'] ?? ''),
            'skills_en' => $this->normalizeLines($data['skills_en'] ?? ''),
            'skills_zh' => $this->normalizeLines($data['skills_zh'] ?? ''),
            'created_at' => $data['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        $replaced = false;
        foreach ($experiences as $index => $existing) {
            if (($existing['id'] ?? null) === $id) {
                $experience['created_at'] = $existing['created_at'] ?? $experience['created_at'];
                $experiences[$index] = $experience;
                $replaced = true;
                break;
            }
        }

        if (! $replaced) {
            $experiences[] = $experience;
        }

        $this->writeJson('experiences.json', array_values($experiences));

        return $experience;
    }

    public function deleteExperience(string $id): void
    {
        $experiences = array_values(array_filter($this->experiences(), fn ($experience) => ($experience['id'] ?? null) !== $id));
        $this->writeJson('experiences.json', $experiences);
    }

    public function saveAchievement(array $data, ?string $id = null): array
    {
        $achievements = $this->achievements();
        $now = now()->toIso8601String();
        $id ??= (string) Str::uuid();

        $achievement = [
            'id' => $id,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'badge' => $data['badge'] ?? '',
            'date' => $data['date'] ?? '',
            'title_vi' => $data['title_vi'] ?? '',
            'title_en' => $data['title_en'] ?? '',
            'title_zh' => $data['title_zh'] ?? '',
            'organization_vi' => $data['organization_vi'] ?? '',
            'organization_en' => $data['organization_en'] ?? '',
            'organization_zh' => $data['organization_zh'] ?? '',
            'description_vi' => $data['description_vi'] ?? '',
            'description_en' => $data['description_en'] ?? '',
            'description_zh' => $data['description_zh'] ?? '',
            'created_at' => $data['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        $replaced = false;
        foreach ($achievements as $index => $existing) {
            if (($existing['id'] ?? null) === $id) {
                $achievement['created_at'] = $existing['created_at'] ?? $achievement['created_at'];
                $achievements[$index] = $achievement;
                $replaced = true;
                break;
            }
        }

        if (! $replaced) {
            $achievements[] = $achievement;
        }

        $this->writeJson('achievements.json', array_values($achievements));

        return $achievement;
    }

    public function deleteAchievement(string $id): void
    {
        $achievements = array_values(array_filter($this->achievements(), fn ($achievement) => ($achievement['id'] ?? null) !== $id));
        $this->writeJson('achievements.json', $achievements);
    }

    public function findPost(string $idOrSlug): ?array
    {
        foreach ($this->posts() as $post) {
            if (($post['id'] ?? null) === $idOrSlug || ($post['slug'] ?? null) === $idOrSlug) {
                return $post;
            }
        }

        return null;
    }

    public function savePost(array $data, ?string $id = null): array
    {
        $posts = $this->posts();
        $now = now()->toIso8601String();
        $id ??= (string) Str::uuid();
        $slug = Str::slug($data['slug'] ?: $data['title']);
        $slug = $slug !== '' ? $slug : $id;

        $post = [
            'id' => $id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($slug, $id, $posts),
            'excerpt' => $data['excerpt'] ?? '',
            'content' => $data['content'] ?? '',
            'cover_image' => $data['cover_image'] ?? '',
            'status' => $data['status'] ?? 'draft',
            'created_at' => $data['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        $replaced = false;
        foreach ($posts as $index => $existing) {
            if (($existing['id'] ?? null) === $id) {
                $post['created_at'] = $existing['created_at'] ?? $post['created_at'];
                $posts[$index] = $post;
                $replaced = true;
                break;
            }
        }

        if (! $replaced) {
            $posts[] = $post;
        }

        $this->writeJson('posts.json', array_values($posts));

        return $post;
    }

    public function deletePost(string $id): void
    {
        $posts = array_values(array_filter($this->posts(), fn ($post) => ($post['id'] ?? null) !== $id));
        $this->writeJson('posts.json', $posts);
    }

    public function storeUpload(UploadedFile $file, string $folder = 'media'): string
    {
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = $safeName !== '' ? $safeName : 'upload';
        $name = $safeName.'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $target = public_path('uploads/'.$folder);

        File::ensureDirectoryExists($target);
        $file->move($target, $name);

        return '/uploads/'.$folder.'/'.$name;
    }

    public function storeCv(UploadedFile $file): array
    {
        $url = $this->storeUpload($file, 'cv');
        $settings = [
            'cv_url' => $url,
            'cv_original_name' => $file->getClientOriginalName(),
        ];

        $this->saveSettings($settings);

        return $settings;
    }

    private function readJson(string $file, array $fallback): array
    {
        $path = $this->cmsPath.'/'.$file;

        if (! File::exists($path)) {
            return $fallback;
        }

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : $fallback;
    }

    private function writeJson(string $file, array $data): void
    {
        File::put(
            $this->cmsPath.'/'.$file,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function normalizeLines(string|array $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        $lines = preg_split('/\r\n|\r|\n|,/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }

    private function uniqueSlug(string $slug, string $id, array $posts): string
    {
        $base = $slug;
        $counter = 2;
        $slugs = [];

        foreach ($posts as $post) {
            if (($post['id'] ?? null) !== $id) {
                $slugs[] = $post['slug'] ?? '';
            }
        }

        while (in_array($slug, $slugs, true)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
