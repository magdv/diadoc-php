<?php

namespace MagDv\Diadoc\Auth;

use MagDv\Diadoc\Exception\DiadocApiException;

/**
 * Контракт стратегии авторизации для DiadocApi.
 *
 * Каждая реализация инкапсулирует конкретный способ авторизации в API Диадока:
 *  - {@see AuthenticateV3AuthMode} — устаревший DiadocAuth (ddauth_api_client_id + ddauth_token);
 *  - {@see OidcAuthFlowAuthMode} — OpenID Connect (Authorization: Bearer + refresh).
 *
 * Благодаря этому DiadocApi не знает деталей авторизации, а новые способы
 * добавляются простой реализацией интерфейса.
 */
interface AuthModeInterface
{
    /**
     * Идентификатор режима (используется в тестах и для диагностики).
     */
    public function getMode(): string;

    /**
     * Заголовки авторизации для HTTP-запроса.
     *
     * @param string|null $contentType Content-Type для POST-запросов
     * @param string      $method      HTTP-метод (GET/POST)
     * @param bool        $forAuthenticateCall true — запрос к эндпоинту аутентификации (без токена)
     *
     * @return string[] список строк заголовков вида "Name: value"
     *
     * @throws DiadocApiException если нет токена и он обязателен
     */
    public function buildRequestHeaders(?string $contentType, string $method, bool $forAuthenticateCall): array;

    /**
     * Подготовка перед запросом: проверка наличия токена, проактивное обновление.
     *
     * @throws DiadocApiException если токен отсутствует и обязателен
     */
    public function ensureAuthorized(): void;

    /**
     * Обработка ответа 401 Unauthorized.
     *
     * @return bool true — запрос следует повторить (например, после refresh токена)
     */
    public function handleUnauthorized(): bool;

    /**
     * Установить токен (с учётом особенностей режима).
     */
    public function setToken(?string $token): void;

    /**
     * Получить текущий токен.
     */
    public function getToken(): ?string;
}
