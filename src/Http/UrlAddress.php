<?php

declare(strict_types=1);

namespace App\Http;

use Valitron\Validator;

/**
 * Validates a submitted address and reduces it to the part we actually
 * store: scheme + host, lower-cased. Two pages on the same host are the
 * same tracked site, whatever path or query the visitor typed.
 */
class UrlAddress
{
    private const MAX_LENGTH = 255;
    private const ALLOWED_SCHEMES = ['http', 'https'];

    public static function isValid(string $raw): bool
    {
        $validator = new Validator(['url' => $raw]);
        $validator->rules([
            'required' => ['url'],
            'url' => ['url'],
            'lengthMax' => [['url', self::MAX_LENGTH]],
        ]);

        if (!$validator->validate()) {
            return false;
        }

        $scheme = parse_url($raw, PHP_URL_SCHEME);

        return is_string($scheme) && in_array(strtolower($scheme), self::ALLOWED_SCHEMES, strict: true);
    }

    public static function normalize(string $raw): string
    {
        $scheme = strtolower((string) parse_url($raw, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($raw, PHP_URL_HOST));

        return "{$scheme}://{$host}";
    }
}
