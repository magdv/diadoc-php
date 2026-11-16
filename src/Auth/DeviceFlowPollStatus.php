<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

/**
 * Статусы ответа /connect/token при опросе в Device Flow (RFC 8628).
 */
enum DeviceFlowPollStatus
{
    /**
     * Токены получены.
     */
    case Success;
    /**
     * Пользователь ещё не прошёл авторизацию — продолжать опрос.
     */
    case AuthorizationPending;
    /**
     * Опрос слишком частый — увеличить интервал (+5 сек) и продолжить.
     */
    case SlowDown;
    /**
     * device_code истёк — нужно начать флоу заново.
     */
    case ExpiredToken;
    /**
     * Пользователь отклонил авторизацию — нужно начать флоу заново.
     */
    case AccessDenied;
}
