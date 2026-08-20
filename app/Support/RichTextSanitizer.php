<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class RichTextSanitizer
{
    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowElement('img', ['src', 'alt', 'width', 'height'])
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowRelativeLinks()
            ->allowMediaSchemes(['http', 'https', 'data'])
            ->allowRelativeMedias()
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->dropElement('audio')
            ->dropElement('video')
            ->dropElement('source')
            ->dropElement('svg')
            ->withMaxInputLength(16_777_215);

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public function sanitize(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        // Rich text supports pasted raster images. Reject SVG and arbitrary data URIs.
        $html = preg_replace_callback(
            '/(<img\b[^>]*\bsrc\s*=\s*)(["\'])(.*?)\2/isu',
            static function (array $matches): string {
                $source = trim(html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if (str_starts_with(strtolower($source), 'data:')
                    && ! preg_match('/^data:image\/(?:png|jpe?g|gif|webp);base64,[a-z0-9+\/=\r\n]+$/i', $source)) {
                    return $matches[1].$matches[2].$matches[2];
                }

                return $matches[0];
            },
            $html,
        ) ?? '';

        return $this->sanitizer->sanitize($html);
    }
}
