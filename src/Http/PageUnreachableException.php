<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;
use Throwable;

class PageUnreachableException extends RuntimeException
{
    public function __construct(string $url, ?Throwable $previous = null)
    {
        parent::__construct("Could not reach {$url}", previous: $previous);
    }
}
