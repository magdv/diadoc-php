<?php

/**
 * Роутер для встроенного сервера PHP: локальный тест OAuth (Authorization Code).
 *
 * Запуск (обычно из Docker, см. docker-compose):
 *   php -S 0.0.0.0:8765 -t /app/docker/oauth-test /app/docker/oauth-test/router.php
 *
 * В Кабинете интегратора зарегистрируйте redirect URI — тот же, что в OAUTH_REDIRECT_URI
 * (по умолчанию http://127.0.0.1:8765/oauth/callback).
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli-server') {
    fwrite(STDERR, "Этот файл предназначен только для php -S (встроенный сервер).\n");
    exit(1);
}

$projectRoot = dirname(__DIR__, 2);
chdir($projectRoot);

$autoload = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (! is_file($autoload)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Нет vendor/autoload.php. Выполните: composer install\n";
    exit;
}

require_once $autoload;

use MagDv\Diadoc\Auth\OidcAuthMode;
use MagDv\Diadoc\DiadocApi;
use MagDv\Diadoc\Exception\DiadocApiException;

$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriPath = is_string($uriPath) ? $uriPath : '/';

if ($uriPath === '/favicon.ico') {
    http_response_code(404);
    exit;
}

$varDir = $projectRoot . DIRECTORY_SEPARATOR . 'var';
if (! is_dir($varDir)) {
    @mkdir($varDir, 0777, true);
}

$stateFile = $varDir . DIRECTORY_SEPARATOR . '.oauth_pending_state';
$redirectUri = getenv('OAUTH_REDIRECT_URI');
if ($redirectUri === false || trim($redirectUri) === '') {
    $redirectUri = 'http://127.0.0.1:8765/oauth/callback';
}

$redirectUri = trim($redirectUri);

$clientId = getenv('OAUTH_CLIENT_ID');
$clientSecret = getenv('OAUTH_CLIENT_SECRET');
$diadocUrl = getenv('DIADOC_URL');
$identityUrl = getenv('OAUTH_IDENTITY_URL');

if ($clientId === false || trim($clientId) === '' || $clientSecret === false || trim($clientSecret) === '') {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Ошибка конфигурации</h1><p>Задайте в .env переменные <code>OAUTH_CLIENT_ID</code> и <code>OAUTH_CLIENT_SECRET</code>.</p>';
    exit;
}

if ($diadocUrl === false || trim($diadocUrl) === '') {
    $diadocUrl = 'https://diadoc-api.kontur.ru/';
}

$identityBase = ($identityUrl !== false && trim($identityUrl) !== '') ? trim($identityUrl) : 'https://identity.kontur.ru';

$api = DiadocApi::create(
    new OidcAuthMode(trim($clientId), trim($clientSecret), $identityBase, trim($diadocUrl)),
    trim($diadocUrl)
);

if ($uriPath === '/' || $uriPath === '') {
    $state = bin2hex(random_bytes(16));
    file_put_contents($stateFile, $state);
    $authUrl = $api->buildAuthorizationUrl($redirectUri, $state);

    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Diadoc OAuth — тест</title></head><body>';
    echo '<h1>Локальный тест авторизации Диадок</h1>';
    echo '<p>1. В <a href="https://integrations.kontur.ru/" target="_blank" rel="noopener">Кабинете интегратора</a> в redirect URI должно быть <strong>точно</strong>:</p>';
    echo '<pre>' . htmlspecialchars($redirectUri, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    echo '<p>2. Перейдите по ссылке и войдите под пользователем Диадок:</p>';
    echo '<p><a href="' . htmlspecialchars($authUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">Войти через Контур (OpenID)</a></p>';
    echo '<p><small>Ожидаемый <code>state</code> сохранён в <code>var/.oauth_pending_state</code> для проверки на callback.</small></p>';
    echo '</body></html>';
    exit;
}

if ($uriPath === '/oauth/callback') {
    header('Content-Type: text/html; charset=utf-8');

    $error = isset($_GET['error']) ? (string) $_GET['error'] : '';
    if ($error !== '') {
        $desc = isset($_GET['error_description']) ? (string) $_GET['error_description'] : '';
        http_response_code(400);
        echo '<h1>Ошибка авторизации</h1><pre>' . htmlspecialchars($error . ' ' . $desc, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
        exit;
    }

    $code = isset($_GET['code']) ? (string) $_GET['code'] : '';
    $gotState = isset($_GET['state']) ? (string) $_GET['state'] : '';

    if ($code === '') {
        http_response_code(400);
        echo '<h1>Нет параметра code</h1>';
        exit;
    }

    $expectedState = is_file($stateFile) ? (string) file_get_contents($stateFile) : '';
    if ($expectedState === '' || ! hash_equals($expectedState, $gotState)) {
        http_response_code(400);
        echo '<h1>Неверный state</h1><p>Откройте сначала <a href="/">главную</a> этой страницы и снова пройдите вход.</p>';
        exit;
    }

    @unlink($stateFile);

    try {
        $api->exchangeAuthorizationCode($code, $redirectUri);
    } catch (DiadocApiException $e) {
        http_response_code(502);
        echo '<h1>Ошибка обмена code на токен</h1><pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
        exit;
    }

    $session = $api->getOAuthSessionState();
    $json = json_encode($session, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $humanPath = $varDir . DIRECTORY_SEPARATOR . 'oauth_last_session.json';
    file_put_contents($humanPath, (string) $json);

    // Формат как у Test\helpers\SimpleFileCache + ключ diadoc_oauth_session (см. ConfigNames::DIADOC_OAUTH_CACHE_KEY)
    $cacheKey = 'diadoc_oauth_session';
    $cachePath = $varDir . DIRECTORY_SEPARATOR . md5($cacheKey) . '.cache';
    $cacheExp = time() + 86400 * 30;
    if (! empty($session['expires_at'])) {
        $cacheExp = max($cacheExp, (int) $session['expires_at'] + 7200);
    }

    $payload = serialize(
        [
            'exp' => $cacheExp,
            'val' => json_encode($session, JSON_UNESCAPED_UNICODE),
        ]
    );
    file_put_contents($cachePath, $payload);

    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Успех</title></head><body>';
    echo '<h1>Токены получены</h1>';
    echo '<p>Записано в кеш тестов (как у <code>ApiClient</code>): <code>var/' . htmlspecialchars(basename($cachePath), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></p>';
    echo '<p>Читаемая копия: <code>var/oauth_last_session.json</code></p>';
    echo '<p>Можно запускать PHPUnit / скрипты, которые читают кеш из <code>var/</code>.</p>';
    echo '<p>Либо перенесите значения в <code>.env</code>: <code>DIADOC_ACCESS_TOKEN</code>, <code>DIADOC_REFRESH_TOKEN</code>, <code>DIADOC_ACCESS_EXPIRES_AT</code>.</p>';
    echo '<h2>Содержимое сессии (без полного access в лог)</h2><pre>';
    $safe = $session;
    if (isset($safe['access_token']) && is_string($safe['access_token']) && strlen($safe['access_token']) > 24) {
        $safe['access_token'] = substr($safe['access_token'], 0, 12) . '…' . substr($safe['access_token'], -8);
    }

    echo htmlspecialchars(json_encode($safe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo '</pre></body></html>';
    exit;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo sprintf('Неизвестный путь: %s%s', $uriPath, PHP_EOL);
