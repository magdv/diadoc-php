<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Entities\Http;

/**
 * Параметры таймаутов HTTP-запросов (семантика как у Symfony HttpClient).
 *
 *  - timeout     — простой: установка соединения и пауза между байтами;
 *  - maxDuration — жёсткий потолок на весь запрос (0 — без потолка).
 *
 * Значения в целых секундах: этого достаточно для CURLOPT_* (секунды) и не
 * требует CURLOPT_TIMEOUT_MS, который на старых сборках libcurl может быть
 * не определён. Пока объект не передан в клиент, работает легаси-поведение.
 */
final readonly class HttpTimeouts
{
    public function __construct(
        public int $timeout = 30,
        public int $maxDuration = 30,
    ) {
        if ($this->timeout < 1) {
            throw new \InvalidArgumentException('timeout должен быть не меньше 1 секунды');
        }

        if ($this->maxDuration < 0) {
            throw new \InvalidArgumentException('maxDuration не может быть отрицательным');
        }
    }

    /**
     * @return array<int, int>
     */
    public function toCurlOptions(): array
    {
        $idle = $this->maxDuration > 0 ? \min($this->timeout, $this->maxDuration) : $this->timeout;

        return [
            \CURLOPT_CONNECTTIMEOUT => $idle,
            \CURLOPT_LOW_SPEED_LIMIT => 1,
            \CURLOPT_LOW_SPEED_TIME => $idle,
            \CURLOPT_TIMEOUT => $this->maxDuration,
        ];
    }
}
