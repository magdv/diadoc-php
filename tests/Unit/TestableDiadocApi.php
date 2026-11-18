<?php

declare(strict_types=1);

namespace Test\Unit;

use MagDv\Diadoc\DiadocApi;
use MagDv\Diadoc\Entities\Http\HttpTimeouts;

/**
 * Подкласс DiadocApi для доступа к protected-методам в тестах.
 */
class TestableDiadocApi extends DiadocApi
{
    public function __construct(?HttpTimeouts $timeouts = null)
    {
        parent::__construct('dd-client', 'https://diadoc-api.kontur.ru/', timeouts: $timeouts);
    }

    public function exposeLogRequest(string $uri, mixed $postData, string $method, int $httpCode, string $response): void
    {
        $this->logRequest($uri, $postData, $method, $httpCode, $response);
    }

    /**
     * @return array<int, int>
     */
    public function exposeBuildTimeoutOptions(): array
    {
        return $this->buildTimeoutOptions();
    }
}
