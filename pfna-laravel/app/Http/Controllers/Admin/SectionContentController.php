<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\Request;

class SectionContentController extends Controller
{
    private array $sections = [
        'about' => 'Giới thiệu',
        'skills' => 'Kỹ năng',
        'hobbies' => 'Sở thích',
        'languages' => 'Ngôn ngữ',
        'projects' => 'Dự án',
        'contact' => 'Liên hệ',
    ];

    public function edit(JsonCmsStore $cms)
    {
        return view('admin.sections.edit', [
            'sections' => $this->extractSections($cms->pageHtml()),
        ]);
    }

    public function update(Request $request, JsonCmsStore $cms)
    {
        $data = $request->validate([
            'items' => ['array'],
            'items.*.*.vi' => ['nullable', 'string', 'max:5000'],
            'items.*.*.en' => ['nullable', 'string', 'max:5000'],
            'items.*.*.zh' => ['nullable', 'string', 'max:5000'],
        ]);

        $html = $cms->pageHtml();

        foreach (($data['items'] ?? []) as $sectionId => $items) {
            if (! array_key_exists($sectionId, $this->sections)) {
                continue;
            }

            $html = $this->replaceSection($html, $sectionId, $items);
        }

        $cms->savePageHtml($html);

        return back()->with('status', 'Đã lưu nội dung các mục.');
    }

    private function extractSections(string $html): array
    {
        $sections = [];

        foreach ($this->sections as $id => $label) {
            $sectionHtml = $this->findSectionHtml($html, $id);
            $sections[$id] = [
                'label' => $label,
                'items' => $sectionHtml ? $this->extractTranslatableItems($sectionHtml) : [],
            ];
        }

        return $sections;
    }

    private function replaceSection(string $html, string $sectionId, array $items): string
    {
        $sectionHtml = $this->findSectionHtml($html, $sectionId);
        if (! $sectionHtml) {
            return $html;
        }

        $updatedSection = $this->updateSectionHtml($sectionHtml, $items);

        return preg_replace_callback(
            $this->sectionPattern($sectionId),
            fn () => $updatedSection,
            $html,
            1
        ) ?? $html;
    }

    private function findSectionHtml(string $html, string $sectionId): ?string
    {
        if (! preg_match($this->sectionPattern($sectionId), $html, $matches)) {
            return null;
        }

        return $matches[0];
    }

    private function sectionPattern(string $sectionId): string
    {
        return '/<section id="'.preg_quote($sectionId, '/').'"[\s\S]*?(?=<section id="|<footer)/';
    }

    private function extractTranslatableItems(string $sectionHtml): array
    {
        [$dom, $xpath] = $this->loadFragment($sectionHtml);
        $nodes = $xpath->query('//*[@data-vi]');
        $items = [];

        foreach ($nodes as $index => $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $text = trim($node->textContent);
            if ($text === '') {
                continue;
            }

            $items[$index] = [
                'tag' => strtolower($node->tagName),
                'label' => mb_substr($text, 0, 80),
                'vi' => $node->getAttribute('data-vi'),
                'en' => $node->getAttribute('data-en'),
                'zh' => $node->getAttribute('data-zh'),
            ];
        }

        return $items;
    }

    private function updateSectionHtml(string $sectionHtml, array $items): string
    {
        [$dom, $xpath] = $this->loadFragment($sectionHtml);
        $nodes = $xpath->query('//*[@data-vi]');

        foreach ($nodes as $index => $node) {
            if (! $node instanceof DOMElement || ! isset($items[$index])) {
                continue;
            }

            $values = $items[$index];
            $vi = trim((string) ($values['vi'] ?? ''));
            $en = trim((string) ($values['en'] ?? ''));
            $zh = trim((string) ($values['zh'] ?? ''));

            $node->setAttribute('data-vi', $vi);
            $node->setAttribute('data-en', $en !== '' ? $en : $vi);
            $node->setAttribute('data-zh', $zh !== '' ? $zh : $vi);

            if (! $this->hasElementChildren($node)) {
                $node->textContent = $vi;
            }
        }

        return $this->saveFragment($dom);
    }

    private function loadFragment(string $html): array
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="cms-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        return [$dom, new DOMXPath($dom)];
    }

    private function saveFragment(DOMDocument $dom): string
    {
        $root = $dom->getElementById('cms-root');
        $html = '';

        foreach ($root?->childNodes ?? [] as $child) {
            $html .= $dom->saveHTML($child);
        }

        return $html;
    }

    private function hasElementChildren(DOMElement $node): bool
    {
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                return true;
            }
        }

        return false;
    }
}
