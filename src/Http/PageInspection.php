<?php

declare(strict_types=1);

namespace App\Http;

/**
 * What a single fetch of a page found: its HTTP status and whatever
 * SEO tags were present. Any of the strings may be empty — the page
 * simply had no h1, no title, or no meta description.
 */
class PageInspection
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $h1,
        public readonly string $title,
        public readonly string $description,
    ) {}
}
