<?php

namespace Tests\Unit;

use App\Support\PathNormalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathNormalizerTest extends TestCase
{
    #[Test]
    public function strips_storage_and_public_prefixes_and_backslashes(): void
    {
        $this->assertSame('foo/bar.pdf', PathNormalizer::normalize('storage/app/public/foo/bar.pdf'));
        $this->assertSame('foo/bar.pdf', PathNormalizer::normalize('public/foo/bar.pdf'));
        $this->assertSame('foo/bar.pdf', PathNormalizer::normalize('storage/foo/bar.pdf'));
        $this->assertSame('foo/bar.pdf', PathNormalizer::normalize('public\\foo\\bar.pdf'));
        $this->assertSame('foo/bar.pdf', PathNormalizer::normalize('/foo/bar.pdf'));
    }

    #[Test]
    public function null_and_empty_inputs(): void
    {
        $this->assertNull(PathNormalizer::normalize(null));
        $this->assertSame('', PathNormalizer::normalize(''));
    }
}

