<?php

namespace Tests\Unit;

use App\Support\RichTextSanitizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RichTextSanitizerTest extends TestCase
{
    #[Test]
    public function it_removes_executable_html_and_keeps_basic_formatting(): void
    {
        $html = '<p onclick="alert(1)"><strong>Safe</strong><script>alert(2)</script>'
            .'<a href="javascript:alert(3)">link</a></p>';

        $sanitized = (new RichTextSanitizer)->sanitize($html);

        $this->assertStringContainsString('<strong>Safe</strong>', $sanitized);
        $this->assertStringNotContainsStringIgnoringCase('<script', $sanitized);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $sanitized);
        $this->assertStringNotContainsStringIgnoringCase('javascript:', $sanitized);
    }

    #[Test]
    public function it_only_keeps_supported_raster_data_images(): void
    {
        $png = 'data:image/png;base64,'.base64_encode('png');
        $svg = 'data:image/svg+xml;base64,'.base64_encode('<svg><script>alert(1)</script></svg>');

        $sanitized = (new RichTextSanitizer)->sanitize(
            '<img src="'.$png.'" alt="ok"><img src="'.$svg.'" alt="bad">',
        );

        $this->assertStringContainsString($png, $sanitized);
        $this->assertStringNotContainsString('image/svg+xml', $sanitized);
    }
}
