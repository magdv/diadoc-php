<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

/**
 * Результат одного опроса POST /connect/token (grant_type=device_code).
 */
final readonly class DeviceFlowPollResult
{
    public function __construct(
        public DeviceFlowPollStatus $status,
        /**
         * Заполнено при DeviceFlowPollStatus::Success.
         */
        public ?string $accessToken = null,
        /**
         * Заполнено при DeviceFlowPollStatus::Success.
         */
        public ?string $refreshToken = null,
        /**
         * Время жизни access_token в секундах. Заполнено при Success.
         */
        public ?int $expiresIn = null,
        /**
         * Человекочитаемое описание ошибки. Заполнено при ошибочных статусах.
         */
        public ?string $errorDescription = null,
    ) {
    }

    public static function success(string $accessToken, ?string $refreshToken, ?int $expiresIn): self
    {
        return new self(
            status: DeviceFlowPollStatus::Success,
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            expiresIn: $expiresIn,
        );
    }

    public static function pending(): self
    {
        return new self(status: DeviceFlowPollStatus::AuthorizationPending);
    }

    public static function slowDown(): self
    {
        return new self(status: DeviceFlowPollStatus::SlowDown);
    }

    public static function failed(DeviceFlowPollStatus $status, ?string $errorDescription = null): self
    {
        return new self(status: $status, errorDescription: $errorDescription);
    }
}
