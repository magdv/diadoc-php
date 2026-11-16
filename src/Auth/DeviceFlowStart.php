<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

/**
 * Результат запроса POST /connect/deviceauthorization (начало Device Flow).
 *
 * @see https://developer.kontur.ru/Docs/diadoc-api/authentication.html
 */
final readonly class DeviceFlowStart
{
    public function __construct(
        /**
         * Временный код подтверждения, который обменивается на access_token.
         */
        public string $deviceCode,
        /**
         * Код, который пользователь вводит на странице авторизации.
         */
        public string $userCode,
        /**
         * URI страницы ввода кода (если приложение не умеет открывать браузер).
         */
        public string $verificationUri,
        /**
         * URI с предзаполненным кодом — достаточно открыть в браузере.
         */
        public ?string $verificationUriComplete,
        /**
         * Периодичность опроса /connect/token в секундах.
         */
        public int $interval,
        /**
         * Время жизни device_code в секундах.
         */
        public int $expiresIn,
    ) {
    }
}
