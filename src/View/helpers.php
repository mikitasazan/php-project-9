<?php

declare(strict_types=1);

use Slim\Interfaces\RouteParserInterface;

/**
 * HTML-escape for interpolating into a template.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, encoding: 'UTF-8');
}

/**
 * Cut a string down to $limit characters, marking the cut with "...".
 * Left alone if it already fits.
 */
function truncate(string $value, int $limit = 200): string
{
    if (mb_strlen($value) <= $limit) {
        return $value;
    }

    return mb_substr($value, start: 0, length: $limit) . '...';
}

/**
 * Build a path from a named route, the only way templates reference a URL.
 */
function routeUrl(RouteParserInterface $routeParser, string $name, array $params = []): string
{
    return $routeParser->urlFor($name, $params);
}
