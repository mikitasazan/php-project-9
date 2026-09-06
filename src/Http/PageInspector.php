<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Fetches a page and pulls out the tags a search engine would look at.
 * Any network problem (refused connection, timeout, a 4xx/5xx status —
 * Guzzle throws on all of those by default) surfaces as one exception
 * so the caller doesn't have to know Guzzle's exception hierarchy.
 */
class PageInspector
{
    private const TIMEOUT_SECONDS = 2.0;

    public function __construct(
        private readonly Client $client = new Client(),
    ) {}

    /**
     * @throws PageUnreachableException
     */
    public function inspect(string $url): PageInspection
    {
        try {
            $response = $this->client->get($url, [
                'connect_timeout' => self::TIMEOUT_SECONDS,
                'timeout' => self::TIMEOUT_SECONDS,
            ]);
        } catch (GuzzleException $e) {
            throw new PageUnreachableException($url, previous: $e);
        }

        $crawler = new Crawler((string) $response->getBody());

        return new PageInspection(
            statusCode: $response->getStatusCode(),
            h1: $this->firstText($crawler, 'h1'),
            title: $this->firstText($crawler, 'title'),
            description: $this->firstAttribute($crawler, 'meta[name=description]', 'content'),
        );
    }

    private function firstText(Crawler $crawler, string $selector): string
    {
        $node = $crawler->filter($selector)->first();

        return $node->count() > 0 ? trim($node->text()) : '';
    }

    private function firstAttribute(Crawler $crawler, string $selector, string $attribute): string
    {
        $node = $crawler->filter($selector)->first();

        return $node->count() > 0 ? (string) $node->attr($attribute) : '';
    }
}
