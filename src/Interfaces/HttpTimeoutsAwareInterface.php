<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Interfaces;

use MagDv\Diadoc\Entities\Http\HttpTimeouts;

/**
 * Режим авторизации, которому можно передать параметры таймаутов HTTP-запросов.
 *
 * Вынесен отдельно от AuthModeInterface, чтобы не ломать сторонние реализации:
 * режимы без этого интерфейса продолжают работать как раньше.
 */
interface HttpTimeoutsAwareInterface
{
    /**
     * @param HttpTimeouts|null $timeouts null — вернуть легаси-поведение
     */
    public function setHttpTimeouts(?HttpTimeouts $timeouts): void;

    public function getHttpTimeouts(): ?HttpTimeouts;
}
