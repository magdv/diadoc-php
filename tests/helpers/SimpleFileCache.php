<?php

declare(strict_types=1);

namespace Test\helpers;

/**
 * Минимальная замена DOFileCache для тестов без dev-зависимостей.
 */
class SimpleFileCache
{
    private string $cacheDirectory = '';

    /**
     * @param array<string, mixed> $config
     */
    public function changeConfig(array $config): void
    {
        if (isset($config['cacheDirectory']) && is_string($config['cacheDirectory'])) {
            $this->cacheDirectory = rtrim($config['cacheDirectory'], DIRECTORY_SEPARATOR);
            if (!is_dir($this->cacheDirectory)) {
                mkdir($this->cacheDirectory, 0777, true);
            }
        }
    }

    /**
     * @return mixed|false
     */
    public function get(string $key)
    {
        $path = $this->pathForKey($key);
        if (!is_file($path)) {
            return false;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return false;
        }

        $data = unserialize($raw);
        if (!is_array($data) || !array_key_exists('exp', $data) || !array_key_exists('val', $data)) {
            return false;
        }

        if ($data['exp'] < time()) {
            @unlink($path);

            return false;
        }

        return $data['val'];
    }

    public function set(string $key, mixed $value, int $expiresAt): void
    {
        $path = $this->pathForKey($key);
        $payload = serialize(['exp' => $expiresAt, 'val' => $value]);
        file_put_contents($path, $payload);
    }

    private function pathForKey(string $key): string
    {
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . md5($key) . '.cache';
    }
}
